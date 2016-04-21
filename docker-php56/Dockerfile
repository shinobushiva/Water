FROM php:5.6-apache
RUN apt-get update && apt-get install -y libmysqlclient-dev libmemcached-dev \
    && docker-php-ext-install pdo pdo_mysql \
    && pecl install memcached \
    && docker-php-ext-enable memcached \
