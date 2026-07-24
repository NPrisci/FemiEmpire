FROM php:8.3-apache

# Extensions PHP
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Désactiver tous les MPM puis activer uniquement prefork
RUN a2dismod mpm_event mpm_worker mpm_prefork 2>/dev/null || true && \
    a2enmod mpm_prefork

# Activer le rewrite pour Laravel
RUN a2enmod rewrite

# Configuration PHP
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Configuration du document root Laravel
RUN sed -i 's|DocumentRoot /var/www/html|DocumentRoot /var/www/html/public|' \
    /etc/apache2/sites-available/000-default.conf

# Copier le projet
COPY . /var/www/html/

# Permissions
RUN chown -R www-data:www-data /var/www/html && \
    chmod -R 755 /var/www/html

# Démarrage Apache
CMD ["apache2-foreground"]