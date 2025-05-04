#!/bin/bash

envsubst < /etc/msmtprc.template > /etc/msmtprc

chown www-data:www-data /etc/msmtprc
chmod 600 /etc/msmtprc

touch /var/log/msmtp.log
chown www-data:www-data /var/log/msmtp.log

exec "$@"
