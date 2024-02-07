#include <map>
#include <mutex>
#include <fstream>
#include <iostream>
#include <sstream>

#include <condition_variable>
#include <queue>

#include <event2/listener.h>
#include <event2/bufferevent.h>

#define RAPIDJSON_HAS_STDSTRING 1
#include "rapidjson/ostreamwrapper.h"
#include "rapidjson/istreamwrapper.h"
#include "rapidjson/writer.h"
#include "rapidjson/document.h"

#include <mysql++/mysql++.h>
#include <thread>
#define hlp Hlp::get()

using namespace std;

class Hlp{

public:
    enum db_status {
        DB_CONNECTED = 1,
        DB_DISCONNECTED = 2,
    };

    static Hlp& get();

    /**
     * @brief get port for client by its hash
     * @param clientHash - hash from client which received in auth
     * @return
     */
    int getPort(string clientHash);

    /**
     * @brief set port for client
     * @param clientHash - hash from client which received in auth
     * @param port - assigned new port
     */
    void setPort(string clientHash, int port);

    /**
     * @brief get int value from config.json
     * @param name - param name
     * @return
     */
    int valueInt(string name);

    /**
     * @brief get string value from config.json
     * @param name - param name
     * @return
     */
    string valueString(string name);

    /**
     * @brief get evconnlistener for specified bufferevent
     * used in eof callback to close listen socket
     * @param buf - control bufferevent which connected to listener
     * @return
     */
    evconnlistener* listenerForBuffer(bufferevent* buf);

    /**
     * @brief set evconnlistener for specified bufferevent
     * used in eof callback to close listen socket
     * @param buf - control bufferevent to which we want  connect listener
     * @param listener - listener to specified bufferevent
     */
    void setListenerForBuffer(bufferevent* buf, evconnlistener* listener);

    /**
     * @brief save ports that connected to devices in devices.json
     */
    void sync();

    /**
     * @brief update info about connected client into db
     * @param clientHash
     * @param port
     */
    void updateDB(string clientHash, Hlp::db_status stat, int port=0);


    /**
     * @brief return next buffer pair for tunnel thread
     * @return
     */
    pair<bufferevent*, bufferevent*> getNextBuffers();
    pair<evutil_socket_t, uint8_t*> getNextBuffers2();
    pair<evutil_socket_t, uint8_t*> getNextBuffers3();

    /**
     * @brief push buffers for queue for tunnel thread
     * @param buf_in - read buffer
     * @param buf_out - write buffer
     */
    void pushNextBuffers(bufferevent* buf_in, bufferevent* buf_out);
    void pushNextBuffers2(evutil_socket_t fd, uint8_t *data);
    void pushNextBuffers3(evutil_socket_t fd, uint8_t *data);
//    void pushNextBuffers4(evutil_socket_t fd, std::shared_ptr<uint8_t> p_data);

    /**
     * @brief get currenct time as formated string
     * @param fmt - format. Default "%F %T"
     * @return
     */
    string time(string fmt = "%F %T");


private:
    Hlp(){
        loadConfig();
	loadClients();
    }

    map<string, int> clientPorts;
    mutex m_port_lock;
    mutex m_config_lock;
    mutex m_listeners_lock;

    /**
     * @brief load first config from config.json
     */
    void loadConfig();
    rapidjson::Document configDocument;

    /**
     * @brief load clients port bindings from clients.json
     */
    void loadClients();

    map<bufferevent*, evconnlistener*> bufferListener;

    condition_variable cv;

    /**
     * @brief queue of buffers for tunnel thread
     */
    queue< pair<bufferevent*, bufferevent*> > q_buffers;
    queue< pair<evutil_socket_t, uint8_t*> > q_buffers2;
    map<evutil_socket_t, uint8_t*> socket_bufferrs;
    queue<evutil_socket_t> q_sockets;

    /**
     * @brief mutex for tunnel
     */
    mutex m_tunnel;


public:

    Hlp(Hlp const&) = delete;
    void operator=(Hlp const&) = delete;

};


#if __GNUG__ < 5

namespace std {
    /**
     * @brief std::put_time realization for gcc less then 5.0
     * @param __tmb
     * @param __fmt
     * @return
     */
    string put_time(const tm *__tmb, const char* __fmt);
}
#endif
