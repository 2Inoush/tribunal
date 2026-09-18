<?php
/**
 * CONTROLEUR : accueil
 *
 * Trois roles :
 *   1. enregistrer le vote quand on clique sur Acquitté ou Coupable (F5)
 *   2. afficher le taux de vote de l'affaire qu'on vient de juger  (F6)
 *   3. sinon, tirer une affaire a juger
 *
 * Le parcours du cahier des charges :
 *   « il vote, le taux de vote s'affiche aussitot, puis il passe a
 *     l'affaire suivante. »
 * D'ou l'enchainement : vote -> resultats -> affaire suivante.
 */

require_once __DIR__ . '/../models/affaire.php';
require_once __DIR__ . '/../models/vote.php';


// =====================================================================
//  1. TRAITEMENT DU VOTE  (F5)
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choix'], $_POST['id_affaire'])) {

    // ---- Il faut etre connecte pour voter ----
    if (!estConnecte()) {
        $_SESSION['erreur'] = 'Vous devez être connecté pour voter.';
        header('Location: connexion.php');
        exit;
    }

    $idUtilisateur = (int) utilisateurConnecte()['id'];
    $idAffaire     = (int) $_POST['id_affaire'];
    $choix         = $_POST['choix'];

    // ---- Le choix doit etre l'une des deux valeurs attendues ----
    if (!in_array($choix, ['acquitte', 'coupable'], true)) {
        $_SESSION['erreur'] = 'Choix de vote invalide.';
        header('Location: index.php');
        exit;
    }

    // ---- Un seul vote par affaire ----
    // Sans ce test, la contrainte UNIQUE de la base ferait planter la
    // page avec une erreur SQL.
    if (aDejaVote($pdo, $idUtilisateur, $idAffaire)) {
        $_SESSION['erreur'] = 'Vous avez déjà rendu votre verdict sur cette affaire.';
        header('Location: index.php');
        exit;
    }

    enregistrerVote($pdo, $idUtilisateur, $idAffaire, $choix);

    // On repart sur la meme page, mais en demandant l'affichage des
    // resultats de l'affaire qu'on vient de juger.
    //
    // Pourquoi rediriger plutot qu'afficher directement ? Parce que sans
    // cela, recharger la page renverrait le formulaire et tenterait un
    // second vote. L'adresse obtenue est aussi rechargeable et partageable.
    header('Location: index.php?resultat=' . $idAffaire);
    exit;
}


// =====================================================================
//  2. AFFICHAGE DU TAUX DE VOTE  (F6)
// =====================================================================
// On arrive ici avec index.php?resultat=12 juste apres un vote.
// $resultats reste null le reste du temps.

$resultats = null;
$monChoix  = null;

if (isset($_GET['resultat']) && estConnecte()) {

    $idAffaire = (int) $_GET['resultat'];

    // trouverAffaire() lit la vue v_affaire_stats : les pourcentages
    // sont donc deja calcules par MySQL, il n'y a rien a recalculer ici.
    $resultats = trouverAffaire($pdo, $idAffaire);

    // Son propre verdict, pour le lui rappeler sous la jauge.
    $monChoix = voteDeUtilisateur($pdo, (int) utilisateurConnecte()['id'], $idAffaire);
}


// =====================================================================
//  3. L'AFFAIRE A JUGER
// =====================================================================
// Inutile de tirer une affaire si l'on affiche des resultats.
// Vaut null quand il n'y a plus rien a juger : la vue le signale.

$affaire = null;

if ($resultats === null && estConnecte()) {
    $affaire = affaireAJuger($pdo, (int) utilisateurConnecte()['id']);
}

$titre = 'Accueil';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/accueil.php';
require __DIR__ . '/../views/footer.php';
