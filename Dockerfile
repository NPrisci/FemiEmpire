FROM php:8.3-apache

RUN docker-php-ext-install pdo pdo_mysql mysqli

# Supprimer tous les modules MPM activés
RUN rm -f /etc/apache2/mods-enabled/mpm_*.load \
          /etc/apache2/mods-enabled/mpm_*.conf

# Activer uniquement le MPM prefork
RUN a2enmod mpm_prefork

COPY . /var/www/html/

CMD ["apache2-foreground"]