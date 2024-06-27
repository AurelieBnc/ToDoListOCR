
# Projet ToDo & CO - Contributing Guidelines

This file describes how to contribute to ToDo & Co App improvement.
Thanx for your contributing !

## Introduction

Suivre ces directives permet de respecter le temps de chacun et de garder une cohérence dans l'application et dans la qualité du code.

Vous pouvez écrire des contributions de mise à jour de package, de correctif de bug, de sécurité ou de nouvelle fonctionalité.
Nous ne cherchons pas à faire une refonte graphique de l'application actuellement.

N'oubliez pas de consulter le Readme.md !

### 1. Code of Conduct

Avant de faire une PR, vous devez écrire une Issue en respectant le modèle des précédentes, les taguer selon leur contenu et l'affilier au projet ToDoListOcr. Elle devra être affilié à un MileStone correspondant au thème de l'Issue.
Une fois l'Issue validée, vous pourrez créer une PR qui se revue par l'équipe.

Soyez bienvaillant dans les revues de code ou d'issue, nous sommes tous et toujours en apprentissage.

Vous travaillez sur votre première Pull Request ? Vous pouvez apprendre comment procéder grâce à cette série gratuite , Comment contribuer à un projet Open Source sur GitHub . https://egghead.io/series/how-to-contribute-to-an-open-source-project-on-github

### 2. Prerequisites

Avant de faire une PR, vous devez écrire une Issue en respectant le modèle des précédentes, les taguer selon leur contenu et l'affilier au projet ToDoListOcr. Elle devra être affilié à un MileStone correspondant au thème de l'Issue.
Une fois l'Issue validée, vous pourrez créer une PR qui sera revue par l'équipe.

## To Start

Make a fork of the project Github directory.
Have installed the project in local by following README.md instructions.
Before writing your code créer une nouvelle branche
```
git checkout -b new-branch
```
Puis
```
git push -u origin HEAD
```

### 1. Testing

Pour chaque nouvelle fonctionnalité, merci de créer les tests unitaires et fonctionnels correspondant et de maintenir un rapport de couverture au dessus de 70%.
Pour cela nous utilisons PhpUnit et la commande :
```
vendor/bin/phpunit --coverage-html public/test-coverage
```
Elle permetra d'avoir un visuel html du rapport de couverture et de son efficacité, et de connaitre la répartition des tests.

### 2. Code Quality

Afin de maintenir une bonne qualité du code et éviter une dette technique trop importante, nous utilisons Php-Cs-Fixer pour respecter les normes de qualité de Symfony et des PSR. Nous les vérifions grace à des audits avec Codacy.

Installer Php-Cs-Fixer à la racine du projet et lancer la vérification
```
composer require --dev friendsofphp/php-cs-fixer
vendor/bin/php-cs-fixer check
```
Après avoir vérifier le check, lancer le fix et placer le dans un commit.
```
vendor/bin/php-cs-fixer fix
```

## After finishing your develop

Create a PR with this template
```
### Old behaviors
### New behaviors
### Issue
### Dependencies (pull requests):
```

## Thank you !

## About Symfony

This application develop with Symfony framework, please check the Symfony Documentation to follow best practices.
