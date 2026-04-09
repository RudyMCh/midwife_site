#!/bin/sh
set -e

echo "==> Migrations..."
php bin/console doctrine:migrations:migrate --no-interaction

if [ "$APP_ENV" = "dev" ]; then
    echo "==> Fixtures..."
    php bin/console doctrine:fixtures:load --no-interaction

    echo "==> Sass build..."
    php bin/console sass:build
fi

exec docker-php-entrypoint "$@"
