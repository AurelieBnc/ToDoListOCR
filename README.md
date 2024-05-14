# ToDoListOCR
Refactorisation d'une application Symfony de gestion de tâches quotidiennes datant de 8 ans - projet OCR 

## Require
- PHP 8.2
- Symfony 7
- MySql 8

## Installation

### Télécharger le projet et dézipez le
```

```

### Créer un fichier .env.local et réecrire les paramètres d'environnement dans le fichier .env (changer user_db et password_db et les identifiant du compte pour envoyer les mails)

```
DATABASE_URL="mysql://user:mdp@127.0.0.1:3306/demo?serverVersion=8"
```

### Déplacer le terminal dans le dossier cloné
```
cd ToDoListOCR
```

### Taper les commandes suivantes :
```
composer install
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
```

### Installation des fixtures 
```

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
