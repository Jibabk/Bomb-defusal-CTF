FROM php:8.2-apache

# Copia os arquivos do nosso painel para o servidor web
COPY src/ /var/www/html/

# Ajusta as permissões para o usuário do Apache
RUN chown -R www-data:www-data /var/www/html/
