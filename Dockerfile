# Dockerfile - Version corrigée
FROM php:8.3-apache

# Installer les extensions MySQL
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Activer les erreurs
RUN echo "display_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "display_startup_errors = On" >> /usr/local/etc/php/conf.d/errors.ini && \
    echo "error_reporting = E_ALL" >> /usr/local/etc/php/conf.d/errors.ini

# Copier les fichiers
COPY . /var/www/html/

WORKDIR /var/www/html

# Script d'entrypoint unique et correct
RUN echo '#!/bin/bash\n\
echo "=== DÉMARRAGE DU CONTENEUR ==="\n\
echo "PHP Version: $(php -v | head -1)"\n\
echo "Extensions MySQL:"\n\
php -m | grep -i mysql || echo "Aucune extension MySQL trouvée"\n\
echo "=== INITIALISATION DE LA BASE ==="\n\
if [ -f /var/www/html/init_db.php ]; then\n\
    php /var/www/html/init_db.php 2>&1\n\
else\n\
    echo "⚠️  init_db.php non trouvé"\n\
fi\n\
echo "=== DÉMARRAGE APACHE ==="\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]