# Frontend — décisions et plan de migration

## Évaluation AssetMapper vs Webpack Encore

### Verdict : migration vers AssetMapper après Rector

| Critère | Webpack Encore | AssetMapper |
|---|---|---|
| Node.js requis | Oui (build) | Non (prod), optionnel (dev SCSS) |
| SCSS | Natif | Via `symfonycasts/sass-bundle` |
| Symfony 7 recommandé | Non | **Oui (défaut)** |
| TinyMCE self-hosted | Copie assets webpack | Importmap |
| Bootstrap 5 | Oui | Oui |
| Complexité config | Élevée (webpack.config.js) | Faible |
| jQuery requis | Non (retiré) | Non |

**Conclusion** : AssetMapper est le bon choix pour ce projet. Le JS est simple (pas de bundling complexe), jQuery est retiré, Bootstrap 5 et TinyMCE v6 sont compatibles. La migration sera effectuée après la migration Symfony 7.4 (AssetMapper requiert Symfony 6.3+).

**Dépendance à ajouter pour SCSS avec AssetMapper :**
```
composer require symfonycasts/sass-bundle
```

---

## État actuel (branche `asset-frontend`)

### Suppressions

| Supprimé | Remplacé par |
|---|---|
| jQuery 3.6 | Vanilla JS |
| Select2 | Tom Select ^2.3 |
| CKEditor 4 + FOSCKEditorBundle | TinyMCE v6 (self-hosted webpack) |
| jquery-ui / jquery-ui-bundle | — (non utilisé en pratique) |
| node-sass | sass (dart sass) |
| popper.js v1 | @popperjs/core v2 (déjà présent) |
| fontawesome-free | — (doublon de @fortawesome/*) |
| exports-loader, file-loader, regenerator-runtime | Webpack 5 / Babel gèrent nativement |
| stimulus v2 | @hotwired/stimulus ^3.0 |

### Ajouts

| Ajouté | Rôle |
|---|---|
| Tom Select ^2.3 | Sélecteur multi sans jQuery |
| TinyMCE ^6.8 | Éditeur rich text self-hosted |
| @hotwired/stimulus ^3.0 | Framework JS léger (Symfony UX) |
| sass ^1.86 | Compilateur SCSS (dart sass) |

### Mises à jour

| Package | Avant | Après |
|---|---|---|
| @symfony/webpack-encore | ^1.0 | ^4.0 |
| @symfony/stimulus-bridge | ^2.0 | ^3.0 |
| bootstrap | ^5.1.3 | ^5.3 |
| @fortawesome/* | ^1.2.36/^5.15.4 | ^1.3/^5.15 |
| sass-loader | ^12.3 | ^16.0 |

---

## JS front — changements

### `front/main.js`
- `$(document).ready()` → `document.addEventListener('DOMContentLoaded', ...)`

### `front/burger.js`
- Réécriture complète en vanilla JS
- Sélecteurs `$('.js-xxx')` → `document.querySelector/querySelectorAll`
- `.on('click', ...)` → `.addEventListener('click', ...)`
- `.addClass/.removeClass/.hasClass` → `.classList.add/remove/contains`
- `.next/.closest/.siblings` → `.nextElementSibling/.closest/.querySelectorAll`

### `front/slide-in.js`
- Plugin jQuery `$.fn.visible` + `$(window).scroll()` → **IntersectionObserver**
- Même approche que `_bgMenu.js` (déjà vanilla)
- `already-visible` appliqué immédiatement si dans le viewport, `come-in` via observer

### `front/_bgMenu.js`, `front/_showContactFooter.js`
- Déjà en vanilla JS — aucun changement

---

## JS admin — changements

### `admin/main.js`
- `$(document).ready()` → `document.addEventListener('DOMContentLoaded', ...)`
- `$('.select2').select2()` → `new TomSelect(el, { plugins: ['remove_button'] })`
- Initialisation TinyMCE sur `textarea.tinymce`
- Guard sur `#compressImage` avant d'init `ImagePreviewCompress`

### `admin/imagePreviewCompress.js`
- Déjà en vanilla JS (XMLHttpRequest) — aucun changement

---

## TinyMCE — intégration

### Phase actuelle (webpack)

TinyMCE est self-hosted via webpack `copyFiles` :
- Assets copiés dans `public/build/tinymce/`
- Initialisation avec `base_url: '/build/tinymce'` et `suffix: '.min'`
- Langue française : fichier `fr_FR` à télécharger depuis tinymce.com/download/language-packages et placer dans `public/build/tinymce/langs/`

### Form Types modifiés

| Form Type | Champ(s) | Avant | Après |
|---|---|---|---|
| `MidwifeType` | `description` | `CKEditorType` | `TextareaType` + `class=tinymce` |
| `ServiceType` | `description` | `CKEditorType` | `TextareaType` + `class=tinymce` |
| `InformationPageType` | `legal`, `coming`, `price`, `links` | `CKEditorType` | `TextareaType` + `class=tinymce` |

### Phase AssetMapper (après Rector)

```php
// importmap.php
return [
    'tinymce' => [
        'path' => '/build/tinymce/tinymce.min.js',
    ],
];
```

Ou via `symfonycasts/asset-mapper` avec `php bin/console importmap:require tinymce`.

---

## Plan migration AssetMapper (après Rector Symfony 7.4)

1. `composer require symfony/asset-mapper`
2. `composer require symfonycasts/sass-bundle` (pour SCSS)
3. Migrer les entrées webpack → `assets/app.js` avec importmap
4. `php bin/console importmap:require bootstrap @hotwired/stimulus tom-select`
5. TinyMCE self-hosted via importmap local
6. Supprimer `webpack.config.js`, `package.json` (ou garder minimal)
7. Mettre à jour les templates (suppression des `{{ encore_entry_script_tags }}`)

---

## Note sur FontAwesome

Les icônes FontAwesome v5 sont conservées pour éviter les breaking changes (certains noms d'icônes changent en v6). La migration vers v6 devra être faite avec vérification des templates Twig.
