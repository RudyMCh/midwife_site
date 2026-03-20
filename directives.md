# objectifs

1) mettre à niveau ce projet en php 8.4 et symfony 7.4, en conservant l'aspect et la logique métier
2) se débarraser de la dépendance à filemanager
3) évaluer s'il y a gain à passer de webpack à assetMapper, migrer si l'évaluation est positive
4) infra, envrionnement docker, pas de symfony cli, donc un docker compose soigné et les dockerfile éventuels, dont celui de l'app
5) évalutation et correction du code, phpstan niveau 8
6) ajout php-cs-fixer
7) amélioration SEO, objectif très haut
8) écriture de tests


## compréhension du projet

### filemanager

analyser  le bundle /lib/FileManagerBundle
et repoter ses fonctions et particularités dans un fichier /doc/filemanager.md

### app symfony

analyser tout le projet et lister toutes les fonctionnalités, php , js, etc, les reporter dans un fichier /doc/analyse.md

analyser le style et le js et les templates, proposer des améliorations si pertinent, les reporter dans un fichier pour traitement plus tard

trouver et lister précisemment les fonctions utilisant FileManagerBundle. Evaluer la pertinence de l'utilisation de ces fonctions, et proposer une alternative ou imaginer l'implémentation de code pour satisfaire ces fonctions dans l'appli.
La partie sécurisée est je pense inutile, à vérifier

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
