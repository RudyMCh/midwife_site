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
sass:build (dev)      → compile SCSS → var/sass/ (volume Docker, lu par AssetMapper)
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
/assets/styles/front-HASH.css  →  var/sass/front.output.css  (volume Docker)
/assets/vendor/bootstrap/...   →  assets/vendor/bootstrap/...  (bind mount)
```

Nginx reçoit la requête, ne trouve **pas** le fichier physique dans `public/`,
tombe en fallback sur `index.php`, Symfony sert le fichier. `public/assets/`
n'a pas besoin d'exister du tout en dev.

### Workflow SCSS en dev

Le service `sass` (dans `docker-compose.override.yaml`) tourne en permanence :

```
assets/styles/**/*.scss  →  [sass:build --watch]  →  var/sass/front.output.css
                                                         (volume Docker)
```

AssetMapper lit `var/sass/front.output.css` et le sert dynamiquement. Toute
modification SCSS est visible après un simple rechargement de page.

**Ne jamais lancer `asset-map:compile` en développement.** C'est la commande
de production uniquement.

### Piège : fichiers stale dans `public/assets/`

Nginx a cette règle dans sa configuration :

```nginx
location ~* \.(css|js|...)$ {
    try_files $uri /index.php$is_args$args;
}
```

Si un fichier CSS existe dans `public/assets/styles/`, nginx le sert
**directement**, sans passer par Symfony. Le SCSS recompilé par le service
`sass` est alors invisible, même après rechargement.

**Ce qui crée des fichiers stale :**
- Un `asset-map:compile` lancé en dev (deploy test, etc.)
- Ces fichiers sont écrits en `root` par Docker → `rm` impossible depuis le host

**Symptômes :** modifications SCSS sans effet visible en dev, même après
`sass:build` et rechargement.

**Diagnostic :**
```bash
ls public/assets/styles/          # fichiers stale présents ?
docker compose logs sass           # le service sass recompile-t-il ?
```

**Remède :** supprimer depuis le container (Docker a écrit en root) :
```bash
docker compose exec php bash -c \
  "rm -rf /var/www/html/public/assets/styles \
          /var/www/html/public/assets/manifest.json \
          /var/www/html/public/assets/importmap.json"
```
Puis hard-refresh navigateur (`Ctrl+Shift+R`).

En mode **prod**, la commande `asset-map:compile` compile tout vers
`public/assets/` pour que nginx serve les fichiers statiquement sans PHP.
Après un passage en prod/staging, toujours supprimer `public/assets/styles/`
avant de repasser en dev.

---

## Fichiers générés à l'exécution (gitignorés)

| Chemin | Généré par | Servi par |
|---|---|---|
| `assets/vendor/` | `importmap:install` (entrypoint) | AssetMapper via PHP |
| `var/sass/*.output.css` | `sass:build --watch` (service `sass`) | AssetMapper via PHP |
| `public/assets/` | `asset-map:compile` (**prod uniquement**) | nginx statique |
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

