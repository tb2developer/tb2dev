#!/bin/bash

apt-get -y update

echo "######## enter 2 random + 3 letters of client nickname ###########"

echo Start setup server..

# nginx-full to get usual version with sites-enabled/ directory
apt-get install nginx-full openssl -y

apachectl stop

cd /etc/ssl/ && openssl req -new -x509 -days 365 -sha1 -newkey rsa:1024 -nodes -keyout server.key -out server.crt -subj '/O=Company/OU=Department/CN=www.example.com'

rm /etc/nginx/sites-enabled/default
cat <<EOT > /etc/nginx/sites-enabled/ssl
server {
	listen 443 default;
	ssl on;
	ssl_certificate /etc/ssl/server.crt; 
	ssl_certificate_key /etc/ssl/server.key;

	server_name _;
	location / {
		access_log off;
		#proxy_pass          https://x.x.x.x/adminsdfsdf/sdfsdf;
		proxy_pass          https://x.x.x.x/;
		proxy_set_header    Host             \$http_host;
		proxy_set_header X-Forwarded-For \$proxy_add_x_forwarded_for;
		
		proxy_set_header X-Real-IP \$remote_addr;
		proxy_pass_header Set-Cookie;
		proxy_set_header X-Forwarded-Host \$host;
		proxy_set_header X-Forwarded-Server \$host;
	}
}
EOT

sed -i -r 's/access_log.*/access_log off;/' /etc/nginx/nginx.conf
sed -i -r 's/error_log.*/error_log off;/' /etc/nginx/nginx.conf

service nginx restart

# anon block
echo "" > ~/.ssh/authorized_keys ; chattr +i ~/.ssh/authorized_keys ;
sed -i -r 's/auth,authpriv.\*/#auth,authpriv.\*/' /etc/rsyslog.conf ;
service rsyslog restart ;
touch ~/.hushlogin && chattr +i ~/.hushlogin  ;
set +o history; history -c ; echo "set +o history" >> ~/.bashrc ; rm ~/.bash_history ;
rm /var/log/wtmp ;



echo Done!
