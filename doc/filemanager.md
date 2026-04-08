# Analyse du FileManagerBundle

Bundle custom localisé dans `/lib/FileManagerBundle/`, namespace `Moustache\FileManagerBundle`.

---

## Rôle général

Interface d'administration pour gérer des fichiers (images, PDF, vidéos, iframes YouTube) uploadés sur le serveur.
Deux espaces : public (`/public/uploads/`) et sécurisé (`/securedSpace/`).

---

## Architecture

### Entités (2)

#### `File`
Table de métadonnées pour chaque fichier stocké.

| Champ | Type | Rôle |
|---|---|---|
| `id` | int | PK |
| `title` | string(255) nullable | Titre humain |
| `filename` | string(255) | Nom physique du fichier (slugifié + uniqid) |
| `directory` | string(255) | Chemin relatif depuis `/public` |
| `description` | string(255) nullable | Description |
| `slug` | string(255) unique | Généré depuis `title` via Gedmo |
| `alt` | string(255) nullable | Attribut alt pour les images |
| `isVideo` | bool nullable | Fichier vidéo local |
| `isIframe` | bool nullable | Embed YouTube |
| `videoUrl` | string(500) nullable | URL YouTube |
| `isChangingDirectory` | bool nullable | Flag déplacement en cours |
| `createdAt`, `updatedAt` | datetime | Via `TimestampableEntity` |

Méthode utilitaire `getPath()` : retourne `directory/filename`.

#### `DirectoryAccess`
Contrôle d'accès par rôles sur les dossiers de `securedSpace`.

| Champ | Type | Rôle |
|---|---|---|
| `id` | int | PK |
| `dirName` | string | Nom du dossier |
| `dirPath` | string | Chemin relatif depuis la racine projet |
| `roles` | array | Rôles Symfony autorisés |
| `createdAt`, `updatedAt` | datetime | Via `TimestampableEntity` |

---

### Services (2)

#### `FileService`

| Méthode | Rôle |
|---|---|
| `uploadFile(UploadedFile, directory)` | Upload principal : slugifie le nom, crée le thumbnail, redimensionne et compresse si nécessaire, déplace le fichier |
| `safeFilename(UploadedFile)` | Retourne un nom sûr : slug + uniqid + extension |
| `updateFiles(string)` | Synchronise la BDD avec les fichiers physiques présents dans `/uploads` ou `/securedSpace` |
| `showPrivateImage(path, filename, display)` | Stream d'un fichier sécurisé via `BinaryFileResponse` (inline ou attachment) |
| `getImagePathFromPublic(id)` | Retourne l'entité `File` par son id |
| `scaleImageFileToBlob(file)` | Redimensionne une image en 200x200 max et retourne un blob (utilisé pour les thumbnails en mémoire) |

**Constantes de seuil dans `ImageService` :**
- `MAX_WEIGHT` : 200 000 octets (200 kB) — au-delà, compression déclenchée
- `MAX_WIDTH` : 2000 px / `MAX_HEIGHT` : 1000 px — au-delà, redimensionnement déclenché
- `MIN_RATIO_WEIGHT` : 20 (qualité minimum)
- `THUMB_SIZE` : 200 px (largeur thumbnail)

#### `ImageService`

| Méthode | Rôle |
|---|---|
| `makeThumb(UploadedFile, newfilename)` | Crée un thumbnail 200px dans `/public/thumbs/` |
| `getRatioResize(file)` | Calcule le ratio de redimensionnement si dimensions dépassées |
| `getRatioWeight(file)` | Calcule la qualité de compression si poids dépassé |
| `compressUploadedImage(file, quality)` | Compresse le fichier temporaire avant déplacement (GD, JPEG/GIF/PNG) |
| `resizeUploadedImage(file, ratioResize, ratioWeight)` | Redimensionne le fichier temporaire avant déplacement |
| `compressImage(File, quality)` | Compresse un fichier déjà uploadé (via chemin `../public/...`) |
| `resizeImage(File, opt)` | Redimensionne un fichier déjà uploadé (JPEG uniquement) |
| `transformSize(int)` | Formate un poids en B / kB / MB |
| `getDetailsOfFile(File)` | Retourne poids, dimensions, mime, nom d'un fichier (JPG/PNG/MP4) |

**Note :** `compressImage` et `resizeImage` utilisent le chemin hardcodé `../public/...`, ce qui suppose une exécution depuis `/public`. Fragile.

---

### Controllers (5)

#### `FileManager` — `/admin/gestionnaire-de-fichier/`
Interface principale espace public.

| Route | Action |
|---|---|
| `GET/POST /` | Liste fichiers/dossiers, upload, création/renommage/déplacement dossier, refresh BDD |
| `POST /compress/{id}` | Compresse un fichier existant |
| `POST /resize/{id}` | Redimensionne un fichier existant |
| `GET/POST /edit/{id}` | Édition des métadonnées d'un fichier |
| `DELETE /deleteFolder` | Supprime un dossier et ses entrées BDD |
| `DELETE /{id}` | Supprime un fichier (physique + BDD) |

**Note :** dans `edit()`, un `dd($quality)` est présent (debug non supprimé).

#### `PrivateFileManagerController` — `/admin/gestionnaire-de-fichier-prive/`
Même fonctionnalités pour l'espace sécurisé (`/securedSpace/`).

#### `IframeFileManager` et `IframeSecuredFileManagerController`
Interfaces iframe pour sélection de fichier depuis les formulaires (MoustacheFileType).

#### `MoustacheApiController`
API JSON utilisée par les widgets de sélection de fichier dans les formulaires.

---

### Form Types (utilisés depuis l'app)

| Type | Rôle | Usage dans l'app |
|---|---|---|
| `MoustacheFileType` | Champ de sélection d'un seul fichier (id → entité File) | Midwife (4x), Service (1x), Domain (1x), HomePage (3x), InformationPage (1x) |
| `MoustacheFileCollectionType` | Collection de fichiers | Midwife (pictures), HomePage (pictures) |

Ces types ouvrent une iframe vers le file manager pour sélectionner un fichier ; ils stockent l'id de l'entité `File`.

---

### Twig Extension `MoustacheExtension` (17 fonctions)

| Fonction Twig | Rôle |
|---|---|
| `getFileFromValue(id)` | Retourne l'entité File |
| `getRelativePathFromValue(id)` | Chemin relatif depuis `/public` (ex: `/uploads/monimage.jpg`) |
| `getAbsolutePathFromValue(id)` | Chemin absolu système |
| `getExtension(File)` | Extension du fichier (ou `'youtube'` si iframe) |
| `getExtensionById(id)` | Idem par id |
| `renderViewFile(file, class)` | HTML complet selon le type : `<img>`, `<video>`, `<embed>`, `<iframe>`, icône FontAwesome |
| `renderViewFileForModal(file, class)` | Idem, variante pour affichage en modal |
| `renderViewFileForList(id, class, datasetId)` | Représentation compacte pour liste (thumbnail ou icône) |
| `renderThumbNail(id, class, datasetId)` | Thumbnail 200px depuis `/public/thumbs/` |
| `renderSecuredFileForList(File)` | URL stream pour fichiers sécurisés (si droits OK) |
| `imgOrFont(File)` | Booléen : `true` si image, `false` si autre type (pour conditionnels twig) |
| `downloadPrivateFile(File)` | `BinaryFileResponse` pour téléchargement depuis securedSpace |
| `getDirectoryAccess(SplFileInfo)` | Entité `DirectoryAccess` d'un dossier |
| `getRolesAuthorized(SplFileInfo)` | Booléen : l'utilisateur courant a-t-il accès au dossier ? |
| `getRoles()` | Liste des rôles de l'utilisateur courant |
| `getDetailsOfFile(File)` | Poids + dimensions (délégué à ImageService) |
| `getDetails(File)` | Idem depuis `/public/thumbs/` |

---

### Event Listener

`FileListener` — écoute les changements de répertoire sur l'entité `File` via Doctrine (probablement pour mettre à jour `DirectoryAccess` lors d'un déplacement).

---

## Usages dans l'application

### Entités avec relations vers `File`

| Entité | Champs | Type relation |
|---|---|---|
| `Midwife` | `picture`, `bgCard`, `bgTitle`, `pictureSelf` | ManyToOne File |
| `Midwife` | `pictures` | ManyToMany File |
| `Service` | `picture` | ManyToOne File |
| `Domain` | `titleBg` | ManyToOne File |
| `HomePage` | `backgroundImage1`, `backgroundImage2`, `titleBg` | ManyToOne File |
| `HomePage` | `pictures` | ManyToMany File |
| `InformationPage` | `titleBg` | ManyToOne File |

### Form Types de l'app utilisant le bundle

| Form Type | Champs concernés |
|---|---|
| `MidwifeType` | `picture`, `bgCard`, `bgTitle`, `pictureSelf` (MoustacheFileType), `pictures` (MoustacheFileCollectionType) |
| `ServiceType` | `picture` (MoustacheFileType) |
| `DomainType` | `titleBg` (MoustacheFileType) |
| `HomePageType` | `backgroundImage1`, `backgroundImage2`, `titleBg` (MoustacheFileType), `pictures` (MoustacheFileCollectionType) |
| `InformationPageType` | `titleBg` (MoustacheFileType) |

---

## Partie sécurisée — évaluation

La partie sécurisée (`/securedSpace/`, `PrivateFileManagerController`, `IframeSecuredFileManagerController`) permet de stocker des fichiers hors de `/public` et de les servir via stream après vérification des rôles.

**Aucune des entités métier de l'app ne référence des fichiers sécurisés.** Toutes les relations `File` pointent vers des fichiers dans `/uploads` (espace public). La partie sécurisée semble inutilisée dans l'app actuelle. **À confirmer avec le propriétaire avant suppression.**

---

## Problèmes identifiés (à corriger lors de la migration)

| Problème | Localisation | Sévérité |
|---|---|---|
| `dd($quality)` en production | `FileManager::edit()` ligne 207 | Bloquant |
| Chemin hardcodé `../public/...` | `ImageService::compressImage()`, `resizeImage()`, `getDetailsOfFile()` | Élevée |
| `PHP_OS === 'Linux'` pour détecter le séparateur | Partout | Moyenne (utiliser `DIRECTORY_SEPARATOR`) |
| `imagejpeg` sans vérification d'existence de `$image` | `ImageService::compressImage()` | Moyenne |
| Annotations Doctrine/Route → à migrer en Attributes PHP 8 | Tout le bundle | Élevée |
| `Security` deprecated en Sf6+ (utiliser `AuthorizationCheckerInterface`) | `MoustacheExtension` | Élevée |
| `getRatioResize()` utilise `MAX_WEIGHT` au lieu de `MAX_WIDTH` comme diviseur | `ImageService::getRatioResize()` ligne 58 | Bug potentiel |

---

## Proposition de remplacement

Les fonctionnalités réellement utilisées par l'app se résument à :

1. **Upload d'image** avec slugification du nom, compression et redimensionnement automatique, génération de thumbnail
2. **Sélection d'un fichier existant** dans un formulaire Symfony (widget iframe → id stocké en BDD)
3. **Affichage d'une image** dans les templates Twig depuis son id

Le file manager (navigation dossiers, rename, move, delete) n'est qu'une interface d'administration, remplaçable par un composant plus simple (VichUploaderBundle ou implémentation directe).

**Plan suggéré pour le remplacement :**
- Conserver l'entité `File` (ou la simplifier)
- Remplacer `MoustacheFileType` par un champ upload direct avec prévisualisation
- Remplacer les fonctions Twig par des helpers simples (chemin depuis l'id)
- Intégrer la compression/resize dans un service dédié de l'app
- Supprimer la partie sécurisée si confirmée inutile
