FROM php:8.2.10-apache

WORKDIR /var/www/html

RUN apt update -y && \
    apt upgrade -y && \
    apt install -y libonig-dev && \
    docker-php-ext-install mbstring mysqli

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug
RUN echo "zend_extension=xdebug.so" >> /usr/local/etc/php/conf.d/90-xdebug.ini
RUN echo "xdebug.mode=develop,debug" >> /usr/local/etc/php/conf.d/90-xdebug.ini
RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/90-xdebug.ini
RUN echo "xdebug.discover_client_host=0" >> /usr/local/etc/php/conf.d/90-xdebug.ini
RUN echo "xdebug.client_host=host.docker.internal" >> /usr/local/etc/php/conf.d/90-xdebug.ini

COPY . .

RUN chown -R www-data:www-data /var/www/html && \
    a2enmod rewrite
