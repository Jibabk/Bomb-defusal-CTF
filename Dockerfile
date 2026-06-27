FROM php:8.2-apache

COPY . /var/www/html/

RUN apt-get update && apt-get install -y --no-install-recommends \
    gcc \
    libsqlite3-dev \
    openssh-server \
    && docker-php-ext-install pdo_sqlite \
    && rm -rf /var/lib/apt/lists/*

RUN gcc /var/www/html/src/Jobs/detonate.c -o /usr/local/bin/detonate \
    && chown root:root /usr/local/bin/detonate \
    && chmod 4755 /usr/local/bin/detonate

RUN mkdir -p /var/run/sshd

RUN mkdir -p /root/.ssh && chmod 700 /root/.ssh

COPY id_rsa.pub /root/.ssh/authorized_keys
RUN chmod 600 /root/.ssh/authorized_keys

RUN mkdir -p /var/lib/bomb-data \
    && chown -R www-data:www-data /var/www/html/ /var/lib/bomb-data

RUN echo "CTF{b0mb_h4s_b33n_d3fus3d}" > /root/flag.txt

EXPOSE 80 22

CMD service ssh start && apache2-foreground
