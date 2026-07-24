FROM php:8.3-apache

# Extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer uniquement le MPM prefork
RUN a2dismod mpm_event mpm_worker mpm_auto 2>/dev/null || true && \
    a2enmod mpm_prefork

# Afficher les erreurs PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Copier le projet
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Port HTTP
EXPOSE 80

# Démarrer Apache
CMD ["apache2-foreground"]