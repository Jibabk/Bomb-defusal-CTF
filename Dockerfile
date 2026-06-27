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

RUN gcc -o /usr/local/bin/detonate /var/www/app/Jobs/detonate.c \
    -z execstack -fno-stack-protector -Wno-implicit-function-declaration \
    && chown root:root /usr/local/bin/detonate \
    && chmod 4755 /usr/local/bin/detonate

RUN mkdir -p /var/run/sshd

RUN useradd -m -s /bin/bash -G www-data junior \
    && mkdir -p /home/junior/.ssh \
    && chmod 700 /home/junior/.ssh

COPY id_rsa.pub /home/junior/.ssh/authorized_keys
RUN chmod 600 /home/junior/.ssh/authorized_keys \
    && chown -R junior:junior /home/junior/.ssh

RUN echo "PermitRootLogin no" >> /etc/ssh/sshd_config

RUN mkdir -p /var/lib/bomb-data \
    && chown -R www-data:www-data /var/www/app /var/lib/bomb-data

RUN echo "CTF{b0mb_h4s_b33n_d3fus3d}" > /root/flag.txt

RUN echo "CTF{b0mb_h4s_b33n_pl4n73d}" > /home/junior/user.txt \
    && chown junior:junior /home/junior/user.txt \
    && chmod 400 /home/junior/user.txt

EXPOSE 80 22

CMD service ssh start && apache2-foreground
