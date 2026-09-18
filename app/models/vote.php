<?php
/**
 * MODELE : vote
 *
 * Toutes les requetes SQL sur la table `vote` (fonctionnalite F5).
 */


/**
 * Ce juré a-t-il deja vote sur cette affaire ?
 *
 * Le cahier des charges est clair : un seul vote par utilisateur et par
 * affaire. La base l'interdit deja (contrainte UNIQUE dans schema.sql),
 * mais on verifie AVANT d'inserer pour pouvoir afficher un message
 * propre plutot qu'une erreur SQL.
 */
function aDejaVote(PDO $pdo, int $idUtilisateur, int $idAffaire): bool
{
    $requete = $pdo->prepare(
        'SELECT id FROM vote WHERE id_utilisateur = ? AND id_affaire = ?'
    );

    $requete->execute([$idUtilisateur, $idAffaire]);

    return $requete->fetch() !== false;
}


/**
 * Enregistre un vote.
 *
 * $choix ne peut valoir que « acquitte » ou « coupable » : c'est le
 * type ENUM de la colonne. Le controleur verifie la valeur avant
 * d'appeler cette fonction.
 */
function enregistrerVote(PDO $pdo, int $idUtilisateur, int $idAffaire, string $choix): void
{
    $requete = $pdo->prepare(
        'INSERT INTO vote (id_utilisateur, id_affaire, choix) VALUES (?, ?, ?)'
    );

    $requete->execute([$idUtilisateur, $idAffaire, $choix]);
}


/**
 * Qu'a vote ce juré sur cette affaire ?
 *
 * Renvoie « acquitte », « coupable », ou null s'il n'a pas encore vote.
 * Sert a lui rappeler son propre verdict sous la jauge des resultats.
 */
function voteDeUtilisateur(PDO $pdo, int $idUtilisateur, int $idAffaire): ?string
{
    $requete = $pdo->prepare(
        'SELECT choix FROM vote WHERE id_utilisateur = ? AND id_affaire = ?'
    );

    $requete->execute([$idUtilisateur, $idAffaire]);

    // fetchColumn() renvoie directement la valeur de la colonne,
    // ou false s'il n'y a pas de ligne.
    return $requete->fetchColumn() ?: null;
}
