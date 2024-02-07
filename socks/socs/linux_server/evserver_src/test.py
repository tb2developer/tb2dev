#!/usr/bin/python3

import socket
import struct
import time
from threading import Thread

def socks_thread(port):
	s = socket.socket()
	s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
	s.connect(('127.0.0.1', port))


	# while 1:
	data = s.recv(512)
	s.send(data + b'111')

	time.sleep(10)

	s.close()	



def socks_connect(port):
	s = socket.socket()
	s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
	s.connect(('127.0.0.1', port))

	data = b'asd'
	print('socks >>> ', data)
	s.send(data)
	data = s.recv(512)
	print('socks <<< ', data)
	s.close()	

addr = '127.0.0.1'

s = socket.socket()
s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
s.connect(('127.0.0.1', 5555))

# hello
print(b'>> \x05\x02\x01\x81')
s.send(b'\x05\x02\x01\x81')
a = s.recv(256)
print('<< ', a)
# auth

user_hash = b'98d9987f93b6edeede60bf7892f72428'
send_data = struct.pack('cB', b'\x05', len(user_hash)) + user_hash
print(b'>> ', send_data)
s.send(send_data)
a = s.recv(256)
print('<< ', a)

# bind
print(b'>> \x05\x02\x00\x03\x09127.0.0.1\x50\x00')
s.send(b'\x05\x02\x00\x03\x09127.0.0.1\x50\x00')
a = s.recv(256)
print('<< ', a)

print(a[-2:])

st = struct.unpack('>H', a[-2:])

p = st[0]
p = socket.ntohs(p)
# print(socket.ntohs(p))
print(p)

t2 = Thread(target=socks_connect, args=(int(p),))
t2.start()

# connected client
a = s.recv(256)
print('<< ', a)

st = struct.unpack('>H', a[-2:])

p = st[0]
p = socket.ntohs(p)
print(p)

t = Thread(target=socks_thread, args=(int(p),))
t.start()



