FROM php:8.4-fpm-alpine

# Dépendances runtime + build (les -dev sont supprimées après compilation)
RUN apk add --no-cache \
        bash git unzip \
        icu-libs libzip libjpeg-turbo libpng freetype libwebp \
    && apk add --no-cache --virtual .build-deps \
        icu-dev libzip-dev libjpeg-turbo-dev libpng-dev freetype-dev libwebp-dev oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql intl gd zip opcache bcmath mbstring \
    && apk del .build-deps

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# PHP config
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/zz-app.conf

# Entrypoint (avant COPY . . pour ne pas invalider ce layer à chaque changement de code)
COPY docker/php/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

# Dépendances Composer (layer mis en cache tant que composer.json/lock n'ont pas changé)
COPY composer.json composer.lock ./
RUN composer install --no-scripts --no-autoloader --prefer-dist

# Code source
COPY . .
RUN git config --global --add safe.directory /var/www/html \
    && composer dump-autoload --optimize \
    && php bin/console importmap:install \
    && chown -R www-data:www-data /var/www/html

ENTRYPOINT ["entrypoint.sh"]
CMD ["php-fpm"]
