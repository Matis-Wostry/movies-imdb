# Movies IMDb

Application web PHP permettant de rechercher des films, consulter leurs informations et laisser des avis.

## Site en ligne

[movies-imdb.matis-wostry.com](https://movies-imdb.matis-wostry.com)

Le site est déployé automatiquement via CI/CD : chaque push sur la branche `main` déclenche un pipeline GitHub Actions qui synchronise les fichiers sur le serveur et redémarre la stack Docker.

## Fonctionnalités

- **Recherche de films** — saisie libre ou suggestions prédéfinies, résultats affichés en grille avec affiches
- **Page détail** — titre, année, note IMDb, synopsis, acteurs et affiche récupérés via expressions régulières depuis les fichiers HTML locaux
- **Sauvegarde automatique** — chaque film consulté est enregistré en base de données
- **Avis utilisateur** — formulaire pour noter un film (1–10) et laisser un commentaire, sauvegardé en base
- **Collection** — page listant tous les films déjà sauvegardés en base
- **Films en local** — liste des films disponibles localement, accessibles sans base de données

## Stack

- PHP (sans framework)
- MySQL via PDO
- Tailwind CSS (CDN)
- MAMP (serveur local)

## Installation

### 1. Cloner le projet

Placer le dossier dans `htdocs` de MAMP :

```
MAMP/htdocs/movies-imdb/
```

### 2. Créer la base de données

Ouvrir **phpMyAdmin** (`http://localhost/phpmyadmin`) puis :

1. Aller dans l'onglet **Importer**
2. Sélectionner le fichier `data/movies_imdb.sql`
3. Cliquer sur **Exécuter**

Cela crée la base `movies_imdb` avec les tables `movies` et `reviews`.

### 3. Configurer la connexion

Ouvrir `db.php` et ajuster les identifiants si nécessaire :

```php
$pdo = new PDO(
    'mysql:host=localhost;dbname=movies_imdb;charset=utf8mb4',
    'root',   // utilisateur
    'root',   // mot de passe
    ...
);
```

> Par défaut MAMP utilise `root` / `root`.

### 4. Lancer MAMP

Démarrer Apache et MySQL depuis MAMP, puis accéder à :

```
http://localhost/movies-imdb/index.php
```

## Structure des fichiers

```
movies-imdb/
├── index.php          # Page d'accueil — collection + films en local
├── detail.php         # Page détail d'un film
├── results.php        # Page de résultats de recherche
├── db.php             # Connexion PDO à la base de données
├── _header.php        # Header partagé (barre de recherche)
├── _local_films.php   # Section films disponibles en local
└── data/
    ├── movies_imdb.sql    # Script SQL de création de la base
    └── imdb/
        ├── tt0137523.html         # Fichiers détail des films
        ├── ...
        ├── search_inception.html  # Fichiers de résultats de recherche
        └── ...
```

## Recherches disponibles

Les fichiers locaux couvrent les termes suivants :

| Terme | Fichier |
|---|---|
| Fight Club | `search_fight_club.html` |
| Godfather | `search_godfather.html` |
| Inception | `search_inception.html` |
| Lord of the Rings | `search_lord_of_the_rings.html` |
