# Le Tribunal des Disputes Minuscules

> « Soumettez un conflit absurde. Plaidez votre cause. Laissez le peuple trancher. »

Projet d'école — Ines, Enora, Matéo.
Plateforme web où la communauté vote **Acquitté** ou **Coupable** sur des conflits du quotidien.

## Stack

| Couche    | Technologie                                    |
|-----------|------------------------------------------------|
| Front     | HTML5, CSS3 (mobile first), JavaScript `fetch` |
| Back      | PHP 8 vanilla, architecture MVC, API REST JSON |
| Données   | MySQL 8 / MariaDB 10.4+                        |

## Installation

### Avec MAMP (ce qu'on utilise)

1. Placer le dossier du projet dans `/Applications/MAMP/htdocs/`.
2. Copier `.env.example` en `.env` (les valeurs par défaut correspondent déjà à MAMP :
   MySQL sur le port 8889, `root` / `root`).
3. Dans phpMyAdmin, importer dans l'ordre :
   `database/schema.sql` puis `database/seeds.sql`.
4. Ouvrir **http://localhost:8888/tribunal/public/**

> L'URL pointe sur `/public` : c'est le seul dossier que le navigateur doit voir.
> Les liens du site s'adaptent tout seuls au sous-dossier (voir `BASE_URL` dans
> `public/index.php`).

### Avec le serveur intégré de PHP

```bash
cp .env.example .env            # puis mettre DB_PORT=3306 et DB_PASS=
php -S localhost:8000 -t public
```

Comptes de démonstration : mot de passe `tribunal` pour tous
(`juge.citron@tribunal.test`, `maitre.caillou@tribunal.test`, …).

## Comment une page se construit

Toutes les URL passent par `public/index.php`, qui suit toujours les mêmes étapes :

```
Navigateur                 http://localhost:8888/tribunal/public/connexion
    ↓
public/.htaccess           redirige tout vers index.php
    ↓
public/index.php           prépare l'application (autoloader, .env, session)
    ↓
app/Config/routes.php      'GET /connexion' → AuthController::formulaireConnexion
    ↓
app/Core/Router.php        appelle cette méthode
    ↓
app/Controllers/…          le contrôleur décide quoi faire
    ↓
app/Models/…               le modèle parle à MySQL (lui seul écrit du SQL)
    ↓
app/Views/…                la vue affiche le résultat en HTML
```

En une phrase : le **contrôleur** décide, le **modèle** interroge la base,
la **vue** affiche. C'est tout le principe du MVC.

## Arborescence

```
tribunal/
├── public/              ← racine web (seul dossier exposé)
│   ├── index.php        ← front controller : toutes les URL passent ici
│   ├── .htaccess
│   └── assets/          ← css, js, img, fonts
├── app/
│   ├── Config/          ← config.php, database.php, routes.php
│   ├── Core/            ← Router, Controller, Model, Database, Session…
│   ├── Middleware/      ← Auth (connecté), Owner (auteur de l'affaire)
│   ├── Controllers/
│   │   ├── Web/         ← rendent des vues HTML
│   │   └── Api/         ← répondent en JSON (consommés par fetch)
│   ├── Models/          ← Utilisateur, Affaire, Vote
│   ├── Views/           ← layouts, partials, pages, errors
│   └── Helpers/
├── database/
│   ├── schema.sql       ← structure + vues (à importer en premier)
│   ├── seeds.sql        ← jeu de données de démonstration
│   └── migrations/
├── storage/logs/
└── docs/
```

## Correspondance fonctionnalités ↔ code

| #  | Fonctionnalité              | Route API                            | Modèle         |
|----|-----------------------------|--------------------------------------|----------------|
| F1 | Inscription / connexion ✅  | `POST /inscription`, `POST /connexion` | `Utilisateur`  |
| F2 | Créer une affaire           | `POST /api/affaires`                 | `Affaire`      |
| F3 | Modifier une affaire        | `PUT /api/affaires/{id}`             | `Affaire`      |
| F4 | Supprimer une affaire       | `DELETE /api/affaires/{id}`          | `Affaire`      |
| F5 | Voter                       | `POST /api/affaires/{id}/vote`       | `Vote`         |
| F6 | Résultats en direct         | `GET /api/affaires/{id}/resultats`   | vue `v_affaire_stats` |
| F7 | Récompenses                 | *(front — `recompense.js`)*          | —              |
| F8 | Profil et historique *(bonus)* | `GET /profil`                     | `Affaire`, `Vote` |
| F9 | Classement des jurés *(bonus)* | `GET /api/classement`             | vue `v_classement_jures` |

## Formulaires classiques ou `fetch` ?

L'inscription et la connexion utilisent des **formulaires HTML classiques**
(`POST` puis redirection) plutôt que `fetch` : c'est plus simple à lire, ça
marche même sans JavaScript, et une connexion a de toute façon besoin de
recharger la page.

L'API REST en JSON reste prévue là où elle apporte quelque chose : **voter sans
recharger la page** (F5, F6). Ces routes sont déjà écrites, en commentaire,
dans `app/Config/routes.php`.

## Règles métier verrouillées côté serveur

- Un seul vote par utilisateur et par affaire — contrainte `UNIQUE (id_utilisateur, id_affaire)`.
- Une affaire n'est modifiable que par son auteur, et **tant qu'elle n'a aucun vote**
  (colonne `modifiable` de la vue `v_affaire_stats`).
- Une affaire n'est supprimable que par son auteur ; ses votes partent en cascade.
- Mots de passe hachés avec `password_hash()`, jamais stockés en clair.

## Documentation

- [Modèle de données](docs/modele-de-donnees.md)
- [API REST](docs/api.md)
