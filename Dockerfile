FROM php:8.2-apache

COPY src/ /var/www/html/

RUN apt-get update \
    && apt-get install -y --no-install-recommends libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/* \
    && mkdir -p /var/lib/bomb-data \
    && chown -R www-data:www-data /var/www/html/ /var/lib/bomb-data
