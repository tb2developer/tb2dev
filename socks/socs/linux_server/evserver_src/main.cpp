#include <iostream>
#include <fstream>
#include <thread>
#include <chrono>
#include <csignal>
#include "socks/server2.h"

using namespace std;

void sigHandler(int sig){
    std::cout << "[" << hlp.time() << "] stopping process with signal " << sig << std::endl;

    if (sig == SIGSEGV) {
        cout << "[" << hlp.time() << "] segmentation fault signal received, restarting server\n";
    }

    exit(0);

}

int main() {

    string logfile = hlp.valueString("logfile");
    if (logfile.size()){
        ofstream stdout(logfile);
        std::cout.rdbuf(stdout.rdbuf());
    }

    cout << "[" << hlp.time() << "] Starting server\n";

    auto syncer = [](){
        while (true){
            this_thread::sleep_for(std::chrono::minutes(1));
            hlp.sync();
        }
    };
    thread t(syncer);

    std::atexit([](){
        std::cout << "[" << hlp.time() << "] saving to database all clients as disconnected\n";

        mysqlpp::Connection con( false );

        string mysql_login = hlp.valueString("mysql_user");
        string mysql_password = hlp.valueString("mysql_password");
        string mysql_db = hlp.valueString("mysql_db");
        string server = hlp.valueString("mysql_server");
        if (!server.length()){
            server = "localhost";
        }

        if (con.connect( mysql_db.c_str(), server.c_str(), mysql_login.c_str(), mysql_password.c_str(), 3306 )){

            vector<string> sqls;
            sqls.push_back("update `socks` set `is_online` = 0, `status` = 'offline';");
//            sqls.push_back("update `socks_log` set `message` = 'disconnected from server because server has stopped', `event` = 'DISCONNECTED' where `id` in (select `id` from )");

            for (auto &sql: sqls){
                mysqlpp::Query query = con.query(sql);
                if (!query.execute()){
                    cout << "[" << hlp.time() << "] " << query.error() << endl;
                    break;
                }
            }
        }

    });

    std::signal(SIGINT, &sigHandler);
    std::signal(SIGABRT, &sigHandler);
    std::signal(SIGSEGV, &sigHandler);

//    char *a = (char*) malloc(25);
//    evconnlistener_free((evconnlistener*) a);

    ServerII s;
    int port = hlp.valueInt("port");
    if (!port){
        port = 5555;
    }
    s.start(port);
//    s.start_multithread(port);

    return 0;
}

