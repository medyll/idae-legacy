# PHP 8.2 avec Apache
FROM php:8.2-apache

# Installation des dépendances système et extensions PHP
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libzip-dev \
        libicu-dev \
        libxml2-dev \
        libxslt1-dev \
        libonig-dev \
        libcurl4-openssl-dev \
        unzip \
        curl \
        gnupg \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        gd \
        mysqli \
        pdo_mysql \
        zip \
        intl \
        opcache \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Configuration PHP
RUN echo "short_open_tag=On" >> /usr/local/etc/php/php.ini \
    && echo "session.save_path = /tmp" >> /usr/local/etc/php/php.ini \
    && echo "session.auto_start = On" >> /usr/local/etc/php/php.ini \
    && echo "error_log = /var/log/apache2/php-error.log" >> /usr/local/etc/php/php.ini \
    && echo "memory_limit = 256M" >> /usr/local/etc/php/php.ini \
    && echo "upload_max_filesize = 50M" >> /usr/local/etc/php/php.ini \
    && echo "post_max_size = 50M" >> /usr/local/etc/php/php.ini

# Installation de Node.js 18.x
RUN curl -fsSL https://deb.nodesource.com/setup_18.x | bash - \
    && apt-get install -y nodejs

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Copier les fichiers de l'application dans le conteneur
COPY ./idae /var/www/html/idae
# Copier la configuration de .user.ini dans le conteneur
# COPY ./idae/web/.user.ini /var/www/html/.user.ini

# Donner les permissions nécessaires
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Installer les dépendances PHP avec Composer (mongodb/mongodb)
WORKDIR /var/www/html/idae/web
RUN if [ -f composer.json ]; then \
        composer install --no-dev --ignore-platform-reqs --optimize-autoloader; \
    fi

WORKDIR /var/www/html

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
