FROM php:8.2-apache

COPY src/ /var/www/html/

RUN mkdir -p /var/lib/bomb-data \
    && chown -R www-data:www-data /var/www/html/ /var/lib/bomb-data
