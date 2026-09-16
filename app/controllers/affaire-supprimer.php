<?php
/**
 * CONTROLEUR : supprimer une affaire  (fonctionnalite F4)
 *
 * Cahier des charges :
 *   « l'affaire disparait du flux pour tout le monde »
 *   Erreur a gerer : suppression tentee par un autre utilisateur que l'auteur.
 *
 * Cette page n'affiche aucune vue : elle verifie, supprime, puis renvoie
 * l'utilisateur vers son profil avec un message.
 *
 * Elle est appelee par le formulaire de la liste « Mes conflits » :
 *     <form method="post" action="affaire-supprimer.php?id=12">
 */

require_once __DIR__ . '/../models/affaire.php';


// ---------------------------------------------------------------------
//  1. Il faut etre connecte
// ---------------------------------------------------------------------

if (!estConnecte()) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour supprimer une affaire.';
    header('Location: connexion.php');
    exit;
}


// ---------------------------------------------------------------------
//  2. Ne supprimer que sur l'envoi d'un formulaire (POST)
// ---------------------------------------------------------------------
// Une suppression ne doit jamais partir sur une simple visite d'adresse :
// un lien peut etre clique par erreur, ou precharge par le navigateur.
// Si quelqu'un tape l'adresse a la main, on le renvoie simplement au profil.

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: profil.php');
    exit;
}


// ---------------------------------------------------------------------
//  3. Recuperer l'identifiant de l'affaire
// ---------------------------------------------------------------------
// $_GET['id'] est du texte et peut etre absent ou farfelu :
// on le convertit en entier avant toute chose. (int) 'abc' vaut 0.

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['erreur'] = 'Affaire introuvable.';
    header('Location: profil.php');
    exit;
}


// ---------------------------------------------------------------------
//  4. L'affaire existe-t-elle ?
// ---------------------------------------------------------------------

$affaire = trouverAffaire($pdo, $id);

if ($affaire === null) {
    $_SESSION['erreur'] = "Cette affaire n'existe pas ou a déjà été supprimée.";
    header('Location: profil.php');
    exit;
}


// ---------------------------------------------------------------------
//  5. Est-ce bien SON affaire ?
// ---------------------------------------------------------------------
// Le controle le plus important de cette page. Le bouton Supprimer
// n'apparait que sur ses propres affaires, mais cacher un bouton
// n'empeche personne de changer le chiffre dans l'adresse.
// La verification se fait donc ici, cote serveur.

if ((int) $affaire['id_utilisateur'] !== (int) utilisateurConnecte()['id']) {
    $_SESSION['erreur'] = 'Vous ne pouvez supprimer que vos propres affaires.';
    header('Location: profil.php');
    exit;
}


// ---------------------------------------------------------------------
//  6. Suppression
// ---------------------------------------------------------------------
// Les votes de l'affaire disparaissent en meme temps, grace au
// ON DELETE CASCADE de la cle etrangere (database/schema.sql).
//
// A noter : contrairement a la modification (F3), la suppression reste
// possible meme si l'affaire a deja recu des votes.

supprimerAffaire($pdo, $id);

$_SESSION['message'] = 'Votre affaire a été supprimée.';

header('Location: profil.php');
exit;
