
# ToDoListOCR

Refactorisation d'une application Symfony de gestion de tâches quotidiennes datant de 8 ans - projet OCR

<img src="https://insight.symfony.com/insight/img/medals/with-ribbon/medal-silver.png" width="20" height="20" /> [![Codacy Badge](https://app.codacy.com/project/badge/Grade/e4d7aac320aa43a39a909f965de216bb)](https://app.codacy.com/gh/AurelieBnc/ToDoListOCR/dashboard?utm_source=gh&utm_medium=referral&utm_content=&utm_campaign=Badge_grade) 
## Require

- PHP 8.2
- Symfony 7
- MySql 8

## Installation

### Télécharger le projet et dézipez le

```
https://github.com/AurelieBnc/ToDoListOCR/archive/refs/heads/dev.zip
```

### Créer un fichier .env.local et réecrire les paramètres d'environnement dans le fichier .env (changer user_db et password_db et les identifiant du compte pour envoyer les mails)

```
DATABASE_URL="mysql://user:mdp@127.0.0.1:3306/demo?serverVersion=8"
```

### Déplacer le terminal dans le dossier cloné

```
cd ToDoListOCR
```

### Taper les commandes suivantes

```
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Installation des fixtures

```
php bin/console doctrine:fixtures:load
```

### Lancer/arrêter le serveur local Symfony

```
symfony server:start

symfony server:stop
```

### Activer le deboggage en env local

Ne pas l'appliquer en env de prod
Dans votre fichier .env.local ajouté la ligne :
```
APP_DEBUG=1
```

### Passer en env de Prod

Lancer la commande:
```
symfony console cache:clear
```

Dans votre fichier .env.local, modifiez:
```
APP_ENV=prod
APP_DEBUG=0
```

## Tests et couverture de test

### Création de la base de données de Test et charger les fixtures

Configurer votre fichier env.local avec l'adresse de votre base de donnée. 
Symfony se chargera de la renommée en suffixant le nom de la base par _test.

Créer la base de donnée de test
```
php bin/console doctrine:database:create --env=test
php bin//console doctrine:migrations:migrate --env=test
```

Charger les fixtures
```
php bin/console doctrine:fixtures:load --env=test
```

### Lancement des tests et de la couverture de test

Pour lancer les tests PhpUnit, pensez à bien relancer les fixtures avant, puis:
```
vendor/bin/phpunit
```

Lancer une couverture de tests avec visuel html:
```
vendor/bin/phpunit --coverage-html public/test-coverage
```

## Performance

Une fois le projet installé, afin d'optimiser les performances lancer ces commandes :
```
composer dump-env prod
composer dump-autoload --optimize
```

Il est également nécessaire d'activer les extensions php suivante :
```
extension=php_apcu

zend_extension=opcache
opcache.preload=/app/var/cache/prod/App_KernelProdContainer.preload.php
```
## Documentations
- [Diagrammes](https://github.com/AurelieBnc/ToDoListOCR/tree/dev/docs/diagrammes)
- [Documentations techniques](https://github.com/AurelieBnc/ToDoListOCR/tree/dev/docs/documentation_technique)
- [Audit de qualité et de performance & bonnes pratiques](https://github.com/AurelieBnc/ToDoListOCR/tree/dev/docs/audit)

## Contribution

Merci de votre intérêt à contribuer! Il existe de nombreuses façons de contribuer à ce projet. Get started [here](https://github.com/AurelieBnc/ToDoListOCR/blob/dev/CONTRIBUTING.md).
