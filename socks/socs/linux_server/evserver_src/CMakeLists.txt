cmake_minimum_required(VERSION 2.8)
project(evserver)

#set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11 -Wl,-rpath,lib -O0 -Wall -g")
set(CMAKE_CXX_FLAGS "${CMAKE_CXX_FLAGS} -std=c++11 -Wl,-rpath,lib -Wall -O0 -g -I/usr/include/mysql")

INCLUDE_DIRECTORIES(3rdparty)

find_library(libevent NAMES libevent.a libevent.so)

if (NOT libevent)
    MESSAGE(SEND_ERROR "libevent not found")
    return()
endif()

find_library(libmysqlpp NAMES libmysqlpp.a libmysqlpp.so)

if (NOT libmysqlpp)
    MESSAGE(SEND_ERROR "libmysql++ not found")
    return()
endif()

MESSAGE(${libevent})
MESSAGE(${libmysqlpp})

set(${CMAKE_VERBOSE_MAKEFILE} TRUE)

set(SOURCE_FILES main.cpp socks/server2.cpp socks/config.cpp)
#set(SOURCE_FILES thread.cpp socks/server2.cpp socks/config.cpp)
add_executable(evserver ${SOURCE_FILES})
target_link_libraries(evserver ${libevent} ${libmysqlpp} -pthread)
