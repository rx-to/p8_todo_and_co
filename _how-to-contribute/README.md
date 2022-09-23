# APPLICATION TODO & CO : MÉTHODOLOGIE DE TRAVAIL

**ToDo & Co** est une application permettant de gérer ses tâches quotidiennes.

Le but de ce document est d’expliquer aux futurs développeurs comment ils doivent s’y prendre pour effectuer une modification sur le projet.

# Table des matières

[TOC]

# Méthode KANBAN
![](img/kanban.png)

Afin de gérer le projet de façon optimale, nous avons utilisé la méthode KANBAN qui consiste à utiliser un tableau avec plusieurs colonnes, en général nous avons au moins trois colonnes :

- **To do** : les tâches à faire
- **In progress** : les tâches en cours
- **Done** : les tâches finies

D’autres colonnes peuvent être ajoutées à la guise de la personne chargée du projet.

Puisque nous avons déjà une version stable de l’application **To Do & Co** qui suit exactement le même principe, les développeurs peuvent l’utiliser pour créer leurs tâches ainsi que pour les mettre à jour.
# **Utilisation de Git**
![](img/git.png)

**Git** est un [**logiciel de gestion de versions**](https://fr.wikipedia.org/wiki/Logiciel_de_gestion_de_versions) [**décentralisé**](https://fr.wikipedia.org/wiki/Gestion_de_versions#Gestion_de_versions_d.C3.A9centralis.C3.A9e). C'est un [**logiciel libre**](https://fr.wikipedia.org/wiki/Logiciel_libre) et gratuit \[...] Depuis les années 2010, il s’agit du [**logiciel de gestion de versions**](https://fr.wikipedia.org/wiki/Logiciel_de_gestion_de_versions) le plus populaire dans le développement logiciel et web, qui est utilisé par des dizaines de millions de personnes, sur tous les environnements (Windows, Mac, Linux). Git est aussi le système à la base du célèbre site web [**GitHub**](https://fr.wikipedia.org/wiki/GitHub), le plus important hébergeur de code informatique.

*Source : Wikipédia*

## **Cloner le projet**
Afin de pouvoir travailler sur le projet en utilisant **Git**, il faut tout d’abord **cloner le dépôt Git** : $ git clone <https://github.com/rx-to/p8_todo_and_co.git>.

Ensuite, il faut accéder au dossier en utilisant la commande `$ cd p8_todo_and_co`.
## Mettre à jour le projet
Avant de commencer à effectuer des modifications, il faut s’assurer que l’on dispose de la dernière version stable de l’application, si vous venez de cloner le projet, cette étape n’est pas nécessaire, sinon, il vaut mieux lancer les commande suivante :
`$ git pull`

Vous aurez peut-être besoin de faire quelques manipulations supplémentaires, si c’est le cas, l’**invite de commandes** vous l’indiquera.
## Travailler sur une nouvelle branche
Avant toute modification, il faut impérativement travailler sur une nouvelle **branche** afin de pouvoir travailler en toute sérénité et de ne pas risquer de porter atteinte à la version stable de l’application.

Voici quelques commandes utiles :

- `$ git branch` : consulter la liste des **branches** existantes
- `$ git branch dev` : créer une nouvelle **branche** « dev »
## Effectuer des commits régulièrement
Les **commits** permettent à Git de prendre en compte les nouvelles modifications sur la branche actuelle, par conséquent, il ne faut surtout pas oublier cette étape.

Voici quelques commandes utiles :

- `$ git status` : consulter la liste des fichiers modifiés ainsi que leur statut, avant de faire un commit, il est préférable de vérifier à l’aide de cette commande que tous les fichiers modifiés vont bien être pris en compte par le **commit**.
- `$ git add -all` : préparer tous les fichiers créés, modifiés ou supprimés pour un commit. Vous ne pouvez pas faire de **commit** sans avoir ajouté au moins un fichier. 
- `$ git commit` : effectuer un **commit**, tout simplement.
# Publication sur GitHub
![](img/github.png)

**GitHub** est un service web d'[**hébergement**](https://fr.wikipedia.org/wiki/H%C3%A9bergeur_web) et de gestion de [**développement de logiciels**](https://fr.wikipedia.org/wiki/D%C3%A9veloppement_de_logiciel), utilisant le [**logiciel de gestion de versions**](https://fr.wikipedia.org/wiki/Logiciel_de_gestion_de_versions) [**Git**](https://fr.wikipedia.org/wiki/Git). [...] GitHub propose des comptes professionnels payants, ainsi que des comptes gratuits pour les projets de [**logiciels libres**](https://fr.wikipedia.org/wiki/Logiciels_libres).

Le site assure également un contrôle d'accès et des fonctionnalités destinées à la collaboration comme le suivi des bugs, les demandes de fonctionnalités, la gestion de tâches et un wiki pour chaque projet. Le site est devenu le plus important dépôt de code au monde, utilisé comme dépôt public de projets libres ou dépôt privé d'entreprises.
## Publier un dépôt Git
Afin de mettre en ligne vos modifications (vous pouvez le faire à chaque **commit**), vous devez publier votre **dépôt** en utilisant la plate-forme **GitHub**. 

Voici les commandes à utiliser :

`$ git remote add [https://github.com/rx-to/p8_todo_and_co.git todo_and_co](https://github.com/rx-to/p8_todo_and_co.git)`
`$ git push[ todo_and_co](https://github.com/rx-to/p8_todo_and_co.git) dev`

Vos modifications seront publiées sur **GitHub** dans la **branche « dev »** sans impacter la **branche principale**. 
## Effectuer un contrôle de qualité
### Avant le push : lancement des tests unitaires
Avant d’effectuer un **push**, il est nécessaire de vérifier que l’implémentation et/ou la modification d’une fonctionnalité n’a pas impacté le fonctionnement du reste de l’application avec le lancement des **tests unitaires**. De plus, il faut garder le taux de couverture supérieur à 70%.
### Après le push : analyse du code sur Code Climate
![](img/codeclimate.png)

Après avoir effectué un **push de votre branche**, il faut vérifier que la qualité du code ne s’est pas dégradée depuis les nouvelles modifications apportées. Pour cela, nous utilisons le site [**CodeClimate**](https://codeclimate.com). N’oubliez pas de bien **sélectionner votre nouvelle branche** avant de faire votre analyse. Comparez le score que vous avez obtenu avec l’ancien et faites les optimisations nécessaires avant d’effectuer une **pull request**.

## Effectuer une pull request
![](img/pull-request.png)

Quand vous avez fini de développer votre nouvelle fonctionnalité et que vous avez vérifié son bon fonctionnement ainsi que la qualité de votre code, vous pouvez **effectuer une pull request** afin que la ou les personne(s) chargée(s) du projet valide(nt) vos modifications et l’inclue(nt) dans la branche principale. Vos modifications seront alors mises en production.

**En savoir plus sur comment faire une pull request :** 
[**Creating a pull request - GitHub Docs**](https://docs.github.com/en/pull-requests/collaborating-with-pull-requests/proposing-changes-to-your-work-with-pull-requests/creating-a-pull-request)