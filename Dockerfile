FROM docker.io/library/php:apache

VOLUME /var/www/html/fcos/customizations

COPY src/ /var/www/html/
