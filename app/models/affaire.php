<?php
/**
 * MODELE : affaire
 *
 * Toutes les requetes SQL sur la table `affaire`.
 * Comme pour le modele utilisateur : requetes preparees ( les « ? » ),
 * et aucune balise HTML ici.
 */


/**
 * Enregistre une nouvelle affaire et renvoie son identifiant.
 *
 * $argument2 peut valoir null : le deuxieme argument de defense est
 * facultatif (la colonne accepte NULL dans la base).
 */
function creerAffaire(
    PDO $pdo,
    int $idUtilisateur,
    string $titre,
    string $description,
    string $argument1,
    ?string $argument2
): int {
    $requete = $pdo->prepare(
        'INSERT INTO affaire (id_utilisateur, titre, description, argument_1, argument_2)
         VALUES (?, ?, ?, ?, ?)'
    );

    $requete->execute([
        $idUtilisateur,
        $titre,
        $description,
        $argument1,
        $argument2,
    ]);

    // lastInsertId() donne l'id que MySQL vient d'attribuer.
    return (int) $pdo->lastInsertId();
}


/**
 * Renvoie toutes les affaires deposees par un utilisateur,
 * de la plus recente a la plus ancienne.
 *
 * On lit la vue `v_affaire_stats` (voir database/schema.sql) plutot que
 * la table `affaire` : elle donne en plus, pour chaque affaire,
 *   - total_votes    : le nombre de votes recus
 *   - pct_acquitte / pct_coupable : la repartition en pourcentage
 *   - modifiable     : 1 tant qu'aucun vote n'a ete depose, 0 ensuite
 *
 * `modifiable` servira pour F3 : une affaire ne peut etre modifiee
 * que tant qu'elle n'a recu aucun vote.
 */
function affairesDeUtilisateur(PDO $pdo, int $idUtilisateur): array
{
    $requete = $pdo->prepare(
        'SELECT id, titre, description, date_creation,
                total_votes, pct_acquitte, pct_coupable, modifiable
         FROM v_affaire_stats
         WHERE id_utilisateur = ?
         ORDER BY date_creation DESC'
    );

    $requete->execute([$idUtilisateur]);

    // fetchAll() renvoie toutes les lignes d'un coup, dans un tableau.
    return $requete->fetchAll();
}


/**
 * Lit UNE affaire a partir de son identifiant.
 * Renvoie null si elle n'existe pas.
 *
 * On interroge la vue `v_affaire_stats` et non la table `affaire` :
 * elle contient les memes colonnes, plus deux informations dont les
 * controleurs ont besoin pour F3 et F4 :
 *   - id_utilisateur : pour verifier que c'est bien l'auteur
 *   - modifiable     : 1 tant qu'aucun vote n'a ete depose, 0 ensuite
 */
function trouverAffaire(PDO $pdo, int $id): ?array
{
    $requete = $pdo->prepare(
        'SELECT id, id_utilisateur, auteur, titre, description,
                argument_1, argument_2, date_creation, date_modification,
                total_votes, pct_acquitte, pct_coupable, modifiable
         FROM v_affaire_stats
         WHERE id = ?'
    );

    $requete->execute([$id]);

    return $requete->fetch() ?: null;
}


/**
 * Met a jour le contenu d'une affaire.
 *
 * Pas besoin de toucher a `date_modification` : la colonne se met a jour
 * toute seule (ON UPDATE CURRENT_TIMESTAMP, voir database/schema.sql).
 *
 * Attention : cette fonction ne verifie pas QUI demande la modification,
 * ni si l'affaire a deja recu des votes. C'est au controleur de le faire
 * avant de l'appeler (regle F3).
 */
function modifierAffaire(
    PDO $pdo,
    int $id,
    string $titre,
    string $description,
    string $argument1,
    ?string $argument2
): void {
    $requete = $pdo->prepare(
        'UPDATE affaire
         SET titre = ?, description = ?, argument_1 = ?, argument_2 = ?
         WHERE id = ?'
    );

    $requete->execute([
        $titre,
        $description,
        $argument1,
        $argument2,
        $id,
    ]);
}


/**
 * Supprime une affaire.
 *
 * Ses votes disparaissent en meme temps : c'est le ON DELETE CASCADE
 * de la cle etrangere, il n'y a pas de DELETE a ecrire sur la table vote.
 *
 * Meme remarque que ci-dessus : c'est au controleur de verifier que
 * l'utilisateur connecte est bien l'auteur (regle F4).
 */
function supprimerAffaire(PDO $pdo, int $id): void
{
    $requete = $pdo->prepare('DELETE FROM affaire WHERE id = ?');
    $requete->execute([$id]);
}
