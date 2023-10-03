FROM php:8.2.10-apache

WORKDIR /var/www/html

RUN apt update -y && \
    apt upgrade -y && \
    apt install -y libonig-dev && \
    docker-php-ext-install mbstring mysqli

COPY . .

RUN chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite
