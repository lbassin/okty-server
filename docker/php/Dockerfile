FROM php:7.2-fpm

RUN apt-get update && apt-get install -y git zlib1g-dev libicu-dev libcurl4-openssl-dev pkg-config libssl-dev && apt-get clean
RUN docker-php-ext-install zip pdo_mysql
RUN pecl install mongodb && docker-php-ext-enable mongodb
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

WORKDIR /usr/share/nginx/html