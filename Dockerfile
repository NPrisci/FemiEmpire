# Dockerfile
FROM php:8.3-apache

# Installer les extensions MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer les erreurs
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Copier les fichiers
COPY . /var/www/html/

# Créer un script d'initialisation
RUN echo '#!/bin/bash\nphp /var/www/html/init_db.php\napache2-foreground' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]