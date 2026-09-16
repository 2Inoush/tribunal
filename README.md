# Le Tribunal des Disputes Minuscules

> « Soumettez un conflit absurde. Plaidez votre cause. Laissez le peuple trancher. »

Projet d'école — Ines, Enora, Matéo.
Plateforme web où la communauté vote **Acquitté** ou **Coupable** sur des conflits du quotidien.

## Technologies

HTML5, CSS (Tailwind), PHP 8 et MySQL — sans aucun framework ni installation.

## Installation

1. Placer le dossier du projet dans `/Applications/MAMP/htdocs/`
2. Démarrer MAMP (Apache + MySQL)
3. Dans phpMyAdmin, importer **dans cet ordre** :
   - `database/schema.sql` (crée la base et les tables)
   - `database/seeds.sql` (jeu de données de démonstration, facultatif)
4. Ouvrir **http://localhost:8888/tribunal/public/**

Comptes de démonstration — mot de passe `tribunal` pour tous :
`juge.citron@tribunal.test`, `maitre.caillou@tribunal.test`, …

> Les identifiants MySQL sont en haut de `app/config.php`. Les valeurs par
> défaut sont celles de MAMP (port 8889, `root` / `root`).

## Organisation des fichiers

```
tribunal/
├── public/              ← LES PORTES D'ENTRÉE (ce que le navigateur ouvre)
│   ├── index.php            3 lignes : charge la config, appelle le contrôleur
│   ├── inscription.php
│   ├── connexion.php
│   ├── deconnexion.php
│   ├── affaire-creer.php
│   ├── affaire-modifier.php
│   ├── affaire-supprimer.php
│   ├── profil.php
│   └── images/
│
├── app/                 ← LE CODE (le navigateur n'y accède jamais)
│   ├── .htaccess            interdit l'accès direct à ce dossier
│   ├── config.php           session + connexion à la base + fonction e()
│   │
│   ├── controllers/         C — reçoit le formulaire, vérifie, décide
│   │   ├── accueil.php
│   │   ├── inscription.php
│   │   ├── connexion.php
│   │   ├── deconnexion.php
│   │   ├── affaire-creer.php
│   │   ├── affaire-modifier.php
│   │   ├── affaire-supprimer.php
│   │   └── profil.php
│   │
│   ├── models/              M — les requêtes SQL
│   │   ├── utilisateur.php
│   │   └── affaire.php
│   │
│   └── views/               V — le HTML
│       ├── header.php       haut de page (commun)
│       ├── footer.php       bas de page (commun)
│       ├── accueil.php
│       ├── inscription.php
│       ├── connexion.php
│       ├── affaire-creer.php
│       ├── affaire-modifier.php
│       └── profil.php
│
├── database/
│   ├── schema.sql           structure des tables + vues
│   └── seeds.sql            données de démonstration
└── docs/
```

## Le MVC ici

Une page du site = **un fichier dans `public/`**. Pas de routeur, pas de
configuration : `connexion.php` dans l'URL, c'est `public/connexion.php`
sur le disque.

Ce fichier ne fait que deux lignes de travail — il passe la main au contrôleur :

```php
// public/connexion.php
require_once __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/controllers/connexion.php';
```

Le contrôleur, lui, fait le vrai travail, toujours dans le même ordre :

```php
// app/controllers/connexion.php
require_once __DIR__ . '/../models/utilisateur.php';

// 1. Si un formulaire a été envoyé, on le traite
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // vérifier les champs → appeler le modèle → rediriger
}

// 2. On affiche la vue
require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/connexion.php';
require __DIR__ . '/../views/footer.php';
```

Qui fait quoi :

| Couche | Où | Rôle |
|--------|-----|------|
| **Modèle** | `app/models/` | Parle à MySQL. **Le seul endroit où on écrit du SQL.** |
| **Vue** | `app/views/` | Affiche le HTML. Aucun calcul, aucune requête. |
| **Contrôleur** | `app/controllers/` | Reçoit le formulaire, vérifie, appelle le modèle, choisit la vue. **Ni SQL ni HTML.** |

Le chemin complet d'une page :

```
Navigateur  →  public/connexion.php  →  app/controllers/connexion.php
                                              ↓              ↓
                                       app/models/     app/views/
                                        (la base)       (le HTML)
```

## Avancement

| #  | Fonctionnalité | État |
|----|----------------|------|
| F1 | Inscription et connexion | ✅ fait |
| F2 | Créer une affaire | ✅ fait |
| F3 | Modifier une affaire (tant qu'aucun vote) | 🟡 vue + SQL prêts, contrôleur à écrire (Ines) |
| F4 | Supprimer une affaire | ✅ fait |
| F5 | Voter (Acquitté / Coupable) | à faire |
| F6 | Résultats du vote en direct | à faire |
| F7 | Récompenses | à faire |
| F8 | Profil et historique *(bonus)* | 🟡 page faite, historique des votes à venir |
| F9 | Classement des jurés *(bonus)* | à faire |

## Sécurité

Trois points, tous vérifiables dans le code :

- **Mots de passe** : jamais stockés en clair. `password_hash()` à l'inscription,
  `password_verify()` à la connexion (`app/models/utilisateur.php`).
- **Injection SQL** : toutes les requêtes sont préparées (les `?` puis
  `execute([...])`), donc une saisie ne peut pas modifier une requête.
- **Faille XSS** : tout texte affiché passe par `e()` (`app/config.php`), qui
  neutralise le HTML qu'un utilisateur aurait pu saisir.
- **Dossier `app/`** : inaccessible depuis le navigateur (`app/.htaccess`).
  Seules les pages de `public/` l'incluent.

À l'inscription, le message de connexion reste volontairement vague
(« Email ou mot de passe incorrect ») pour ne pas révéler quels comptes existent.

## Documentation

- [Modèle de données](docs/modele-de-donnees.md) — MCD, MLD et explication des vues
