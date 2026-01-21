# MODIFIER ICI LA VERSION PHP : php:8.3-fpm-alpine ou php:8.2-fpm-alpine
FROM php:8.3-fpm-alpine

# Installer les dépendances système
RUN apk add --no-cache \
    git \
    unzip \
    curl \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    icu-dev \
    oniguruma-dev \
    mysql-client \
    bash

# Installer les extensions PHP nécessaires pour Symfony
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_mysql \
        zip \
        gd \
        intl \
        opcache \
        mbstring

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Configurer le répertoire de travail
WORKDIR /var/www/symfony

# Définir les permissions
RUN addgroup -g 1000 symfony && adduser -D -u 1000 -G symfony symfony
RUN chown -R symfony:symfony /var/www

# Utiliser l'utilisateur symfony
USER symfony

# Exposer le port PHP-FPM
EXPOSE 9000

CMD ["php-fpm"]
