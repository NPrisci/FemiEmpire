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

# Créer un script d'initialisation NON BLOQUANT
RUN echo '#!/bin/bash\n\
echo "=== INITIALISATION DE LA BASE ==="\n\
php /var/www/html/init_db.php\n\
echo "=== DÉMARRAGE D APACHE ==="\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

# Ajouter un timeout pour éviter le blocage
RUN echo '#!/bin/bash\n\
echo "=== LANCEMENT DU CONTENEUR ==="\n\
# Lancer l init en arrière-plan si elle prend trop de temps\n\
timeout 30 php /var/www/html/init_db.php || echo "Init timeout ou déjà fait"\n\
echo "=== DÉMARRAGE APACHE ==="\n\
apache2-foreground' > /usr/local/bin/docker-entrypoint.sh && \
    chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

ENTRYPOINT ["/usr/local/bin/docker-entrypoint.sh"]