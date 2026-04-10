# Contexte du projet

Site vitrine + back-office pour un cabinet de sages-femmes à Dijon (rue des Saunières).
Deux praticientes : Aurélie Albandéa et Chloé Gauthier.
Objectif métier : augmenter les prises de rendez-vous via Doctolib.

## État du projet au 2026-04-10

La migration technique est **entièrement terminée** (voir `doc/conclusions.md` et `doc/etape9-corrections.md` pour le détail).

Stack en place :
- PHP 8.4 / Symfony 7.4 / Doctrine ORM 3
- AssetMapper + sass-bundle (Webpack Encore supprimé)
- TinyMCE v6, Tom Select, Vanilla JS (jQuery, CKEditor, Select2 supprimés)
- PHPStan level 8 : 0 erreur — PHP-CS-Fixer configuré
- Docker Compose fonctionnel (dev) — fixtures prêtes — site fonctionnel
- FileManagerBundle supprimé → `ImageUploadService` natif
- SEO de base en place (sitemap, robots.txt, meta tags, JSON-LD, cache HTTP, Matomo cookieless)

Le projet est sain. On entre dans une **phase nouvelle**, orientée contenu, UX et référencement.

---

# Objectifs de la phase actuelle

## 1. Visuel et style

- Recherche des attentes des profils visiteurs (femmes, femmes enceintes) : couleurs, typographie, style bannière/footer
- Création d'une charte graphique cohérente, simple, peu de fioritures
- Ajout de dynamisme CSS/JS mesuré
- Révision de la page sage-femme : trop rigide, meilleure corrélation volumes texte/image

## 2. SEO — invitation à la prise de rendez-vous

- Évaluer si le bouton Doctolib est suffisamment mis en avant
- Évaluer la pertinence d'un mécanisme de partage réseaux sociaux (fiche sage-femme, photos)
- Compléter `og:image` par page (actuellement absent)

## 3. Blog

- Ajout d'un système de blog activable/désactivable
- Les sages-femmes peuvent écrire des articles (rich text TinyMCE)
- Gestion des images dans les articles
- Archivage des articles
- Notification Google (sitemap dynamique, ping)

## 4. Aide à l'écriture SEO

- Section informative sur le SEO/référencement dans l'admin
- Bonnes pratiques synthétisées
- Au moins 3 exemples par champ éditable lié au référencement
- Helper succinct contextuel pour chaque champ concerné

---

# Consignes globales

## Code
- PHPStan level 8
- PHP-CS-Fixer avec le ruleset `@Symfony`
- Responsabilités limitées par fonction
- Nommage soigné
- Commentaires uniquement si la logique est complexe et le nommage insuffisant

## Workflow
- Branche de développement par fonctionnalité, commits soignés
- Documenter les changements dans `/doc/`

## Tech
- Hébergement : o2switch (mutualisé, pas de Docker en prod)
- Base de données : MariaDB 11.4
- Tracking : Matomo (cookieless)
- Pas de jQuery
- TinyMCE v6 pour le rich text
