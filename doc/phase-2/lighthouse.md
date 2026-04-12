# Lighthouse — Analyse et plan de développement

---

## Historique des audits

| Audit | Date | Env | Device | Perf | Access. | Best P. | SEO | LCP | FCP |
|-------|------|-----|--------|------|---------|---------|-----|-----|-----|
| Initial | 2026-04-11 | dev | Desktop | 66/100 | 70/100 | 100/100 | 58/100 | 5.6 s | 5.1 s |
| Après sessions I + J | 2026-04-12 | dev | Desktop | **94/100** | **89/100** | 100/100 | 58/100 | **1.2 s** | **1.2 s** |
| Initial | 2026-04-12 | dev | Mobile | 69/100 | 82/100 | 100/100 | 58/100 | 5.1 s | 4.7 s |
| Prod (APP_DEBUG=1) | 2026-04-12 | prod | Desktop | 98/100 | 93/100 | 100/100 | 66/100 | 0.9 s | 0.9 s |
| Prod (APP_DEBUG=1) | 2026-04-12 | prod | Mobile | 73/100 | 95/100 | 100/100 | 66/100 | 4.6 s | 4.4 s |
| **Prod corrigée** ¹ | 2026-04-12 | prod | Desktop | **98/100** | **95/100** | **96/100** | **100/100** | **0.9 s** | **0.8 s** |
| **Prod corrigée** ¹ | 2026-04-12 | prod | Mobile | **72/100** | **100/100** | **96/100** | **100/100** | **4.7 s** | **4.4 s** |
| **Prod avec photos** ² | 2026-04-12 | prod | Desktop | **97/100** | **100/100** | **100/100** | **100/100** | **1.0 s** | **0.9 s** |
| **Prod avec photos** ² | 2026-04-12 | prod | Mobile | **66/100** | **100/100** | **100/100** | **100/100** | **5.8 s** | **5.0 s** |

> ¹ **Prod corrigée** = `APP_DEBUG=0` + corrections heading-order/contrast/list appliquées, sans photos uploadées.
> ² **Prod avec photos** = images réelles uploadées (carousel, fiches sage-femme). `ImageUploadService` convertit automatiquement en WebP — images bien optimisées (max 64 Ko). La dégradation mobile (72→66, −6 pts, LCP +1.1 s) est due au chargement des images sur réseau simulé 3G, pas à leur format. Cause principale restante : J5 (CSS render-blocking).

---

## Analyse desktop — audit initial (2026-04-11)

### SEO — 58/100 (score artificiel en dev)

Les deux échecs sont des artefacts de l'environnement de développement :

- **`X-Robots-Tag: noindex`** : header envoyé par la config dev/test — absent en prod.
- **Liens non crawlables** (`javascript:void(0)`, `file://`) : générés par la debug toolbar Symfony — absents en prod.

**En production, ces deux points disparaissent.** Un audit en prod est nécessaire pour le score définitif (K1).

---

### Accessibilité — 70/100

| Priorité | Audit | Élément | Diagnostic |
|---|---|---|---|
| haute | `meta-viewport` | `<meta name="viewport">` | `maximum-scale=1, user-scalable=0` bloque le zoom — violation RGAA/WCAG |
| haute | `color-contrast` | Footer bottom `.footer-bottom-inner` | `#6e8078` sur `#15251b` = ratio 3.82:1, requis 4.5:1 |
| haute | `link-name` | `a.midwife-card-picture` | Lien image sans texte ni `aria-label` |
| haute | `link-name` | `a.btn.waze` | Bouton Waze (icône seule) sans `aria-label` |
| haute | `aria-command-name` | `div#menu-icon` (burger) | `role="button"` sans `aria-label` |
| moyenne | `frame-title` | `section#map iframe` Google Maps | `<iframe>` sans attribut `title` |
| moyenne | `list` | `ul.pushNav > hr` (nav burger) | `<hr>` enfant direct de `<ul>` — HTML invalide |
| faible | `heading-order` | Footer `<h5>` | Niveau potentiellement sauté selon le contexte |

#### Artefacts dev (ignorables)

- `button-name` sur le bouton toggle Symfony toolbar
- `link-name` sur les liens `_profiler`
- `color-contrast` sur le badge HTTP 200 de la toolbar

---

### Performance — 66/100

#### Problèmes critiques

**LCP très dégradé (score 0.17)** — l'image hero est un `background-image` CSS sur une `<div>` :
- Non découvrable par le préchargeur du navigateur
- Pas de `fetchpriority="high"` ni de `<link rel="preload">`
- LCP 5.6 s, FCP 5.1 s

**CSS render-blocking** — 4 feuilles bloquent le rendu :

| Ressource | Délai bloquant |
|---|---|
| Bootstrap CSS (232 Ko) | 4062 ms |
| FontAwesome CSS (76 Ko) | 2262 ms |
| CSS app front (34 Ko) | 1212 ms |
| Google Fonts (`@import` dans SCSS) | 881 ms |

**CSS/JS inutilisés :**

| Ressource | Inutilisé | % |
|---|---|---|
| Bootstrap CSS | 222 Ko | 95.9 % |
| FontAwesome CSS | 74 Ko | 98.1 % |
| Bootstrap JS | 44 Ko | 73.9 % |
| CSS app front | 22 Ko | 66.5 % |

**CSS app non minifié** — 12 Ko économisables (à vérifier en prod, cf. J4).

---

### Best Practices — 100/100

Aucun problème. À maintenir.

---

## Analyse prod local (2026-04-12)

### Desktop prod — 98/100

Excellents résultats. LCP 0.9 s, TBT 0 ms, CLS 0.008.

Problèmes restants identifiés :

| Audit | Problème | Cause | Fix |
|---|---|---|---|
| `is-crawlable` | SEO 66 → noindex | `APP_DEBUG=1` active `disallow_search_engine_index` | `APP_DEBUG=0` dans `.env.local` lors des audits prod |
| `color-contrast` | `<a class="active">` nav sticky | `$color-accent` (#5a8c6a) sur `$color-bg-warm` (#fafaf8) = 3.84:1 < 4.5:1 | **corrigé** : `$color-primary` (#3a6b4e) = 5.9:1 ✓ |
| `heading-order` | `<h4 class="footer-section-title">` | homepage : dernier h2 contenu → h4 footer = saut h2→h4 | **corrigé** : footer h4 → h3 |
| `unminified-css` | 13 Ko économisables | AssetMapper ne minifie pas localement | Vérifier en prod réelle (o2switch) — J4 |
| `unused-css` | 316 Ko Bootstrap + FA | Import complet | Phase 3 — J5 |
| `render-blocking` | Google Fonts 243 ms + FA 414 ms + Bootstrap 694 ms | CSS render-blocking | Phase 3 — J5 |

### Mobile prod avec photos — 66/100

Audit avec photos réelles (carousel + fiches sage-femme). Accessibilité 100/100 ✓, SEO 100/100 ✓, Best Practices 100/100 ✓.

**Bonne nouvelle** : `ImageUploadService` convertit toutes les images uploadées en WebP automatiquement (confirmé : `.jpg`/`.jpeg`/`.png` → `.webp`). Tailles max observées : 64 Ko. L2 est résolu de facto.

| Audit | Problème | Cause | Fix |
|---|---|---|---|
| `unused-css-rules` | 304 Ko (savings ~1 650 ms) | Bootstrap 95.9% + FA 98.1% + front.css 55.7% inutilisés | Phase 3 — J5 |
| `unused-javascript` | 44 Ko | Bootstrap JS 73.9% inutilisé | Phase 3 — J5 |

> **Perf mobile 66** : le LCP 5.8 s est dû à la combinaison render-blocking CSS (304 Ko) + chargement images sur réseau 3G simulé. Les images elles-mêmes sont bien optimisées (WebP, ≤ 64 Ko). Sans J5, ce score ne bougera pas. En Wi-Fi réel, le ressenti est bien meilleur.

---

## Analyse desktop — après corrections I + J (2026-04-12)

### Gains obtenus

| Métrique | Avant | Après | Gain |
|---|---|---|---|
| Performance | 66/100 | **94/100** | +28 pts |
| Accessibilité | 70/100 | **89/100** | +19 pts |
| LCP | 5.6 s | **1.2 s** | −4.4 s |
| FCP | 5.1 s | **1.2 s** | −3.9 s |
| CLS | 0 | 0.008 | stable |

**Causes des gains performance :**
- J1 : `<link rel="preload" as="image" fetchpriority="high">` sur l'image hero → LCP découvrable par le préchargeur
- J2 : Google Fonts sorti du `@import` SCSS (render-blocking) → `<link rel="preconnect">` + `<link rel="stylesheet">` dans `base.html.twig`

**Causes des gains accessibilité :**
- I1 à I8 : toutes les corrections ciblées appliquées (voir plan ci-dessous)

### Ce qui reste à 89 (et non 100)

Le score accessibilité desktop 89/100 reflète probablement :
- Des artefacts Symfony toolbar encore présents (ignorables)
- Potentiellement l'ordre des headings (h4 footer après h2 contenu, sans h3 intermédiaire selon le contexte de page)
- B9/B10 non encore vérifiés (contrastes custom, focus visible)

---

## Analyse mobile — audit initial (2026-04-12)

> Audit pris sur l'environnement dev après les corrections I/J (les fixes render-blocking sont déjà appliqués). Les scores LCP/FCP élevés reflètent principalement la simulation réseau mobile + CPU throttling, **et** les 322 Ko de CSS inutilisés (Bootstrap + FA).

### Scores mobiles

| Catégorie | Score | Métriques clés |
|---|---|---|
| Performance | 69/100 | LCP 5.1 s, FCP 4.7 s, TBT 0 ms, CLS 0.038, Speed Index 4.7 s |
| Accessibilité | 82/100 | — |
| Best Practices | 100/100 | — |
| SEO | 58/100 | artefact dev |

### Accessibilité mobile — problèmes identifiés

| Verdict | Audit | Détail |
|---|---|---|
| artefact dev | `button-name` | Bouton Symfony toolbar (ignorable) |
| artefact dev | `color-contrast` | Badge HTTP Symfony toolbar (ignorable) |
| artefact dev | `link-name` | Liens `_profiler` Symfony (ignorable) |
| **à vérifier** | `heading-order` | `<h4 class="footer-section-title">` — possible saut h2→h4 sur certaines pages |
| **déjà corrigé** | `list` | `<hr>` dans `<ul.pushNav>` — fix I7 (nav-separator) |

> Le score mobile 82 (vs desktop 89) s'explique principalement par les artefacts dev restants et potentiellement l'ordre des headings à vérifier en prod.

### Performance mobile — analyse

**Blocage principal : CSS/JS inutilisés (322 Ko)**

| Ressource | Inutilisé | % |
|---|---|---|
| Bootstrap CSS | 222 Ko | 95.9 % |
| FontAwesome CSS | 74 Ko | 98.1 % |
| Bootstrap JS | 44 Ko | 73.9 % |

Ce point est structurel : c'est le chantier J5 (phase 3). Sans purger Bootstrap et FA, le LCP mobile restera pénalisé par les 4+ secondes de render-blocking réseau simulé.

**Les corrections J1/J2 ont vraisemblablement amélioré le LCP mobile**, mais un nouvel audit après commit est nécessaire pour mesurer le gain réel. En environnement prod (minification + cache), le gain sera encore plus marqué.

**Autres points performance mobile :**

| Audit | Score | Note |
|---|---|---|
| `render-blocking-insight` | FAIL | Bootstrap/FA principalement — chantier J5 |
| `unused-css-rules` | 0/100 | 322 Ko → J5 phase 3 |
| `unused-javascript` | 0/100 | 44 Ko Bootstrap JS → J5 phase 3 |
| `unminified-css` | FAIL | 12 Ko économisables — disparaît en prod (J4) |
| `font-display-insight` | 50/100 | FA webfonts sans `font-display: swap` — impact mesuré < 5ms (J3, décidé non traité) |

---

## Plan de développement

> Légende : `[ ]` à faire — `[x]` fait — `[-]` décidé : non traité

### I. Accessibilité — corrections ciblées

- [x] **I1** — Supprimer `maximum-scale=1` et `user-scalable=0` du `<meta name="viewport">` dans `base.html.twig`
- [x] **I2** — Contraste footer bottom corrigé : `.footer-bottom-inner` passe de `$color-footer-muted` à `$color-footer-link` (`#a8b8ae`, ratio 8.65:1 sur `#15251b`, WCAG AA ✓)
- [x] **I3** — `aria-label="Voir la fiche de {{ midwife.firstName }} {{ midwife.lastName }}"` ajouté sur `a.midwife-card-picture` dans `midwife-card.html.twig`
- [x] **I4** — `aria-label="Ouvrir dans Waze"` ajouté sur le lien Waze dans `homepage.html.twig`
- [x] **I5** — `aria-label="Ouvrir le menu"` ajouté sur `div#menu-icon` dans `header.html.twig` ; mis à jour dynamiquement ("Fermer le menu") dans `burger.js`
- [x] **I6** — `title="Plan d'accès au cabinet"` ajouté sur l'`<iframe>` Google Maps dans `homepage.html.twig`
- [x] **I7** — `<hr>` enfant direct de `<ul class="pushNav">` remplacé par `<li class="nav-separator" aria-hidden="true"><hr></li>` dans `header.html.twig` (initialement `role="separator"`, remplacé par `aria-hidden` — axe-core flagge le `role` comme invalide pour le contexte listitem) ; style dans `_header.scss`
- [x] **I8** — Footer h5→h4 (session précédente) puis **h4→h3** (audit prod : saut h2→h4 confirmé sur homepage, dernier h2 contenu → h4 footer) ; séquence finale h1→h2→h3 ✓ ; `.footer-section-title` utilise un sélecteur de classe, rendu inchangé

### J. Performance — gains immédiats

- [x] **J1** — `<link rel="preload" as="image" fetchpriority="high">` ajouté dans `{% block preload %}` de `homepage.html.twig` ; bloc vide déclaré dans `base.html.twig`
- [x] **J2** — Google Fonts sorti du `@import` SCSS (render-blocking) → `<link rel="preconnect">` + `<link rel="stylesheet">` dans `base.html.twig` ; `display=swap` présent dans l'URL
- [-] **J3** — FA `font-display: swap` : impact mesuré à < 5ms dans le rapport mobile — trop marginal pour re-déclarer tous les `@font-face` vendorisés
- [ ] **J4** — Vérifier minification CSS/JS en prod (`APP_ENV=prod`) et documenter les scores
- [-] **J5** — PurgeCSS / allègement Bootstrap + FA : gain ~300 Ko CSS, ~50 Ko JS — chantier architectural, reporté phase 3

### K. SEO — vérification prod

- [x] **K1** — Audit prod réalisé (APP_DEBUG=0) : Desktop 98/100, Mobile 72/100, SEO **100/100** ✓ — voir tableau historique
- [x] **K2** — `X-Robots-Tag: noindex` absent confirmé : SEO 100/100 en prod avec `APP_DEBUG=0` (Symfony n'active plus `disallow_search_engine_index`)

### L. Images — impact mesuré

- [x] **L1** — Audit avec photos réelles réalisé : Desktop 97/100, Mobile 66/100 (LCP 5.8 s). Dégradation mobile : −6 pts vs sans photos, entièrement due au réseau simulé + CSS render-blocking, pas aux images elles-mêmes.
- [x] **L2** — `ImageUploadService` convertit automatiquement en WebP (confirmé sur upload). Images ≤ 64 Ko. Aucune action nécessaire.
- [ ] **L3** — Étendre le `{% block preload %}` aux pages avec image hero (fiche sage-femme, blog index) — J1 ne couvre que la homepage

---

## Priorisation restante

| Priorité | Tâche | Effort | Impact |
|---|---|---|---|
| 1 | **B10** | faible | focus visible sur éléments interactifs |
| 2 | **J4** | faible | confirmer minification CSS en prod réelle o2switch |
| 3 | **L3** | faible | preload LCP étendu fiche sage-femme + blog |
| 4 | **J5** | très élevé | phase 3 — PurgeCSS/refonte Bootstrap (−304 Ko CSS, +~15 pts mobile attendus) |
