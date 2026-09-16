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
