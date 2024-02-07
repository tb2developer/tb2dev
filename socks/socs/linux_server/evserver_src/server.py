#!/usr/bin/python3

import socket
import struct
import time
import select
from threading import Thread

class SocksServer:

	def __init__(self, host, port=None):
		if port is not None:
			self.port = port

		else:
			self.port = 5555

		self.host = host
		self.phoneHash = b'2cc441d2ff4c5c7f23e2fd650ea05484'

	def start(self):

		s = socket.socket()
		s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
		s.connect((self.host, self.port))

		try:
			print('>> ', b'\x05\x01\x81')
			s.send(b'\x05\x01\x81')
			data = s.recv(256)
			print('<< ', data)
			print(data[1])

			if data[1] != 0x81:
				return

			authData = struct.pack('cB', b'\x05', len(self.phoneHash)) + self.phoneHash
			print('>> ', authData)
			s.send(authData)
			data = s.recv(256)
			print('<< ', data)
			if data != b'\x05\x00':
				return

			data = struct.pack('5B{}sH'.format(len(self.host)), 0x05, 0x02, 0x00, 0x03, len(self.host), self.host.encode(), self.port)

			print('>> ', data)
			s.send(data)
			data = s.recv(5)
			print('<< ', data)

			host_data = struct.unpack('5B', data)

			if host_data[0] != 0x05:
				return

			host_len = host_data[4]

			data = s.recv(host_len)

			remote_addr = data.decode()
			data = s.recv(2)
			remote_port = struct.unpack('H', data)[0]

			print('connected to {}:{}'.format(remote_addr, remote_port))


			s.setblocking(0)
			inputs = [s]
			outputs = []


			while True:
				try:
					readable, writable, _ = select.select(inputs, outputs, inputs)

					for sock in readable:


						data = sock.recv(5)
						print('<< ', data)

						client_data = struct.unpack('5B', data)

						if client_data[0] != 0x05:
							return

						client_len = client_data[4]

						data = sock.recv(client_len)
						print('<< ', data)

						client_addr = data.decode()
						data = sock.recv(2)
						print('<< ', data)
						client_port = struct.unpack('H', data)[0]

						t = Thread(target=self.socksThread, args=(self.host, client_port))
						t.start()
				except KeyboardInterrupt:
					print('stopping server')
					s.close()
					break
				except Exception as e:
					print(e)
					s.close()
					break


			# time.sleep(5)
			# s.close()
		except Exception as e:
			print(e)

	def socksThread(self, host, port):
		try:
			s = socket.socket()
			# s.setsockopt(socket.SOL_SOCKET, socket.SO_REUSEADDR, 1)
			s.connect((host, port))

			data = s.recv(2)

			hello_data = struct.unpack('2B', data)
			methods = []

			s.send(b'\x05\x00')

			data = s.recv(hello_data[1])
			methods = struct.unpack('{}B'.format(hello_data[1]), data)
			methods = list(methods)
			print(methods)

			if 0x00 not in methods:
				print('method is not allowed') 
				return

			data = s.recv(4)
			connect_data = struct.unpack('4B', data)
			print('<< ', connect_data)
			if connect_data[0] != 0x05 or connect_data[2] != 0x00:
				return

			cmd = connect_data[1]

			print(cmd)

			if cmd == 0x01:
				atyp = connect_data[3]
				addr = ''
				remote_port = 0
				if atyp == 0x01:
					data = s.recv(4)
					port = struct.unpack('!I', data)[0]
					addr += str((addr & 0xff000000) >> 24) + "."
					addr += str((addr & 0x00ff0000) >> 16) + "."
					addr += str((addr & 0x0000ff00) >> 8) + "."
					addr += str(addr & 0x000000ff)
				elif atyp == 0x03:
					data = s.recv(1)
					addr_len = struct.unpack('B', data)[0]
					data = s.recv(addr_len)
					# addr = struct.unpack('{}B'.format(addr_len), data)[0]
					addr = data.decode()
				else:
					raise Exception('atyp {} is unknown'.format())

				data = s.recv(2)
				remote_port = struct.unpack('!H', data)[0]

				data = struct.pack('5B{}sH'.format(len(addr)), 0x05, 0x00, 0x00, 0x03, len(addr), addr.encode(), socket.htons(remote_port))
				print('>> ', data)
				s.sendall(data)

				print('connecting to {}:{}'.format(addr, remote_port))

				socks_s = socket.socket()
				socks_s.connect((addr, remote_port))
				socks_s.setblocking(0)
				s.setblocking(0)

				inputs = [s, socks_s]
				outputs = []

				data = ''

				while True:
					readable, writable, _ = select.select(inputs, outputs, inputs)

					# print(_)

					for sock in readable:
						if sock == s:
							out_sock = socks_s
						else:
							out_sock = s

						# data = b''
						while True:
							d = sock.recv(512)

							print(d, '\n\n\n')

							if len(d) == 0:
								return
							out_sock.sendall(d)
							if len(d) < 512:
								break

			else:
				raise Exception('cmd {} is not support'.format(cmd))



		except Exception as e:
			print(e)
			s.close()



s = SocksServer('127.0.0.1', 5555)
s.start()