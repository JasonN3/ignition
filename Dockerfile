FROM docker.io/library/php:apache

COPY src/ /var/www/html/

VOLUME /var/www/html/fcos/customizations