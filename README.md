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

```bash
cp .env.example .env            # puis renseigner les accès MySQL
mysql -u root -p < database/schema.sql
mysql -u root -p tribunal < database/seeds.sql   # jeu de démo (facultatif)
php -S localhost:8000 -t public
```

Comptes de démonstration : mot de passe `tribunal` pour tous
(`juge.citron@tribunal.test`, `maitre.caillou@tribunal.test`, …).

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
| F1 | Inscription / connexion     | `POST /api/inscription`, `/connexion`| `Utilisateur`  |
| F2 | Créer une affaire           | `POST /api/affaires`                 | `Affaire`      |
| F3 | Modifier une affaire        | `PUT /api/affaires/{id}`             | `Affaire`      |
| F4 | Supprimer une affaire       | `DELETE /api/affaires/{id}`          | `Affaire`      |
| F5 | Voter                       | `POST /api/affaires/{id}/vote`       | `Vote`         |
| F6 | Résultats en direct         | `GET /api/affaires/{id}/resultats`   | vue `v_affaire_stats` |
| F7 | Récompenses                 | *(front — `recompense.js`)*          | —              |
| F8 | Profil et historique *(bonus)* | `GET /profil`                     | `Affaire`, `Vote` |
| F9 | Classement des jurés *(bonus)* | `GET /api/classement`             | vue `v_classement_jures` |

## Règles métier verrouillées côté serveur

- Un seul vote par utilisateur et par affaire — contrainte `UNIQUE (id_utilisateur, id_affaire)`.
- Une affaire n'est modifiable que par son auteur, et **tant qu'elle n'a aucun vote**
  (colonne `modifiable` de la vue `v_affaire_stats`).
- Une affaire n'est supprimable que par son auteur ; ses votes partent en cascade.
- Mots de passe hachés avec `password_hash()`, jamais stockés en clair.

## Documentation

- [Modèle de données](docs/modele-de-donnees.md)
- [API REST](docs/api.md)
