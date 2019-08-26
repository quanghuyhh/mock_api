FROM nginx:1.14
ADD docker/product/nginx/vhost.conf /etc/nginx/conf.d/default.conf