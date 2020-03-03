FROM php:7.4-fpm

RUN apt-get update && apt-get install -y zlib1g-dev libzip-dev && apt-get clean
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN docker-php-ext-install zip pdo_mysql
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/share/nginx/html