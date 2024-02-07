#include "server2.h"

//bool stopped = false;

ServerII::ServerII()
{

}

bool ServerII::start_multithread(int port)
{

    struct sockaddr_in sin;
    memset(&sin, 0, sizeof(sin));
    sin.sin_family = AF_INET;
    sin.sin_addr.s_addr = htonl(INADDR_ANY);
    sin.sin_port = htons(port);

    std::exception_ptr initException;

    evutil_socket_t listen_socket = -1;

    listen_socket = socket(AF_INET, SOCK_STREAM, 0);
    if (bind(listen_socket, (struct sockaddr *)&sin, sizeof(sin)) < 0) {
        cout << "[" << hlp.time() << "] bind failed\n" << errno << endl;
        return false;
    }

    if (setnonblock(listen_socket) < 0) {
        cout << "[" << hlp.time() << "] failed to set server socket to non-blocking\n";
    }

    bool volatile threadsRunning = true;

    int threads = hlp.valueInt("threads");
    if (threads == 0){
        threads = 5;
    }

    auto threadFunc = [&](){
        try {

            typedef unique_ptr<evconnlistener, decltype(&evconnlistener_free)> ListenerPtr;
            unique_ptr<event_base, decltype(&event_base_free)> base_ptr(event_base_new(), &event_base_free);
            if (!base_ptr){
                throw runtime_error("Can't create base event");
            }


            ListenerPtr listener_ptr(evconnlistener_new(base_ptr.get(), &ServerII::accept_control_listener, NULL, LEV_OPT_CLOSE_ON_FREE | LEV_OPT_REUSEABLE, -1, listen_socket), &evconnlistener_free);

            if (!listener_ptr){
                throw runtime_error("Can't create listener for socket");
            }

            while (threadsRunning){
                event_base_loop(base_ptr.get(), EVLOOP_NONBLOCK);
                this_thread::sleep_for(chrono::milliseconds(20));
            }

        } catch(...)
        {
            initException = current_exception();
        }
    };

    auto threadDeleter = [&](thread *t){
        threadsRunning = false;
        t->join();
        delete t;
    };

    typedef unique_ptr<std::thread, decltype(threadDeleter)> ThreadPtr;
    typedef vector<ThreadPtr> ThreadPool;

    ThreadPool threadPool;
    for (int i = 0; i < threads; i++){
        cout << "[" << hlp.time() << "] Starting thread #" << i << endl;
        ThreadPtr t(new thread(threadFunc), threadDeleter);
        this_thread::sleep_for(chrono::milliseconds(500));
        if (initException != exception_ptr()){
            cout << "[" << hlp.time() << "] Can't create thread\n";
            return false;
        }

        threadPool.push_back(move(t));
    }


    while (true){
        this_thread::sleep_for(chrono::milliseconds(500));
    }


    return true;

}

bool ServerII::start(int port){

    struct sockaddr_in sin;
    memset(&sin, 0, sizeof(sin));
    sin.sin_family = AF_INET;
    sin.sin_addr.s_addr = htonl(INADDR_ANY);
    sin.sin_port = htons(port);

    struct event_base *base = event_base_new();
    if (!base){
        return false;
    }

    evconnlistener *listener = evconnlistener_new_bind(base, &ServerII::accept_control_listener, NULL, LEV_OPT_CLOSE_ON_FREE | LEV_OPT_REUSEABLE, -1, (struct sockaddr*) &sin, sizeof(sin));

    if (!listener){
        return false;
    }

    int worker_threads = hlp.valueInt("threads");
    if (worker_threads == 0){
        worker_threads = 20;
    }
    cout << "[" << hlp.time() << "] Will start " << worker_threads << " worker threads\n";

    vector<thread> threadPool;

    for (int i=0; i<worker_threads; i++){
        thread t = thread(&ServerII::tunnel_worker, i);
        threadPool.push_back(move(t));
    }

    event_base_dispatch(base);

    hlp.pushNextBuffers3(0, 0);

    for (thread &t: threadPool){
        t.join();
    }

    evconnlistener_free(listener);
    event_base_free(base);
    return true;

}

int ServerII::setnonblock(int fd)
{
   int flags;

   flags = fcntl(fd, F_GETFL);
   if (flags < 0) return flags;
   flags |= O_NONBLOCK;
   if (fcntl(fd, F_SETFL, flags) < 0) return -1;
   return 0;
}

void ServerII::bufev_eof_cb(bufferevent *buf_ev, short events, void *ptr)
{

//    UNUSED(ptr);
    control_client *state = static_cast<control_client*>(ptr);
    if ((events & BEV_EVENT_EOF) || (events & BEV_EVENT_ERROR)){

//        string disconnected_address = ServerII::get_buffer_address(buf_ev);
//        auto now = chrono::system_clock::to_time_t(chrono::system_clock::now());
        stringstream ss;

//        ss << "[" << put_time(localtime(&now), "%F %T") <<  "] disconnected client " << state->cHash << endl;
        ss << "[" << hlp.time() <<  "] disconnected client " << state->cHash << endl;

        cout << ss.str();
        bufferevent_free(buf_ev);

        evconnlistener *l = hlp.listenerForBuffer(buf_ev);
        if (l != 0){

            evconnlistener_free(l);
            hlp.setListenerForBuffer(buf_ev, 0);
        }

        hlp.updateDB(state->cHash, Hlp::DB_DISCONNECTED);

    }
}

void ServerII::control_read_cb(bufferevent *buf_ev, void *arg)
{

    control_client *state = (control_client*) arg;

    struct event_base *base = bufferevent_get_base(buf_ev);

    struct evbuffer *buf_input = bufferevent_get_input( buf_ev );
    struct evbuffer *buf_output = bufferevent_get_output( buf_ev );

    size_t len = evbuffer_get_length(buf_input);
    if (len == 0){
        bufferevent_free(buf_ev);

        evconnlistener *l = hlp.listenerForBuffer(buf_ev);
        if (l != 0){
            evconnlistener_free(l);
            hlp.setListenerForBuffer(buf_ev, 0);
        }

    }

    uint8_t *data;
    data = (uint8_t*) malloc(len);

    evbuffer_copyout(buf_input, data, len);
    evbuffer_drain(buf_input, len);

    if (data[0] != 0x5){
        bufferevent_free(buf_ev);
        return;
    }

    switch (state->status){

    case CLIENT_TRANSFER:
        break;

    case CLIENT_DISCONNECT:
        bufferevent_free(buf_ev);
        return;

    case CLIENT_CONNECT:

//        +----+-----+-------+------+----------+----------+
//        |VER | CMD |  RSV  | ATYP | DST.ADDR | DST.PORT |
//        +----+-----+-------+------+----------+----------+
//        | 1  |  1  | X'00' |  1   | Variable |    2     |
//        +----+-----+-------+------+----------+----------+

        if (data[1] != 0x02 || data[2] != 0x00) {
            uint8_t reply[3] = {0x05, 0x07, 0x00};
            evbuffer_add(buf_output, (void *) reply, sizeof(reply));

            state->status = CLIENT_DISCONNECT;
            return;
        }

        switch (data[3]){
        case 0x01:
            break;//TODO: bind by address
        case 0x03:

            uint8_t addrLen = data[4];
            if ((uint8_t) len != addrLen + 7){
                uint8_t reply[3] = {0x05, 0x01, 0x00};
                evbuffer_add(buf_output, (void *) reply, sizeof(reply));
                state->status = CLIENT_DISCONNECT;
                return;

            }

//            TODO: return real server address
//            char *addr;
//            addr = (char*) malloc(addrLen);
//            addr = (char *) (data + 5);

            int portFromConfig = hlp.getPort(state->cHash);

            struct sockaddr_in sin;

            memset(&sin, 0, sizeof(sin));
            sin.sin_family = AF_INET;
            sin.sin_addr.s_addr = htonl(INADDR_ANY);
            sin.sin_port = htons(portFromConfig);

            evconnlistener *remote_listener = evconnlistener_new_bind(base, &ServerII::accept_socks_listener, (void*) buf_ev, LEV_OPT_CLOSE_ON_FREE | LEV_OPT_REUSEABLE, -1, (sockaddr*) &sin, sizeof(sin));

            if (!remote_listener){
                bufferevent_free(buf_ev);
                return;
            }

            hlp.setListenerForBuffer(buf_ev, remote_listener);

            int sockfd = evconnlistener_get_fd(remote_listener);

            int yes = 1;
            socklen_t sz = sizeof(yes);
            setsockopt(sockfd, SOL_SOCKET, SO_KEEPALIVE, &yes, sz);
            struct timeval timeout;
            timeout.tv_sec = 10;
            timeout.tv_usec = 0;
            setsockopt(sockfd, SOL_SOCKET, SO_RCVTIMEO, &timeout, sizeof(timeout));
            setsockopt(sockfd, SOL_SOCKET, SO_SNDTIMEO, &timeout, sizeof(timeout));


            unsigned short socks_port = portFromConfig;

            if (socks_port == 0){


                struct sockaddr_in sin2;

                memset(&sin2, 0, sizeof(sin2));
                int socklen = sizeof(sin2);

                getsockname(sockfd, (sockaddr *) &sin2, (socklen_t *) &socklen);

                socks_port = ntohs(sin2.sin_port);
//                socks_port = htons(sin2.sin_port);

                hlp.setPort(state->cHash, socks_port);
            }

            hlp.updateDB(state->cHash, Hlp::DB_CONNECTED, socks_port);

            string connected_address = ServerII::get_buffer_address(buf_ev);

//            auto now = chrono::system_clock::to_time_t(chrono::system_clock::now());
            stringstream log;
            log << "[" << hlp.time() << "] connected client " << state->cHash << " from address " << connected_address << endl;

            cout << log.str();

            uint8_t reply[16] = {0x05, 0x00, 0x00, 0x03, 0x09, '1', '2', '7', '.', '0', '.', '0', '.', '1', ((uint8_t*) (&socks_port))[0], ((uint8_t*) (&socks_port))[1]};
            state->status = CLIENT_TRANSFER;
            evbuffer_add(buf_output, (void *) reply, sizeof(reply));
            break;
        }

        break;

    case CLIENT_AUTH: {


//        +----+------+----------+
//        |VER | HLEN |  HASH    |
//        +----+------+----------+
//        | 1  |  1   | 1 to 255 |
//        +----+------+----------+


        uint8_t reply[2] = {0x05, 0xff};
        if (data[0] != 0x05){
            evbuffer_add(buf_output, (void *) reply, sizeof(reply));
            bufferevent_free(buf_ev);
            return;
        }

        uint8_t hashSize = data[1];

        if ((uint8_t) len != hashSize+2){
            evbuffer_add(buf_output, (void *) reply, sizeof(reply));
            bufferevent_free(buf_ev);
            return;
        }

        char userHash[hashSize];
        memcpy(userHash, data+2, hashSize);

        reply[1] = 0x00;
        evbuffer_add(buf_output, (void *) reply, sizeof(reply));
        state->status = CLIENT_CONNECT;
        state->cHash = string(userHash, hashSize);

        break;
    }


    case CLIENT_NOOP:

//        +----+----------+----------+
//        |VER | NMETHODS | METHODS  |
//        +----+----------+----------+
//        | 1  |    1     | 1 to 255 |
//        +----+----------+----------+

        int nmethods = (int) data[1];
        if ((uint8_t) len < (nmethods + 2)) {
            bufferevent_free(buf_ev);
            return;
        }

        uint8_t response[2] = {0x05, 0xff};

        for (int i = 2; i < nmethods + 2; i++) {
            // 0x81 - first reserved for private methods
            if (data[i] == 0x81) {
                response[1] = (uint8_t) data[i];
                break;
            }
        }

        state->status = CLIENT_AUTH;
        evbuffer_add(buf_output, (void *) response, sizeof(response));

        if (response[1] == 0xff){
            bufferevent_free(buf_ev);
            return;
        }
        break;

    }

}

void ServerII::remote_local_read_cb_(bufferevent *buf_in, void *arg)
{
//    cout << this_thread::get_id() << endl;
    bufferevent *buf_out = (bufferevent*) arg;

    struct evbuffer *buf_input = bufferevent_get_input( buf_in );
    struct evbuffer *buf_output = bufferevent_get_output( buf_out );

    int res = evbuffer_add_buffer(buf_output, buf_input);
    if (res == -1){
        bufferevent_free(buf_in);
        bufferevent_free(buf_out);
    }

}

void ServerII::r_l_threaded(bufferevent *b_in, bufferevent *b_out)
{
//    cout << this_thread::get_id() << endl;
    struct evbuffer *buf_input = bufferevent_get_input( b_in );
    struct evbuffer *buf_output = bufferevent_get_output( b_out );

    int res = evbuffer_add_buffer(buf_output, buf_input);
    if (res == -1){
        bufferevent_free(b_in);
        bufferevent_free(b_out);
    }

}

//TODO: create threadpool with queue<pair<bufferevent*, bufferevent*>> and conditional_variable

void ServerII::remote_local_read_cb(bufferevent *buf_in, void *arg)
{
    bufferevent *buf_out = (bufferevent*) arg;

//    hlp.pushNextBuffers(buf_in, buf_out);

    evutil_socket_t fd = bufferevent_getfd(buf_out);
    struct evbuffer *buf_input = bufferevent_get_input( buf_in );
    size_t len = evbuffer_get_length(buf_input);
    if (len == 0){
        bufferevent_free(buf_in);
        bufferevent_free(buf_out);
    }

    uint8_t *data;
    data = (uint8_t*) malloc(len);
    evbuffer_copyout(buf_input, data, len);
    evbuffer_drain(buf_input, len);

    uint8_t *buffer;
    buffer = (uint8_t*) malloc(len+4);

    buffer[0] = (len >> 24) & 0xff;
    buffer[1] = (len >> 16) & 0xff;
    buffer[2] = (len >> 8) & 0xff;
    buffer[3] = len & 0xff;

    memcpy(&buffer[4], data, len);

    free(data);

//    std::shared_ptr<char> p_buffer(buffer);

    hlp.pushNextBuffers3(fd, buffer);

}

void ServerII::accept_socks_listener(evconnlistener *listener, int fd, sockaddr *addr, int sock_len, void *arg)
{
    UNUSED(addr);
    UNUSED(sock_len);
    struct event_base *base = evconnlistener_get_base(listener);
    struct bufferevent * remote_bev = bufferevent_socket_new(base, fd, BEV_OPT_CLOSE_ON_FREE | BEV_OPT_DEFER_CALLBACKS);
//    struct bufferevent * remote_bev = bufferevent_socket_new(base, fd, BEV_OPT_CLOSE_ON_FREE );

    if (!remote_bev){
        evconnlistener_free(listener);
        return;
    }

    setnonblock(fd);

    int yes = 1;
    socklen_t sz = sizeof(yes);

    setsockopt(fd, SOL_SOCKET, SO_KEEPALIVE, &yes, sz);

    struct bufferevent *control_bev = (bufferevent*) arg;

    struct sockaddr_in sin;
    memset(&sin, 0, sizeof(sin));
    sin.sin_family = AF_INET;
    sin.sin_addr.s_addr = htonl(INADDR_ANY);
    sin.sin_port = htons(0);


    evconnlistener *local_listener = evconnlistener_new_bind(base, &ServerII::accept_local_listener, (void*) remote_bev, LEV_OPT_CLOSE_ON_FREE, -1, (sockaddr*) &sin, sizeof(sin) );

    if (!local_listener){
        bufferevent_free(remote_bev);
        evconnlistener_free(listener);
        return;
    }

    int sockfd = evconnlistener_get_fd(local_listener);

    struct sockaddr_in sin2;

    memset(&sin2, 0, sizeof(sin2));
    int socklen = sizeof(sin2);

    getsockname(sockfd, (sockaddr *) &sin2, (socklen_t *) &socklen);

    unsigned short socks_port = htons(sin2.sin_port);

    uint8_t reply[16] = {0x05, 0x00, 0x00, 0x03, 0x09, '1', '2', '7', '.', '0', '.', '0', '.', '1', ((uint8_t*) (&socks_port))[0], ((uint8_t*) (&socks_port))[1]};

    setnonblock(sockfd);

    evbuffer_add(bufferevent_get_output( control_bev ), (void *) reply, sizeof(reply));


//    cout << "accepted socks\n start local on " << socks_port << endl;
}

void ServerII::accept_local_listener(evconnlistener *listener_transfer, int sock, sockaddr *address, int socket_len, void *arg)
{
    (void) address;
    (void) socket_len;

    struct event_base *b = evconnlistener_get_base(listener_transfer);
    struct bufferevent *local_bev = bufferevent_socket_new(b, sock, BEV_OPT_CLOSE_ON_FREE | BEV_OPT_DEFER_CALLBACKS);
//    struct bufferevent *local_bev = bufferevent_socket_new(b, sock, BEV_OPT_CLOSE_ON_FREE);

    if (!local_bev){
    evconnlistener_free(listener_transfer);
        return;
    }

    setnonblock(sock);

    int yes = 1;
    socklen_t sz = sizeof(yes);

    setsockopt(sock, SOL_SOCKET, SO_KEEPALIVE, &yes, sz);
    timeval tv;
    tv.tv_sec = 15;
    tv.tv_usec = 0;
    setsockopt(sock, SOL_SOCKET, SO_RCVTIMEO, &tv, sizeof(tv));
    setsockopt(sock, SOL_SOCKET, SO_SNDTIMEO, &tv, sizeof(tv));

    bufferevent *remote_bev = (bufferevent*) arg;

    auto buffer_disconnector = [](bufferevent *buf_ev, short events, void *ptr){
        if (events & BEV_EVENT_EOF){
            bufferevent_free(buf_ev);
            bufferevent_free((bufferevent*) ptr);
        }
    };

//    sockaddr_in *saddr = (sockaddr_in*) address;

//    int port = saddr->sin_port;

    bufferevent_setcb(remote_bev, &ServerII::remote_local_read_cb, NULL, buffer_disconnector, (void*) local_bev);
    bufferevent_enable(remote_bev, EV_READ | EV_TIMEOUT);

    bufferevent_setcb(local_bev, &ServerII::remote_local_read_cb, NULL, buffer_disconnector, (void*) remote_bev);
    bufferevent_enable(local_bev, EV_READ | EV_TIMEOUT);

//    cout << "accepted local port " << port << endl;

    evconnlistener_free(listener_transfer);
}

void ServerII::accept_control_listener(evconnlistener *listener, int fd, sockaddr *addr, int sock_len, void *arg)
{
    UNUSED(addr);
    UNUSED(sock_len);
    UNUSED(arg);

    struct event_base *base = evconnlistener_get_base(listener);
    struct bufferevent * buf_ev = bufferevent_socket_new(base, fd, BEV_OPT_CLOSE_ON_FREE);

    if (!buf_ev){
        return;
    }

    setnonblock(fd);

    int yes = 1;
    socklen_t sz = sizeof(yes);

    setsockopt(fd, SOL_SOCKET, SO_KEEPALIVE, &yes, sz);

    control_client *state = new control_client;
    state->status = CLIENT_NOOP;
    state->cHash = "";
    state->socksListener = 0;

    bufferevent_setcb(buf_ev, &ServerII::control_read_cb, NULL, &ServerII::bufev_eof_cb, (void*) state);
//    bufferevent_enable(buf_ev, EV_READ | EV_PERSIST);
    bufferevent_enable(buf_ev, EV_READ);

}

string ServerII::get_buffer_address(bufferevent *buf)
{
    evutil_socket_t client_sock = bufferevent_getfd(buf);
    return ServerII::get_fd_address(client_sock);
}

string ServerII::get_fd_address(int fd)
{
    struct sockaddr_in sin;

    memset(&sin, 0, sizeof(sin));
    int socklen = sizeof(sin);

    getsockname(fd, (sockaddr *) &sin, (socklen_t *) &socklen);

    uint32_t addr = ntohl(sin.sin_addr.s_addr);

    string ip_address = "";
    ip_address += to_string((addr & 0xff000000) >> 24) + ".";
    ip_address += to_string((addr & 0x00ff0000) >> 16) + ".";
    ip_address += to_string((addr & 0x0000ff00) >> 8) + ".";
    ip_address += to_string(addr & 0x000000ff);

    return ip_address;

}

void ServerII::tunnel_worker(int tid)
{
//    stringstream ss;
//    ss << "listener #" << tid << " started\n";
//    cout << ss.str();

    while (true){

        this_thread::sleep_for(chrono::milliseconds(20+tid));


        pair<evutil_socket_t, uint8_t*> buffers = hlp.getNextBuffers3();
        if (buffers.first == 0 && buffers.second == 0){
//            return;
            continue;
        }

        evutil_socket_t fd = buffers.first;
        uint8_t *buffer = buffers.second;
        int len = (buffer[0] << 24) | (buffer[1] << 16) | (buffer[2] << 8) | buffer[3];

//        cout << "data length " << len << "\n";

        uint8_t *data;
        data = (uint8_t*) malloc(len);
        memcpy(data, buffer+4, len);

        free(buffer);
/*
        if (data[0] == 0x05 && data[1] == 0x01 && data[2] == 0x00){
            cout << "socks connect request\n";
        }

        if (data[0] == 0x05 && data[1] == 0x00 && len == 2){
            cout << "socks connect response\n";
        }
        string s((char*)data, len);

        if (s.find("\nHost: ") != string::npos){
            cout << "HTTP connect requested\n";
        }
*/
        int sent = 0;
        int eagain_counts = 0;

        while (true) {
            int res = send(fd, &data[sent], len-sent, MSG_NOSIGNAL);
            if (res == -1){

                int err = errno;

                if (err == EAGAIN){
                    eagain_counts++;
                    if (eagain_counts == 100){
                        close(fd);
                        break;
                    }
                    cout << "[" << hlp.time() << "] send error: EAGAIN" << endl;
                    this_thread::sleep_for(chrono::milliseconds(200));
                    continue;
                }

                if (err == ESPIPE || err == EPIPE || err == EBADF || err == ECONNRESET){
                    close(fd);
                }
                cout << "[" << hlp.time() << "] send error: " << errno << endl;
                break;
            }
            if (res == 0){
                break;
            }

            sent += res;
//            if (res + sent == len){

            if (sent == len){
                break;
            }
        }

        /*
        pair<bufferevent*, bufferevent*> buffers = hlp.getNextBuffers();
        bufferevent *b_in = buffers.first;
        bufferevent * b_out = buffers.second;
        struct evbuffer *buf_input = bufferevent_get_input( b_in );
        struct evbuffer *buf_output = bufferevent_get_output( b_out );

        size_t len = evbuffer_get_length(buf_input);
        if (len == 0){
//            evbuffer_unlock(buf_input);
//            evbuffer_unlock(buf_output);
            continue;
        }

        stringstream ss;
//        ss << "thread #" << this_thread::get_id() << " got data\n";
//        cout << ss.str();


//        mutex m;
//        unique_lock<mutex> lock(m);

        evbuffer_enable_locking(buf_input, NULL);
        evbuffer_enable_locking(buf_output, NULL);
        evbuffer_lock(buf_input);
        evbuffer_lock(buf_output);

//        uint8_t *data;
//        data = (uint8_t*) malloc(len);
//        evbuffer_copyout(buf_input, data, len);

//        string str_data = string((char*) data, len);

//        str_data = ServerII::normalize_string(str_data);

//        ss = stringstream();

//        ss << "==============\n" << str_data << "==============\n";

//        cout << ss.str();

//        ss = stringstream();


//        ss << "got " << len << " bytes\n";
//        cout << ss.str();




        int res = evbuffer_add_buffer(buf_output, buf_input);
        if (res == -1){
            bufferevent_free(b_in);
            bufferevent_free(b_out);
        }
        evbuffer_unlock(buf_input);
        evbuffer_unlock(buf_output);
        */
    }

}

string ServerII::normalize_string(string str)
{
    string res = "";
    for (char &c: str){
        if (c == ' '){
            res += c;
        }
        else if ( c >= 48 && c <= 122){
            res += c;
        }
        else
        {
            stringstream ss;
            ss << "\\x" << std::setfill ('0') << std::setw(2) << std::hex << (int) c;
            res += ss.str();

        }
    }

    return res;
}

//tcp.port == 5555 || ip.addr == 192.168.0.100 || ip.proto == TCP
