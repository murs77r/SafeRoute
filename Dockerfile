FROM php:8.3-apache

# Extensoes necessarias para MySQL no projeto
RUN docker-php-ext-install mysqli pdo pdo_mysql \
    && a2enmod rewrite headers

# Permite .htaccess no diretorio da aplicacao
RUN sed -ri 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri 's!AllowOverride None!AllowOverride All!g' /etc/apache2/apache2.conf

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 80

# Apache inicia automaticamente pelo entrypoint da imagem base
