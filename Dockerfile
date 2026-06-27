FROM php:8.2-apache

ENV APACHE_DOCUMENT_ROOT=/var/www/app/public

RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libsqlite3-dev \
    openssh-server \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

RUN sed -ri -e "s!/var/www/html!${APACHE_DOCUMENT_ROOT}!g" /etc/apache2/sites-available/*.conf /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf
RUN a2dismod -f autoindex

COPY src /var/www/app

RUN gcc /var/www/app/Jobs/detonate.c -o /usr/local/bin/detonate \
    && chown root:root /usr/local/bin/detonate \
    && chmod 4755 /usr/local/bin/detonate

RUN mkdir -p /var/run/sshd

RUN mkdir -p /root/.ssh && chmod 700 /root/.ssh

COPY id_rsa.pub /root/.ssh/authorized_keys
RUN chmod 600 /root/.ssh/authorized_keys

RUN mkdir -p /var/lib/bomb-data \
    && chown -R www-data:www-data /var/www/app /var/lib/bomb-data

RUN echo "CTF{b0mb_h4s_b33n_d3fus3d}" > /root/flag.txt

EXPOSE 80 22

CMD service ssh start && apache2-foreground
