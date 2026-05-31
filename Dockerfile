FROM php:8.3-apache

# Extensoes necessarias para MySQL no projeto
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2dismod mpm_event mpm_worker || true \
    && a2enmod mpm_prefork rewrite headers

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 80

# Apache inicia automaticamente pelo entrypoint da imagem base
