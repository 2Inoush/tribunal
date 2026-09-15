# API REST

Base : `/api` — toutes les réponses sont en JSON (`Content-Type: application/json`).
Authentification par session PHP (cookie `tribunal_session`).

## Format de réponse

```json
{ "succes": true,  "donnees": { } }
{ "succes": false, "erreur": "Vous avez déjà voté sur cette affaire.", "champ": null }
```

## Codes HTTP

| Code | Cas |
|------|-----|
| 200 | Lecture ou mise à jour réussie |
| 201 | Ressource créée (inscription, affaire, vote) |
| 400 | Validation échouée (champ manquant, titre trop court…) |
| 401 | Non connecté (F5 : vote sans être connecté) |
| 403 | Connecté mais pas autorisé (F3/F4 : pas l'auteur ; F3 : affaire déjà votée) |
| 404 | Affaire introuvable |
| 409 | Conflit (email déjà utilisé, double vote) |
| 500 | Erreur serveur |

## Endpoints

### Comptes — F1
| Méthode | URI | Corps | Retour |
|---------|-----|-------|--------|
| POST | `/api/inscription` | `pseudo`, `email`, `mot_de_passe` | 201 + utilisateur |
| POST | `/api/connexion`   | `email`, `mot_de_passe`           | 200 + utilisateur |
| POST | `/api/deconnexion` | —                                 | 200 |

### Affaires — F2, F3, F4
| Méthode | URI | Règle |
|---------|-----|-------|
| GET    | `/api/affaires`       | flux, du plus récent au plus ancien |
| GET    | `/api/affaires/{id}`  | affaire + taux de vote |
| POST   | `/api/affaires`       | connecté |
| PUT    | `/api/affaires/{id}`  | auteur **et** `total_votes = 0` |
| DELETE | `/api/affaires/{id}`  | auteur |

### Votes — F5, F6
| Méthode | URI | Corps | Règle |
|---------|-----|-------|-------|
| POST | `/api/affaires/{id}/vote`      | `choix` : `acquitte` \| `coupable` | connecté, un seul vote par affaire |
| GET  | `/api/affaires/{id}/resultats` | — | renvoie 0 % / 0 % si aucun vote (état neutre, pas une erreur) |

### Classement — F9 (bonus)
| Méthode | URI |
|---------|-----|
| GET | `/api/classement` |
