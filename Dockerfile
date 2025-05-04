FROM php:8.2-apache

RUN apt-get update && apt-get install -y openssl

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod ssl rewrite

ENV APACHE_DOCUMENT_ROOT=/var/www/app/public
RUN sed -ri -e 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/app

COPY docker/ssl/selfsigned.crt /etc/ssl/certs/selfsigned.crt
COPY docker/ssl/selfsigned.key /etc/ssl/private/selfsigned.key

COPY docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl
