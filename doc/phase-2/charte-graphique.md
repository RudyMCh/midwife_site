# Charte graphique — Phase 2

_Rédigé le 2026-04-10 — à partir de l'analyse du public cible et de l'existant_

---

## Contexte

Le site s'adresse principalement à des femmes, femmes enceintes, jeunes parents.
Le registre attendu dans ce secteur : **douceur, confiance, bienveillance, naturel**.
La palette historique (bleu marine `#232187` + rose vif `#fd3f92` + violet `#800080` + vert fluo
sur le bouton téléphone) manquait d'unité et de chaleur.

---

## Options étudiées

### Option A — Rose poudré / crème (classique périnatal)

Ambiance chaude et douce, très utilisée dans l'univers maternité.

| Rôle | Nom | Hex |
|---|---|---|
| Primaire (CTA, bordures) | Rose poudré profond | `#c9748f` |
| Fond doux | Beige chaud | `#f5ede6` |
| Accent / hover | Bordeaux doux | `#8b3a52` |
| Fond principal | Crème | `#fdf8f5` |
| Texte corps | Brun foncé | `#3d2b2b` |
| CTA fort (téléphone) | Vert sauge | `#6a9e7f` |
| Footer | Bordeaux nuit | `#2d1019` |

**Typographie :** Cormorant Garamond (titres sémantiques) + Lato (corps)

**Pour :** très reconnaissable dans le secteur, chaleureux.  
**Contre :** risque de paraître daté, peu différenciant.

---

### Option B — Blanc cassé / vert sauge / brun-ocre ✅ **Retenu**

Ambiance naturelle et épurée, intemporelle, adaptée à tous les âges du public.

| Rôle | Nom | Hex |
|---|---|---|
| Primaire (CTA, bordures, fonds forts) | Vert forêt | `#3a6b4e` |
| Accent / hover nav | Vert sauge foncé | `#5a8c6a` |
| Titres Tangerine (décoratifs) | Brun-ocre chaud | `#a06035` |
| CTA fort (bouton téléphone) | Corail chaud | `#e09070 → #b85030` |
| Section service (fond sombre) | Vert forêt (= primaire) | `#3a6b4e` |
| Fond principal | Blanc cassé | `#fafaf8` |
| Fond sections alternées | Beige clair | `#f0ede8` |
| Fond sections sage-femmes | Beige rosé | `#e8d5c4` |
| Texte corps | Gris foncé chaud | `#2d2d2d` |
| Footer fond | Vert nuit | `#1a2e22` |
| Footer liens | Gris-vert pâle | `#a8b8ae` |
| Footer texte secondaire | Gris-vert sombre | `#6e8078` |
| Doctolib (partenaire, imposé) | Bleu Doctolib | `#107aca` |

**Typographie :**

| Rôle | Police | Utilisé sur |
|---|---|---|
| Décoratif grand format | Tangerine (700) | `h1` hero, `h2` titres de sections |
| Titres sémantiques | Playfair Display (400/600/700, italic 400) | `h3`, `h4` (noms, sous-titres) |
| Corps de texte | Source Sans 3 (300/400/600) | Tout le reste |

**Pour :** intemporel, différenciant, bonne lisibilité, contraste WCAG AA respecté.  
**Contre :** moins immédiatement associé au monde périnatal qu'un rose.

---

## Règles Tangerine

Tangerine est une police calligraphique fine. Elle est **belle en grand, illisible en petit**.

- ✅ Autorisé : `h1` hero (100px), `h2` titres de sections (50px), texte accroche italic (30px)
- ❌ Interdit : corps de texte, labels, navigation, boutons, anything < 40px

---

## Contraste WCAG AA — vérifications

| Fond | Texte | Ratio | Statut |
|---|---|---|---|
| Blanc `#ffffff` | Vert forêt `#3a6b4e` | ~5.4:1 | ✅ AA normal |
| Blanc `#ffffff` | Brun-ocre `#a06035` | ~4.2:1 | ✅ AA grand texte |
| Vert forêt `#3a6b4e` | Blanc `#ffffff` | ~5.4:1 | ✅ AA normal |
| Vert nuit `#1a2e22` | Gris-vert pâle `#a8b8ae` | ~5.8:1 | ✅ AA normal |

> B9 et B10 de la todo list doivent vérifier les ratios définitifs dans le navigateur
> via https://webaim.org/resources/contrastchecker/

---

## Ce qui ne change pas

- **Doctolib** : `#107aca` — couleur imposée par le partenaire, non modifiable
- **Tangerine** : conservée, usage restreint (voir règles ci-dessus)
- **FontAwesome** : déjà self-hosté (A4)
- **Bootstrap** : conservé pour la grille et les composants utilitaires
