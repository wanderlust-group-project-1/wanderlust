FROM composer:2.7.6 AS composer
FROM php:8.3-apache

COPY --from=composer /usr/bin/composer /usr/bin/composer

RUN docker-php-ext-install mysqli pdo pdo_mysql 
RUN apt-get update && apt-get install -y \
    libzip-dev \
    zip \
    git \
    unzip 


COPY ./conf/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY composer.json composer.lock /var/www/

ENV COMPOSER_ALLOW_SUPERUSER 1
RUN composer install  --working-dir=/var/www

COPY .env /var/www

# allow .htaccess with RewriteEngine
RUN a2enmod rewrite

RUN rm -rf /var/www/html

# create public, app folders
RUN mkdir -p /var/www/public
RUN mkdir -p /var/www/app
