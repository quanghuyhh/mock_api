FROM nginx:1.14
ADD docker/dev/nginx/vhost.conf /etc/nginx/conf.d/default.conf