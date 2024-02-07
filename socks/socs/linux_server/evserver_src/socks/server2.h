#ifndef SERVER2_H
#define SERVER2_H

//#undef put_time
#include <event2/listener.h>
#include <event2/bufferevent.h>
#include <event2/buffer.h>
//#include "libevent/include/event2/listener.h"
//#include "libevent/include/event2/bufferevent.h"
//#include "libevent/include/event2/buffer.h"
#include <cstring>
//#include <bits/unique_ptr.h>
#include <event.h>
#include <fcntl.h>
#include <unistd.h>
#include <utility>
#include <chrono>
#include <ctime>
#include <cstring>
#include <iomanip>
#include "config.h"

#include <iostream>

#include <thread>
#include <vector>
#include <condition_variable>
#include <queue>

//#include <asm/errno.h>
#include <errno.h>

using namespace std;


#define UNUSED(x) (void)(x)



//mutex m_transfer;
//condition_variable cv;
//bool stopped;

/**
 * @brief modified backconnect socks 5 server
 * used channels:
 *  `control` - between server and phone
 *  `remote` - beetwen server and remote client that has connected to this server
 *  `local` - between server and phone using for bridge between remote client and phone
 */
class ServerII
{

    /**
     * @brief State list for control channel
     */
    enum client_state {
        CLIENT_NOOP = 0,
        CLIENT_CONNECT = 1,
//        CLIENT_BIND = 2,
        CLIENT_AUTH = 3,
        CLIENT_TRANSFER = 4,
        CLIENT_DISCONNECT = 5,
    };


    /**
     * @brief struct to share in control channel callbacks
     */
    struct control_client {
        client_state status;
        string cHash;
        evconnlistener *socksListener;
    };

public:
    ServerII();

    /**
     * @brief Start server in multithread mode
     * @param port - port number to listen phone connections
     * @return true if server start and works, otherwise false
     */

    bool start_multithread(int port);

    /**
     * @brief Start server
     * @param port - port number to listen phone connections
     * @return true if server start and works, otherwise false
     */
    bool start(int port);

private:

    /**
     * @brief Set nonblocking option to socket
     * @param fd - socket id to set nonblock
     * @return
     */
    static int setnonblock(int fd);

    /**
     * @brief Check if event is disconnect and close bufferevent
     * @param buf_ev
     * @param events
     * @param ptr
     */
    static void bufev_eof_cb(bufferevent *buf_ev, short events, void *ptr);

    /**
     * @brief eventbase callback for read event in control connection
     * @param buf_ev - bufferevent
     * @param arg - state of event `client_state`
     */
    static void control_read_cb( struct bufferevent *buf_ev, void *arg );

    /**
     * @brief eventbase callback for local and remote ev.
     * main data transfer is here
     * @param buf_ev
     * @param arg
     */
    static void remote_local_read_cb(struct bufferevent *buf_in, void *arg);

    static void remote_local_read_cb_(struct bufferevent *buf_in, void *arg);

    static void r_l_threaded(bufferevent *b_in, bufferevent *b_out);

    /**
     * @brief accepts listener for remote chennel and create new local channel
     * @param listener_transfer
     * @param sock
     * @param address
     * @param socket_len
     * @param bev_pair
     */
    static void accept_socks_listener(evconnlistener *listener, int fd, sockaddr *addr, int sock_len, void *arg);

    /**
     * @brief accept local listener and create events to transfer
     * @param listener_transfer
     * @param sock
     * @param address
     * @param socket_len
     * @param bev_pair
     */
    static void accept_local_listener(evconnlistener *listener_transfer, int sock, sockaddr *address, int socket_len, void *bev_pair);

    /**
     * @brief accept first listener to phones
     * @param listener
     * @param fd
     * @param addr
     * @param sock_len
     * @param arg
     */
    static void accept_control_listener(struct evconnlistener *listener, int fd, struct sockaddr *addr, int sock_len, void* arg);

    /**
     * @brief return ip address from bufferevent object
     * @param buf - bufferevent object
     * @return
     */
    static string get_buffer_address(bufferevent *buf);

    /**
     * @brief get ip address from socket
     * @param fd - socket
     * @return
     */
    static string get_fd_address(int fd);

    /**
     * @brief worker for thread which makes tunnel between remote and local channels
     * @param tid
     */
    static void tunnel_worker(int tid);

    static string normalize_string(string str);

};



#endif
