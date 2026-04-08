# Analyse du projet SageFemme

Projet Symfony 5.4, PHP 7.4+, MariaDB.  
Site vitrine + back-office pour 2-3 sage-femmes, objectif : remplissage du planning.

---

## Stack technique actuelle

| Composant | Version actuelle | Cible |
|---|---|---|
| PHP | 7.4+ | 8.4 |
| Symfony | 5.4 | 7.4 |
| Doctrine ORM | ^2.10 | ^3 |
| Twig | 2.12\|3.0 | 3.x |
| Webpack Encore | ^1.0 | évaluation AssetMapper |
| jQuery | 3.6.0 | supprimer |
| Bootstrap | 5.1.3 | à jour |
| CKEditor | 4.12.1 | TinyMCE v6 |
| Select2 | 4.1.0-rc.0 | à évaluer (jQuery dépendant) |
| KnpPaginator | ^5.7 | ^6 |
| FOSCKEditorBundle | ^2.3 | supprimer |
| stof/doctrine-extensions | ^1.7 | ^1.8+ |

---

## Entités Doctrine (10)

### `User`
Authentification. Email unique, rôles (tableau), mot de passe hashé.  
Implémente `UserInterface`, `PasswordAuthenticatedUserInterface`.  
Liée à `ResetPasswordRequest`.

### `Midwife`
Entité centrale. Représente une sage-femme avec son profil public.

| Champ | Type | Rôle |
|---|---|---|
| `firstname`, `lastname` | string | Identité |
| `slug` | string unique | Généré Gedmo depuis firstname+lastname |
| `aboutMe` | string(5000) | Présentation courte |
| `description` | string(5000) | Description longue |
| `backgroundColor1` | string (CSS color) | Couleur personnalisée |
| `phone` | string(15) | Regex `/^[0-9]{10}$/` |
| `email` | string | Validé |
| `doctolibUrl` | string | URL Doctolib |
| `metaDescription` | string(80-120) | SEO — longueur contrainte |
| `picture` | ManyToOne File | Photo de profil |
| `bgCard` | ManyToOne File | Image de fond carte |
| `bgTitle` | ManyToOne File | Image de fond titre page |
| `pictureSelf` | ManyToOne File | Photo personnelle |
| `pictures` | ManyToMany File | Galerie |
| `paths` | OneToMany Path | Parcours professionnel |
| `degrees` | OneToMany Degree | Diplômes |
| `services` | ManyToMany Service | Prestations proposées |

### `Service`
Prestation médicale. Rattachée à un `Domain`, à plusieurs `Midwife`.

| Champ | Type |
|---|---|
| `name`, `slug` | string |
| `description` | text |
| `domain` | ManyToOne Domain |
| `picture` | ManyToOne File |
| `midwives` | ManyToMany Midwife |
| `position` | int nullable (ordre d'affichage) |

### `Domain`
Domaine de compétence regroupant des services (ex: gynécologie, obstétrique).

| Champ | Type |
|---|---|
| `name`, `slug` | string |
| `titleBg` | ManyToOne File |
| `metaTitle`, `metaDescription` | string |
| `services` | OneToMany Service |

### `HomePage`
Singleton. Contenu de la page d'accueil.

| Champ | Type |
|---|---|
| `title`, `catchphrase` | string |
| `about` | string(1000) |
| `backgroundImage1`, `backgroundImage2`, `titleBg` | ManyToOne File |
| `pictures` | ManyToMany File |
| `metaTitle`, `metaDescription` | string |

### `InformationPage`
Singleton. Page "Informations utiles".

| Champ | Type | Rôle |
|---|---|---|
| `legal` | text | Mentions légales |
| `coming` | text nullable | À venir |
| `price` | text nullable | Tarifs |
| `links` | text nullable | Liens utiles |
| `mention` | string nullable | Mention libre |
| `title` | string nullable | Titre page |
| `titleBg` | ManyToOne File | Image de fond |

### `Office`
Singleton. Informations du cabinet.

| Champ | Type |
|---|---|
| `name`, `address`, `zipcode`, `city` | string |
| `phone` | string |
| `about` | text nullable |
| `urlGoogleMap` | string nullable |
| `latitude`, `longitude` | string nullable |

### `Path`
Parcours professionnel d'une sage-femme.

| Champ | Type |
|---|---|
| `title` | string |
| `start`, `end` | string (dates textuelles) |
| `city` | string nullable |
| `midwife` | ManyToOne Midwife |

### `Degree`
Diplôme d'une sage-femme.

| Champ | Type |
|---|---|
| `title` | string |
| `establishment` | string nullable |
| `year` | string nullable |
| `type` | string nullable |
| `midwife` | ManyToOne Midwife |

### `ResetPasswordRequest`
Gestion des tokens de réinitialisation de mot de passe (SymfonyCasts bundle).

---

## Controllers

### Front (4 controllers)

#### `FrontController\HomePageController` — `/`
Route `homepage_homepage` : charge `HomePage`, `Office`, toutes les `Midwife`, tous les `Domain`. Passe `meta_title` et `meta_description` au template.

#### `FrontController\MidwifeController` — `/sage-femme/{slug}`
Route `midwife_show` : charge la `Midwife` par slug (ParamConverter implicite), charge tous les `Domain`.

#### `FrontController\DomainController` — `/domaine/{slug}`
Charge le `Domain` par slug, tous les `Domain`, les `Service` du domaine.

#### `FrontController\InformationController` — `/information`
Charge le singleton `InformationPage`, tous les `Domain`.

### Admin (11 controllers)

Tous sous `/admin/`, tous protégés `@IsGranted("ROLE_ADMIN")`.

| Controller | Route | Pattern |
|---|---|---|
| `MainController` | `/admin/` | Dashboard avec compteurs d'entités |
| `MidwifeController` | `/admin/sage-femme/` | Index (paginé KnpPaginator), new, edit, delete |
| `ServiceController` | `/admin/service/` | CRUD + recherche |
| `DomainController` | `/admin/domaine/` | CRUD |
| `HomePageController` | `/admin/homepage/` | Edit singleton |
| `InformationPageController` | `/admin/information/` | Edit singleton |
| `OfficeController` | `/admin/bureau/` | Edit singleton |
| `UserController` | `/admin/user/` | CRUD utilisateurs |
| `DegreeController` | `/admin/degree/` | CRUD (embedded dans MidwifeController) |
| `PathController` | `/admin/path/` | CRUD (embedded dans MidwifeController) |
| `UtilsController` | `/admin/utils/` | Utilitaires divers |

**Pattern général CRUD admin :** chaque entité a un `Handler` dédié dans `src/Form/Handler/` qui encapsule la logique persist/flush. Les controllers restent minces.

### Sécurité (2 controllers)

- `SecurityController` : login/logout, formulaire d'authentification
- `ResetPasswordController` : flux complet reset password (request → email → token → reset)

---

## Services applicatifs

### `Tools` (`src/Services/Tools.php`)
Hérite d'`AbstractController` (anti-pattern — un service ne devrait pas hériter d'AbstractController).

| Méthode | Rôle |
|---|---|
| `saveFile(file, directory)` | Upload d'un fichier avec slugification — **doublon de FileService::uploadFile**, non utilisé depuis la migration vers FileManagerBundle |
| `getProperties(class)` | Retourne les propriétés d'une classe via PropertyInfoExtractor |

### `AppExtension` (`src/Twig/AppExtension.php`)
Extension Twig applicative.

| Fonction/Filtre | Rôle |
|---|---|
| `truncate(value, length, after)` | Tronque une chaîne UTF-8 |
| `mobilePhone(value)` | Formate un numéro de téléphone avec espaces |
| `dynamicVariable(el, field)` | Getter dynamique sur une entité depuis un nom de champ string (utilisé pour les listes CRUD génériques) |
| `getTypes(class, prop)` | Retourne le type d'une propriété via PropertyInfoExtractor |
| `countElements(entity)` | Compte les entrées d'un repository (utilisé dans le dashboard admin) |
| `get_class`, `is_array` | Exposition de fonctions PHP natives à Twig |

### `MakeAdminController` (`src/Command/MakeAdminController.php`)
Commande Symfony pour promouvoir un utilisateur en ROLE_ADMIN.

### `CompressingImagesCommand` (`src/Command/CompressingImagesCommand.php`)
Commande `compress:images` — **incomplète et non fonctionnelle** : contient plusieurs `dd()`, utilise `new GdImage($image)` (incorrect — GdImage n'est pas instanciable directement). Code mort à supprimer.

---

## Form Types

Chaque entité a son `Type` dans `src/Form/` et son `Handler` dans `src/Form/Handler/`.

| Form Type | Particularités |
|---|---|
| `MidwifeType` | 5 champs FileManagerBundle (MoustacheFileType/Collection), Select2 pour services |
| `ServiceType` | 1 champ MoustacheFileType, relation Domain |
| `DomainType` | 1 champ MoustacheFileType |
| `HomePageType` | 3 MoustacheFileType + 1 MoustacheFileCollectionType |
| `InformationPageType` | 1 MoustacheFileType, champs texte longs (CKEditor) |
| `UserType` | Email, rôles |
| `DegreeType`, `PathType` | Formulaires simples sans fichier |
| `OfficeType` | Coordonnées cabinet |

---

## Templates Twig

### Hiérarchie

```
base.html.twig
├── front/layouts/layout.html.twig
│   ├── front/homepage.html.twig
│   ├── front/midwife.html.twig
│   ├── front/domain.html.twig
│   └── front/informationUtiles.html.twig
└── admin/layouts/layout.html.twig
    ├── admin/home.html.twig
    ├── admin/crud/index.html.twig       ← liste générique réutilisable
    ├── admin/crud/_form.html.twig       ← formulaire générique
    └── admin/midwife/_form.html.twig    ← formulaire spécifique sage-femme
```

### Composants front réutilisables
- `front/components/midwife-card.html.twig` — carte sage-femme (liste)
- `front/components/midwife-horizontal-card.html.twig` — variante horizontale
- `front/components/service-description.html.twig` — description d'un service
- `front/components/doctolib.html.twig` — bouton/widget Doctolib
- `front/components/doctolib-mini.html.twig` — version compacte

### Templates d'erreur
- `bundles/TwigBundle/Exception/error.html.twig`
- `bundles/TwigBundle/Exception/error404.html.twig`

---

## Assets JavaScript

### Webpack Encore — entrées compilées

| Entrée | Fichier source | Rôle |
|---|---|---|
| `app` | `assets/app.js` | Bootstrap Stimulus |
| `frontStyle` | `assets/styles/front/main.scss` | CSS front |
| `adminStyle` | `assets/styles/admin/main.scss` | CSS admin |
| `frontScript` | `assets/scripts/front/main.js` | JS front |
| `adminScript` | `assets/scripts/admin/main.js` | JS admin |

CKEditor est copié depuis `node_modules/ckeditor/` via `copyFiles` dans webpack.

### Scripts front (`assets/scripts/front/`)

| Fichier | Rôle |
|---|---|
| `main.js` | Point d'entrée : initialise bgMenu, Burger, ShowContactFooter. **Utilise jQuery `$`** |
| `_bgMenu.js` | Couleur de fond du menu au scroll |
| `burger.js` | Menu hamburger mobile |
| `_showContactFooter.js` | Affichage des coordonnées dans le footer |
| `slide-in.js` | Animations d'apparition au scroll |

### Scripts admin (`assets/scripts/admin/`)

| Fichier | Rôle |
|---|---|
| `main.js` | Initialise Select2, ImagePreviewCompress. **Utilise jQuery `$`** |
| `imagePreviewCompress.js` | Prévisualisation d'image avant upload côté admin |

### Librairies JS actives

| Lib | Version | Dépendance jQuery | À remplacer |
|---|---|---|---|
| jQuery | 3.6.0 | — | Oui (directive explicite) |
| Bootstrap | 5.1.3 | Non (BS5 vanilla) | Non |
| Select2 | 4.1.0-rc.0 | **Oui** | Oui (alternative : Tom Select, Choices.js) |
| jQuery UI | 1.13.0 | **Oui** | Oui |
| CKEditor | 4.12.1 | Non | Oui → TinyMCE v6 |
| FontAwesome | 5.15.4 | Non | Évaluer v6 |
| Stimulus | 2.0.0 | Non | Mettre à jour v3+ |

`webpack.config.js` active `.autoProvidejQuery()` — à supprimer lors de la migration.

---

## Configuration

### Sécurité (`config/packages/security.yaml`)
- Provider : `User` entity, property `email`
- Authenticator : form login (`/login`)
- Throttling : 3 tentatives max
- CSRF activé
- Hiérarchie : `ROLE_SUPER_ADMIN > ROLE_ADMIN > ROLE_USER`
- Access control : `/admin` → `ROLE_ADMIN`

### Routes
- Annotations dans les controllers (à migrer en Attributes PHP 8)
- Pas de fichier de routes explicite hors `config/routes.yaml` (import des annotations)

### Docker existant
Le projet contient déjà un `docker-compose.yml` et un `docker-compose.override.yml` — à analyser et améliorer selon la directive.

---

## Problèmes et dettes techniques identifiés

| Problème | Localisation | Priorité |
|---|---|---|
| `dd()` en production | `CompressingImagesCommand`, `MidwifeController::edit()` (commenté), `FileManager::edit()` | Critique |
| `CompressingImagesCommand` non fonctionnelle | `src/Command/CompressingImagesCommand.php` | Code mort à supprimer |
| `Tools` hérite de `AbstractController` | `src/Services/Tools.php` | Mauvaise pratique |
| `Tools::saveFile` doublon de `FileService::uploadFile` | `src/Services/Tools.php` | À supprimer |
| Annotations Doctrine/Route | Toute la couche `src/` et `lib/` | Migration PHP 8 Attributes |
| jQuery utilisé partout en front et admin | `assets/scripts/` | À supprimer (directive) |
| Select2 dépendant de jQuery | `assets/scripts/admin/main.js` | Alternative à choisir |
| CKEditor 4 (EOL) | `package.json`, `webpack.config.js` | Remplacer par TinyMCE v6 |
| `FOSCKEditorBundle` incompatible Symfony 7 | `composer.json` | Supprimer |
| `AppExtension::dynamicVariable` : getter dynamique non typé | `src/Twig/AppExtension.php` | Fragile, à revoir |
| `Security` deprecated Sf6+ dans `MoustacheExtension` | `lib/.../Twig/MoustacheExtension.php` | Migrer vers `AuthorizationCheckerInterface` |
| `PHP_OS === 'Linux'` pour séparateur | `lib/.../Services/FileService.php` et autres | Utiliser `DIRECTORY_SEPARATOR` |
| Chemins hardcodés `../public/` | `lib/.../Services/ImageService.php` | Utiliser `kernel.project_dir` |

---

## Mentions légales — obligations professionnelles sage-femme

À implémenter dans `InformationPage` ou en pied de page :

| Information | Obligatoire |
|---|---|
| Numéro RPPS | Oui (identifiant national professionnel de santé) |
| Numéro ADELI | Oui (ou RPPS selon région) |
| Assurance RCP (Responsabilité Civile Professionnelle) | Oui |
| Diplôme d'État de sage-femme | Recommandé |
| Ordre des Sages-Femmes (numéro ordinal) | Recommandé |
| SIRET (si activité libérale) | Oui |
| Mentions RGPD (formulaires, cookies, Matomo) | Oui |
| Politique de cookies (Matomo) | Oui — Matomo nécessite consentement ou mode anonymisé |

**Champ `mention` de `InformationPage` est un string(255) nullable — insuffisant pour RPPS+ADELI+RCP.** Prévoir des champs dédiés dans `Midwife` ou une section dédiée.

---

## Résumé des fonctionnalités côté public

1. **Page d'accueil** : présentation du cabinet, liste des sage-femmes, liste des domaines, coordonnées
2. **Fiche sage-femme** : biographie, photo, parcours, diplômes, services, lien Doctolib
3. **Page domaine** : liste des services du domaine avec leurs sage-femmes
4. **Page Informations utiles** : tarifs, mentions légales, liens, actualités à venir

## Résumé des fonctionnalités côté admin

1. **Gestion des sage-femmes** : CRUD complet avec photos, galerie, parcours, diplômes, services
2. **Gestion des services et domaines** : CRUD avec photos, positionnement
3. **Édition singletons** : HomePage, InformationPage, Office
4. **Gestion fichiers** : FileManagerBundle (upload, compression, resize, organisation dossiers)
5. **Gestion utilisateurs** : CRUD, changement rôles
6. **Reset password** : flux email complet
