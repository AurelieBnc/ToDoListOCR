
# Projet ToDo & CO - Contributing Guidelines

This file describes how to contribute to ToDo & Co App improvement.
Thanx for your contributing !

## Introduction

Following these guidelines allows you to respect everyone's time and maintain consistency in the application and in the quality of the code.

You can write package update, bug fix, security or new feature contributions.
We are not currently looking to do a graphical overhaul of the application.

Don’t forget to check out the Readme.md!

### 1. Code of Conduct

Before making a PR, you must write an Issue respecting the model of the previous ones, tag them according to their content and affiliate it with the ToDoListOcr project. It must be affiliated with a MileStone corresponding to the theme of the Issue.
Once the Issue has been validated, you can create a PR which is reviewed by the team.

Be kind in code or issue reviews, we are all always learning.

Are you working on your first Pull Request?

You can learn how to do this with this free series, [How to Contribute to an Open Source Project on GitHub](https://egghead.io/series/how-to-contribute-to-an-open-source-project-on-github). 

### 2. Prerequisites

Before making a PR, you must write an Issue respecting the model of the previous ones, tag them according to their content and affiliate it with the ToDoListOcr project. It must be affiliated with a MileStone corresponding to the theme of the Issue.
Once the Issue has been validated, you will be able to create a PR which will be reviewed by the team.

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

For each new feature, please create the corresponding unit and functional tests and maintain a coverage ratio above 70%.
For this we use PhpUnit and the command:
```
vendor/bin/phpunit --coverage-html public/test-coverage
```
It will allow you to have an HTML visual of the coverage report and its effectiveness, and to know the distribution of the tests.

### 2. Code Quality

In order to maintain good code quality and avoid excessive technical debt, we use Php-Cs-Fixer to respect the quality standards of Symfony and PSR. We verify them through audits with Codacy.

Install Php-Cs-Fixer in the project root and run the check
```
composer require --dev friendsofphp/php-cs-fixer
vendor/bin/php-cs-fixer check
```
After verifying the check, launch the fix and place it in a commit.
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

This application develop with Symfony framework, please check the [Symfony Documentation](https://symfony.com/doc/current/index.html) to follow best practices.
