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

# Node.js 20.x — le serveur socket tourne maintenant dans cette image (fusion
# des services `app` et `socket`, 2026-09-12). 20.x et pas 18.x : le driver
# mongodb ^7.1 d'app_node exige Node >= 20.
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y --no-install-recommends nodejs supervisor \
    && mkdir -p /var/log/supervisor \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Dépendances Node du serveur socket, installées AU BUILD (avant le COPY des
# sources, pour que la couche soit mise en cache tant que package.json ne bouge
# pas). Avant la fusion, le service `socket` relançait `npm install` à chaque
# démarrage du conteneur : c'est le gros du surcoût qu'on supprime ici.
#
# --omit=dev : les devDependencies incluent @medyll/idae-be en `file:` vers un
# chemin Windows hôte (D:/development/idae/...) qui n'existe pas dans ce
# conteneur Linux. Le runtime (src/main.js) n'a besoin d'aucune devDep
# (esbuild/idae-be/sass/nodemon = outillage de build côté hôte).
# --package-lock=false : package-lock.json résout cette dépendance `file:`, et
# l'arborist de npm y touche même avec --omit=dev (bug npm connu sur les devDeps
# locales en file: — se manifeste par "Cannot read properties of undefined
# (reading 'extraneous')"). Sans lockfile, npm résout depuis les `dependencies`
# de package.json et n'atteint jamais l'entrée cassée.
WORKDIR /var/www/html/idae/web/app_node
COPY ./idae/web/app_node/package.json ./package.json
RUN npm install --omit=dev --package-lock=false --no-audit --no-fund

# Copier les fichiers de l'application dans le conteneur
# (ne supprime pas le node_modules installé ci-dessus ; .dockerignore empêche
# celui de l'hôte — symlinks pnpm Windows — d'être copié par-dessus)
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
# proxy/proxy_http/proxy_wstunnel : socket.io est servi en same-origin sur
# /socket.io/ depuis ce même conteneur (voir config/apache/socketio.conf)
RUN a2enmod proxy proxy_http proxy_wstunnel

# Apache écoute sur 80 dans le conteneur (le mapping 8080 est côté compose).
# Le port 3005 du serveur socket n'est volontairement PAS exposé : il n'est
# joignable que via le reverse proxy Apache, sur 127.0.0.1.
EXPOSE 80


# Copier le fichier de configuration des hôtes virtuels dans le conteneur
COPY ./config/apache/httpd-vhosts.conf /etc/apache2/sites-available/httpd-vhosts.conf

# Activer la configuration des hôtes virtuels and disable default
RUN a2ensite httpd-vhosts.conf && a2dissite 000-default

# Reverse proxy socket.io (conf serveur : s'applique aux deux vhosts)
COPY ./config/apache/socketio.conf /etc/apache2/conf-available/socketio.conf
RUN a2enconf socketio

# supervisord = PID 1 : il pilote apache2 ET le serveur socket node.
# On remplace la conf Debian au lieu de déposer un fichier dans conf.d/ : notre
# fichier redéfinit [supervisord] (nodaemon), et deux sections [supervisord]
# dans la même conf est ambigu.
COPY ./config/supervisor/supervisord.conf /etc/supervisor/supervisord.conf

CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/supervisord.conf"]
