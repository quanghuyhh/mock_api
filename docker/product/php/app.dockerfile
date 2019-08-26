FROM php:7.2.19-fpm

RUN apt-get update
RUN apt-get install -y libmcrypt-dev
RUN apt-get install -y mysql-client
RUN apt-get install -y libmagickwand-dev 
RUN pecl install imagick
RUN docker-php-ext-enable imagick
RUN pecl install mcrypt-1.0.2
RUN docker-php-ext-enable mcrypt
RUN docker-php-ext-install pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
RUN apt-get update && apt-get install -y zlib1g-dev
RUN docker-php-ext-install zip
