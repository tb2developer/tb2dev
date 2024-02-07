#!/usr/bin/python3
import requests
# import logging
# logging.basicConfig(level=logging.DEBUG)
import socks
from threading import Thread
from urllib.parse import urlparse


def recvall(s):
	l = 256
	res = b''
	while True:
		r = s.recv(l)
		if len(r) == -1:
			break

		res += r
		if len(r) < l:
			break

	return res



def raw_worker(url):

	u = urlparse(url)
	s = socks.socksocket()
	# s.set_proxy(socks.SOCKS5, '127.0.0.1', 9050)
	s.set_proxy(socks.SOCKS5, '1.2.86.32', 42928)
	print('[{}] CONNECT'.format(u.netloc))
	try:
		s.connect((u.netloc, 80))
	except Exception as e:
		print(e)
		return
	req = b'GET /%s%s HTTP/1.1\r\nHost: %s\r\n\r\n' % (u.path.encode(), b'?' + u.query.encode() if u.query else b'', u.netloc.encode())
	print('[{}] >>> {}'.format(u.netloc, req))
	s.send(req)

	page = recvall(s)
	s.close()
	print('[{}] <<< {}'.format(u.netloc, page[:50]))

def worker(url):

	p = {'http': 'socks5://192.168.0.106:42928', 'https': 'socks5://192.168.0.106:42928'}
	#p = {'http': 'socks5://192.168.0.106:43629', 'https': 'socks5://192.168.0.106:43629'}
	p = {'http': 'socks5://109.236.86.32:42928', 'https': 'socks5://109.236.86.32:42928'}
	try:
		# r = requests.get('http://2ip.ru', proxies=p)
		r = requests.get(url, proxies=p)
		print(url, r)
	except Exception as e:
		# print("Can't load page, socks is dead")
		print(e)


# worker('http://2ip.ru')
# exit()

urls = ['http://2ip.ru', 'http://vk.com', 'http://ya.ru', 'http://wtfismyip.com/text', 'http://google.com', 'https://facebook.com', 'https://instagram.com', 'https://mail.ru']
# urls = ['http://2ip.ru', 'http://vk.com', 'http://ya.ru', 'http://wtfismyip.com/text', 'https://facebook.com', 'https://instagram.com', 'https://mail.ru']
threads = []

for url in urls:
	t = Thread(target=worker, args=(url,))

	# t = Thread(target=raw_worker, args=(url,))
	t.start()
	threads.append(t)

	# raw_worker(url)

for t in threads:
	t.join()

