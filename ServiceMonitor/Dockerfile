FROM php:7.0-apache
#COPY / /var/www/html/

RUN apt-get update && \
    apt-get install curl nano && \
    curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

RUN composer update
