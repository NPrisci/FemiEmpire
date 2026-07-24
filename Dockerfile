# Dockerfile - Version finale stable
FROM php:8.3-apache

# Installer extensions
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer erreurs
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Copier les fichiers
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Démarrer Apache
CMD ["apache2-foreground"]