# LINGUA PROJECT

Une petite application pour exposer une langue avec possibilité de créer des catégories, des articles auxquels on peut associer
des documents. On peut configurer le texte à afficher sur la page d'accueil et créer une page **A propos**. Depuis le tableau de
bord de l'administrateur, on peut faire des sauvegardes de la base de données.

## Prérequis

* PHP 8.0.2
* Composer
* MariaDB / MySQL

## Installation

### Récupération du projet et configuration

Installer les fichiers, les dépendances :

```bash
git clone https://github.com/CelineFoucart/LinguaProject.git
composer install
```

Configurer les variables d'environnement dans un **.env.local**.

### Installation de la base de données

Lancer les migrations :

```bash
php bin/console d:m:m
```

Créer un compte administrateur :

```bash
php bin/console app:create-user
```

## License

Distribué sous la licence MIT. Voir `LICENSE` pour plus d'informations.

## TODO

* show password dans admin fonctionne pas
