FROM php:8.2-apache

RUN apt-get update && apt-get install -y \
    openssl \
    msmtp \
    msmtp-mta \
    ca-certificates \
    gettext

RUN a2enmod ssl rewrite

RUN docker-php-ext-install pdo pdo_mysql

ENV APACHE_DOCUMENT_ROOT=/var/www/app/public
RUN sed -ri 's!/var/www/html!/var/www/app/public!g' /etc/apache2/sites-available/000-default.conf

COPY . /var/www/app

COPY docker/ssl/selfsigned.crt /etc/ssl/certs/selfsigned.crt
COPY docker/ssl/selfsigned.key /etc/ssl/private/selfsigned.key

COPY docker/apache/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl

COPY docker/msmtp/msmtprc.template /etc/msmtprc.template
COPY docker/msmtp/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

RUN [ ! -f /usr/sbin/sendmail ] || rm /usr/sbin/sendmail && ln -s /usr/bin/msmtp /usr/sbin/sendmail

ENTRYPOINT ["/entrypoint.sh"]
CMD ["apache2-foreground"]
