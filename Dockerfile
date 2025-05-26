# Revenir à une image de base avec PHP 5.6 et Apache
FROM php:5.6-apache

# Mettre à jour les sources des dépôts pour Debian Stretch
RUN sed -i '/stretch-updates/d' /etc/apt/sources.list \
    && sed -i 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i 's|http://security.debian.org/debian-security|http://archive.debian.org/debian-security|g' /etc/apt/sources.list \
    && apt-get update -o Acquire::Check-Valid-Until=false -o Acquire::Check-Date=false \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libbz2-dev \
        libgmp-dev \
        libldap2-dev \
        libicu-dev \
        libxml2-dev \
        libxslt1-dev \
        libsqlite3-dev \
        libonig-dev \
        libmcrypt-dev \
        libcurl4-openssl-dev \
        libedit-dev \
        libssl-dev \
        zlib1g-dev \
        libzip-dev \
        unzip \
    && pecl install mongo \
    && docker-php-ext-enable mongo

# Configurer short_open_tag à On
RUN echo "short_open_tag=On" >> /usr/local/etc/php/php.ini

# Ajouter les directives session directement dans le fichier php.ini
RUN echo "session.save_path = /tmp" >> /usr/local/etc/php/php.ini \
    && echo "session.auto_start = On" >> /usr/local/etc/php/php.ini

# Configurer le fichier de log des erreurs PHP
RUN echo "error_log = /var/log/apache2/php-error.log" >> /usr/local/etc/php/php.ini

# Mettre à jour les sources de Debian Stretch vers les archives
RUN sed -i 's|http://deb.debian.org/debian|http://archive.debian.org/debian|g' /etc/apt/sources.list \
    && sed -i '/security.debian.org/d' /etc/apt/sources.list \
    && apt-get update \
    && apt-get install -y --no-install-recommends gnupg \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer apt-transport-https avant d'ajouter les dépôts NodeSource
RUN apt-get update && apt-get install -y --no-install-recommends --allow-unauthenticated apt-transport-https \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Modifier les dépôts NodeSource pour installer Node.js 12.x au lieu de 14.x
RUN curl -fsSL https://deb.nodesource.com/gpgkey/nodesource.gpg.key | apt-key add - \
    && echo "deb https://deb.nodesource.com/node_12.x stretch main" > /etc/apt/sources.list.d/nodesource.list \
    && echo "deb-src https://deb.nodesource.com/node_12.x stretch main" >> /etc/apt/sources.list.d/nodesource.list \
    && apt-get update \
    && apt-get install -y --allow-unauthenticated nodejs

# Copier les fichiers de l'application dans le conteneur
COPY ./idae /var/www/html
# Copier la configuration de .user.ini dans le conteneur
# COPY ./idae/web/.user.ini /var/www/html/.user.ini

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Activer le module Apache mod_rewrite
RUN a2enmod rewrite
# Activer le module Apache mod_headers
RUN a2enmod headers
 

# Exposer le port 8080
EXPOSE 8080


# Copier le fichier de configuration des hôtes virtuels dans le conteneur
COPY ./config/apache/httpd-vhosts.conf /etc/apache2/sites-available/httpd-vhosts.conf

# Activer la configuration des hôtes virtuels
RUN a2ensite httpd-vhosts.conf

# Remplacer la commande CMD pour démarrer Apache sans charger envvars
CMD ["apache2-foreground"]
