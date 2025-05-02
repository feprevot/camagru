FROM php:8.2-apache

RUN docker-php-ext-install pdo pdo_mysql

RUN a2enmod rewrite
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

ENV APACHE_DOCUMENT_ROOT=/var/www/app/public
RUN sed -ri -e 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/app
