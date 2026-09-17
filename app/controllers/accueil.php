<?php
/**
 * CONTROLEUR : accueil
 *
 * Deux roles :
 *   1. enregistrer le vote quand on clique sur Acquitté ou Coupable (F5)
 *   2. tirer une affaire au hasard et la passer a la vue (F6)
 *
 * Le traitement du vote a ete ecrit par Enora dans la vue ; il est
 * remonte ici, ou est sa place : une vue ne fait qu'afficher.
 */

require_once __DIR__ . '/../models/affaire.php';
require_once __DIR__ . '/../models/vote.php';


// =====================================================================
//  1. TRAITEMENT DU VOTE  (F5)
// =====================================================================
// On arrive ici quand le formulaire de la carte a ete envoye.
// isset() verifie que les deux champs attendus sont bien presents.

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choix'], $_POST['id_affaire'])) {

    // ---- Il faut etre connecte pour voter ----
    // Erreur prevue par le cahier des charges : « vote sans etre connecte ».
    if (!estConnecte()) {
        $_SESSION['erreur'] = 'Vous devez être connecté pour voter.';
        header('Location: connexion.php');
        exit;
    }

    $idUtilisateur = (int) utilisateurConnecte()['id'];
    $idAffaire     = (int) $_POST['id_affaire'];
    $choix         = $_POST['choix'];

    // ---- Le choix doit etre l'une des deux valeurs attendues ----
    // Le troisieme argument (true) de in_array impose une comparaison
    // stricte, qui compare aussi le type.
    if (!in_array($choix, ['acquitte', 'coupable'], true)) {
        $_SESSION['erreur'] = 'Choix de vote invalide.';
        header('Location: index.php');
        exit;
    }

    // ---- Un seul vote par affaire ----
    // Erreur prevue par le cahier des charges : « double vote sur la
    // meme affaire ». Sans ce test, la contrainte UNIQUE de la base
    // ferait planter la page avec une erreur SQL.
    if (aDejaVote($pdo, $idUtilisateur, $idAffaire)) {
        $_SESSION['erreur'] = 'Vous avez déjà rendu votre verdict sur cette affaire.';
        header('Location: index.php');
        exit;
    }

    enregistrerVote($pdo, $idUtilisateur, $idAffaire, $choix);

    $_SESSION['message'] = $choix === 'acquitte'
        ? 'Verdict rendu : acquitté.'
        : 'Verdict rendu : coupable.';

    // On redirige au lieu d'afficher directement la page : sans cela,
    // recharger la page renverrait le formulaire et tenterait un
    // deuxieme vote. C'est aussi ce qui fait apparaitre l'affaire suivante.
    header('Location: index.php');
    exit;
}


// =====================================================================
//  2. L'AFFAIRE A JUGER
// =====================================================================
// Peut valoir null si la base ne contient aucune affaire :
// la vue previent alors l'utilisateur, ce n'est pas une erreur.

$affaire = affaireAuHasard($pdo);

$titre = 'Accueil';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/accueil.php';
require __DIR__ . '/../views/footer.php';
