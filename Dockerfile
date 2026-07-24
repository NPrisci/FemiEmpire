# Dockerfile - Version ultra simple
FROM php:8.3-apache
RUN docker-php-ext-install pdo pdo_mysql mysqli
COPY . /var/www/html/
CMD ["apache2-foreground"]