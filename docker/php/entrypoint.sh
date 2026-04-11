#!/bin/sh
set -e

echo "==> Assets vendor (importmap)..."
php bin/console importmap:install

echo "==> Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

if [ "$APP_ENV" = "dev" ]; then
    echo "==> Fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction

    echo "==> Sass build..."
    php bin/console sass:build

    echo "==> Cache (clear + warmup)..."
    php bin/console cache:clear
    php bin/console cache:warmup
fi

exec docker-php-entrypoint "$@"
