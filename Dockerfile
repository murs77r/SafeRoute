FROM php:8.3-cli

# Extensoes necessarias para MySQL no projeto
RUN docker-php-ext-install mysqli pdo pdo_mysql

WORKDIR /var/www/html
COPY . /var/www/html

EXPOSE 80

# Railway injeta PORT automaticamente; local usa 80 por padrao
CMD ["sh", "-c", "php -S 0.0.0.0:${PORT:-80} -t /var/www/html /var/www/html/router.php"]
