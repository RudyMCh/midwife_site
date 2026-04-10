# Environnement Docker

## Architecture

```
docker-compose.yml          ← services de base (prod-like)
docker-compose.override.yml ← surcharges dev (ports exposés, mailcatcher)
docker/
├── php/
│   ├── Dockerfile          ← PHP 8.4-fpm-alpine
│   ├── php.ini             ← config PHP commune
│   ├── php.dev.ini         ← surcharges dev (chargé via override)
│   └── php-fpm.conf        ← config PHP-FPM
└── nginx/
    └── default.conf        ← vhost Symfony
```

## Services

| Service | Image | Port exposé (dev) | Rôle |
|---|---|---|---|
| `php` | build local (PHP 8.4-fpm-alpine) | — | Exécution PHP |
| `nginx` | nginx:1.27-alpine | 8080 | Serveur web |
| `database` | mariadb:11.4 | 3306 | Base de données |
| `mailer` | schickling/mailcatcher | 1080 | Capture emails dev |

## Volumes nommés

| Volume | Monté dans | Rôle |
|---|---|---|
| `db-data` | `/var/lib/mysql` | Persistance BDD |
| `uploads` | `/var/www/html/public/uploads` | Fichiers uploadés |
| `thumbs` | `/var/www/html/public/thumbs` | Thumbnails générés |
| `secured` | `/var/www/html/securedSpace` | Fichiers privés |

Les volumes `uploads`, `thumbs`, `secured` sont partagés entre `php` (lecture/écriture) et `nginx` (lecture seule pour les assets).

## Extensions PHP installées

- `pdo_mysql` — connexion MariaDB
- `intl` — internationalisation Symfony
- `gd` — manipulation d'images (JPEG, PNG, GIF avec freetype)
- `zip` — archives
- `opcache` — cache bytecode
- `bcmath`, `mbstring` — calculs et chaînes multibyte

## Commandes courantes

```bash
# Démarrer l'environnement
docker compose up -d

# Voir les logs
docker compose logs -f php
docker compose logs -f nginx

# Exécuter une commande Symfony
docker compose exec php bin/console <commande>

# Composer
docker compose exec php composer install

# Migrations
docker compose exec php bin/console doctrine:migrations:migrate

# Vider le cache
docker compose exec php bin/console cache:clear

# Accéder à MariaDB
docker compose exec database mariadb -u sagefemme -psagefemme sagefemme

# Mailcatcher (interface web)
# http://localhost:1080
```

## Configuration des variables d'environnement

Les credentials ne sont **jamais** dans `.env` (commité). Le workflow est :

1. `.env` — valeurs par défaut non sensibles, commité
2. `.env.local` — surcharges locales, **non commité** (dans `.gitignore`)

Pour la production (o2switch), configurer les variables d'environnement directement sur le serveur ou via `composer dump-env prod`.

## Note sur `/lib/`

Le répertoire `/lib/FileManagerBundle` est dans `.gitignore` et donc **non versionné**. Il devra être versionné ou intégré directement dans `src/` lors de la migration (suppression du bundle).

## Xdebug (optionnel)

Pour activer Xdebug en développement, décommenter dans `docker/php/php.dev.ini` et ajouter l'installation dans le Dockerfile :

```dockerfile
RUN apk add --no-cache $PHPIZE_DEPS linux-headers \
    && pecl install xdebug \
    && docker-php-ext-enable xdebug
```
