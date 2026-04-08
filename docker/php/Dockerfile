FROM php:8.4-fpm-alpine

# Dépendances système
RUN apk add --no-cache \
    bash \
    git \
    unzip \
    curl \
    icu-dev \
    icu-libs \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    freetype-dev \
    oniguruma-dev \
    linux-headers

# Extensions PHP
RUN docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        intl \
        gd \
        zip \
        opcache \
        bcmath \
        mbstring

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf

# Droits www-data
RUN chown -R www-data:www-data /var/www/html
