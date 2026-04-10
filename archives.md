# update du projet
Voici le document référence pour l'étape d'update de symfony4.4 à symfony7.4

```


# objectifs

1) Remplacer la dépendance à filemanager par une implémentation directe des fonctionnalités utilisées
2) mettre à niveau ce projet en php 8.4 et symfony 7.4, en conservant l'aspect et la logique métier, utilisation de rector
3) amélioration SEO, objectif très haut
4) infra, envrionnement docker, pas de symfony cli, donc un docker compose soigné et les dockerfile éventuels, dont celui de l'app
5) évaluer s'il y a gain à passer de webpack à assetMapper, faire la transition si l'évaluation est positive
6) ajout php-cs-fixer
7) écriture de tests unitaires et integrations, notamment pour le remplacement du bundle filemanager


## compréhension du projet

### filemanager

analyser  le bundle /lib/FileManagerBundle
il s'agit d'une aide à la gestion d'images

et reporter ses fonctions et particularités dans un fichier /doc/filemanager.md

### app symfony

analyser tout le projet et lister toutes les fonctionnalités, php , js, etc, les reporter dans un fichier /doc/analyse.md

analyser le style et le js et les templates, proposer des améliorations si pertinent, les reporter dans un fichier pour traitement plus tard

trouver et lister précisemment les fonctions utilisant FileManagerBundle. Evaluer la pertinence de l'utilisation de ces fonctions, et proposer une alternative ou imaginer l'implémentation de code pour satisfaire ces fonctions dans l'appli.
La partie sécurisée (c'est à dire celle qui se trouve en dessous de /public et qui doit utiliser un stream) est je pense inutile, à vérifier

## évalutation de ce document

Si au cours de l'analyse, ce document parait incomplet, demander/proposer à l'utilisateur

## update symfony

### rector
installer et utiliser rector pour effectuer les majs de code, corriger si nécessaire
installer et valider le code avec phpstan niveau 8, corriger si nécessaire

# workflow

Idéalement, l'agent fera une branche de développement et fera un ou des commits soignés
chaque branche documenterait dans un dossier /doc les changements effectués,

# en cas de doute

demander à l'utilisateur, et mémoriser la décision dans un fichier

# SEO

ce site a pour objectif d'aider les sage-femmes qui utiliseront ce site (2 ou 3) à améliorer le remplissage de leur planning.

# tech 

matomo, o2switch pour l'hébergement, mariadb:11.4
pas de jquery
tinymce v6 en remplacement de ckeditor pour l'éditeur de rich text

# compléments

il manque déjà je crois les mentions obligatoires liées à l'activté d'une sage-femme, comme rpps, à vérifier et implémenter
vérifier si autre chose

## ordre d'exécution suggéré

1- référencer les fonctionnalités utilisées de filemanager et conserver pour plus tard
2- environnement de test sous docker compose
3- phpcsfixer
4- utilisation de rector pour monter de version jusqu'à symfony7.4 et php 8.4
5- assetmapper / update package.json, vers les plus récentes versions compatibles + nettoyage si besoin / remplacement ckeditor par tinymce v6
6- Suppresion filemanager et remplacement des fonctionnalité par bundle existant ou code supplémentaire + tests unitaires ou integration selon pertinence
7- Amélioration SEO
8- Refactorisation/optimisation du code : recherche doublons ou redondances, code mort, 

## consigne globale pour le code
qualité de code suit phpstan niveau 8
lancer la commande php-cs-fixer fix sur les fichiers modifiés avant un commit
Limiter les responsablilités des fonctions
Nommage soigné
Commentaires uniquement si logique compliqué et dénomination pas suffisamment explicite
```