# Dockerfile - Version avec vérification
FROM php:8.3-apache

# Installation des extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Configuration PHP
RUN echo "display_errors = On" > /usr/local/etc/php/conf.d/errors.ini

# Copie des fichiers
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html

# Démarrer Apache
CMD ["apache2-foreground"]