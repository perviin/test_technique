# test_technique - Projet réalisé dans le cadre d'un test technique

## Description du projet

Ce projet est une application Symfony permettant la gestion des utilisateurs avec un système d'authentification, un CRUD pour la table `infos`, et une fonctionnalité d'importation d'utilisateurs via un fichier CSV.

L'application propose un espace administrateur pour gérer les utilisateurs et leur attribuer des rôles spécifiques.

## Technologies utilisées

-   **PHP** 8.1.10 (Symfony 6)
    
-   **Twig** (Moteur de template)
    
-   **Doctrine** (ORM pour interagir avec la base de données)
    
-   **Bootstrap** (UI Styling)
    
-   **MySQL** (Base de données, nommée `test__cisad`)
    
-   **Composer** (Gestionnaire de dépendances)
    

## Installation et configuration

### 1. Cloner le repository

```
git clone https://github.com/ton-profil-github/test_technique.git
cd test_technique
```

### 2. Installer les dépendances

```
composer install
```

### 3. Configurer l'environnement

Créer un fichier `.env.local` et y ajouter les informations de connexion à la base de données :

```
DATABASE_URL="mysql://root:password@127.0.0.1:3306/test__cisad"
```

### 4. Mettre en place la base de données

```
symfony console doctrine:database:create
symfony console doctrine:migrations:migrate
```

### 5. Lancer le serveur Symfony

```
symfony server:start
```

L'application sera accessible à l'adresse `http://127.0.0.1:8000`

## Fonctionnalités principales

### 1. Authentification

-   Un utilisateur peut s'enregistrer et se connecter.
    
-   Si le username contient "admin", l'utilisateur reçoit automatiquement le rôle `ROLE_ADMIN`.
    
-   Sinon, l'utilisateur obtient le rôle `ROLE_USER`.
    
-   Un administrateur est redirigé vers le dashboard (`/admin/users`), tandis qu'un utilisateur normal est redirigé vers son profil (`/profile`).
    

### 2. Gestion des utilisateurs (Espace Admin)

-   Accessible uniquement aux administrateurs.
    
-   Permet de lister, modifier et supprimer les utilisateurs.
    
-   L'administrateur peut modifier un utilisateur sans être obligé de changer son mot de passe.
    

### 3. Gestion des informations (`infos`)

-   Chaque utilisateur a une entité `Infos` associée, contenant :
    
    -   `userRank`: Le rang de l'utilisateur (Débutant par défaut).
        
    -   `victoire`: Nombre de victoires (0 par défaut).
        
    -   `defaite`: Nombre de défaites (0 par défaut).
    

## Routes principales

|Routes|Descriptions  |
|--|--|
| `/` | Redirige automatiquement vers `/profile` ou `/admin` si connecter sinon `/register` |
|`/register`|Page d'inscription|
|`/login`|Page de connexion|
|`/admin/users`|Gestion des utilisateurs (Admin)|
|`/admin/users/new`|Importation CSV (Admin)|
|`/admin/users`|Modification d'un utilisateur (Admin)|
