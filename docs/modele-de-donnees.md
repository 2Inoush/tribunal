# Modèle de données

Fichier de référence : [`database/schema.sql`](../database/schema.sql)

## MCD (Merise)

```
UTILISATEUR ──1,n── rédige ──0,n── AFFAIRE
     │                                │
     └──0,n── VOTE (choix, date) ──1,n┘
```

- Un **utilisateur** rédige 0 à n **affaires** ; une affaire a exactement un auteur.
- Un **utilisateur** émet 0 à n **votes** ; une **affaire** reçoit 0 à n votes.
- L'association `vote` porte les attributs `choix` et `date_vote`,
  et est contrainte à **une occurrence par couple (utilisateur, affaire)**.

## MLD

```
utilisateur (id, pseudo, email, mot_de_passe, date_creation)
affaire     (id, #id_utilisateur, titre, description, argument_1, argument_2,
             date_creation, date_modification)
vote        (id, #id_utilisateur, #id_affaire, choix, date_vote)
             UNIQUE (id_utilisateur, id_affaire)
```

## Écarts assumés avec la section 8 du cahier des charges

| Point | Cahier des charges | Schéma livré | Raison |
|-------|--------------------|--------------|--------|
| `argument_2` | obligatoire | `NULL` autorisé | Un seul argument suffit à publier ; le second est un bonus de plaidoirie. |
| `date_modification` | absent | ajouté | Nécessaire pour afficher « modifiée le … » (F3). |
| Suppression d'affaire | non précisé | suppression réelle + cascade | F4 : « l'affaire disparaît du flux pour tout le monde ». |
| Compteurs de votes | non précisé | vue `v_affaire_stats` | Évite de dupliquer un compteur qui pourrait se désynchroniser (F6). |

## Vues

### `v_affaire_stats`
Une ligne par affaire, avec `total_votes`, `votes_acquitte`, `votes_coupable`,
`pct_acquitte`, `pct_coupable` et `modifiable` (vrai tant qu'aucun vote n'est déposé).
Alimente l'accueil (F6) et conditionne l'affichage du bouton « Modifier » (F3).

### `v_classement_jures`
Classement par `score_reputation` (`nb_votes + nb_affaires × 5`).
Les égalités sont départagées par le nombre de votes, puis par l'ancienneté du compte (F9).
