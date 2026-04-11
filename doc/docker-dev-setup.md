# Setup Docker développement — architecture et pièges

## Architecture des volumes

```
Host (bind mount)           Volumes nommés (Docker)
─────────────────           ───────────────────────
.:/var/www/html             vendor:/var/www/html/vendor
                            var:/var/www/html/var
                            uploads:/var/www/html/public/uploads
                            thumbs:/var/www/html/public/thumbs
                            secured:/var/www/html/securedSpace
```

Les volumes nommés se montent **par-dessus** le bind mount. Priorité :
`volume nommé > bind mount` pour les chemins concernés.

`vendor/` et `var/` sont donc **toujours** fournis par les volumes Docker,
jamais par le répertoire du host.

---

## Rôle du `.dockerignore`

**Comportement Docker critique :** quand un volume nommé est créé pour la
première fois (volume vide), Docker l'initialise avec le contenu du répertoire
correspondant dans l'image.

Sans `.dockerignore`, `COPY . .` dans le Dockerfile copiait dans l'image :

- `var/cache/dev/` → cache Symfony du host, potentiellement périmé
- `vendor/` → dépendances Composer du host (écrasait le `composer install` fait dans le Dockerfile)
- `public/assets/` → assets compilés périmés
- `assets/vendor/` → packages JS inutiles (écrasés par le bind mount de toute façon)

**Conséquence :** après `docker compose down -v && docker compose up --build`,
les volumes neufs héritaient du cache périmé du host. Premier accès HTTP →
Symfony chargeait un DI container compilé référençant des fichiers inexistants.
Erreur typique :

```
Warning: require(.../var/cache/dev/Container8BXm1dU/getDomainControllerService.php):
Failed to open stream: No such file or directory
```

Le `.dockerignore` empêche ces répertoires d'entrer dans l'image. Les volumes
neufs démarrent vides et propres.

---

## Rôle de l'entrypoint

L'entrypoint tourne **après** que les volumes et le bind mount sont en place.
C'est là que tout ce qui dépend du système de fichiers réel doit s'exécuter.

```
importmap:install     → peuple assets/vendor/ sur le host via bind mount
migrations:migrate    → schéma BDD
fixtures:load (dev)   → données de test
sass:build (dev)      → compile SCSS → public/assets/styles/
cache:clear (dev)     → vide le cache Symfony (volume var)
cache:warmup (dev)    → reconstruit le DI container proprement avant le 1er hit
```

Sans `cache:warmup`, le cache est construit au premier hit HTTP. Si la
construction échoue à mi-chemin (permission, concurrence), le DI container
est partiellement écrit → crash.

---

## AssetMapper en mode développement

`importmap:install` télécharge les packages JS/CSS dans `assets/vendor/`
(répertoire source, visible via bind mount). Il ne touche **jamais**
`public/assets/vendor/`.

En mode dev, AssetMapper sert tous les assets **dynamiquement via PHP** avec
des URLs fingerprinted :

```
/assets/vendor/bootstrap/bootstrap.index-HgGqGv8.js  →  assets/vendor/bootstrap/...
```

Nginx reçoit la requête, ne trouve pas le fichier physique, tombe en fallback
sur `index.php`, Symfony sert le fichier. Le répertoire `public/assets/vendor/`
n'a pas besoin d'exister.

L'ancien volume `assets_vendor:/var/www/html/public/assets/vendor` était donc
inutile. Il créait en prime le répertoire `public/assets/` owned by `root` sur
le host (Docker daemon crée les répertoires intermédiaires manquants lors du
montage de volumes).

En mode **prod**, la commande `asset-map:compile` compile tout vers
`public/assets/` pour que nginx serve les fichiers statiquement sans PHP.

---

## Fichiers générés à l'exécution (gitignorés)

| Chemin | Généré par | Servi par |
|---|---|---|
| `assets/vendor/` | `importmap:install` (entrypoint) | AssetMapper via PHP |
| `public/assets/styles/` | `sass:build` (entrypoint + sass service) | nginx (fichier physique) |
| `var/cache/` | `cache:warmup` (entrypoint) | PHP (lecture interne) |
| `var/log/` | PHP-FPM à l'exécution | — |

---

## Recette reset complet

```bash
docker compose down -v          # supprime conteneurs + tous les volumes
docker compose up --build       # reconstruit l'image et redémarre
```

L'entrypoint recrée tout dans l'ordre correct. Aucune intervention manuelle
n'est nécessaire.
