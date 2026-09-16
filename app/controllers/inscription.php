<?php
/**
 * CONTROLEUR : inscription  (fonctionnalite F1)
 *
 * C'est le « C » de MVC. Son role :
 *   1. recuperer ce que l'utilisateur a envoye
 *   2. verifier que c'est correct
 *   3. demander au MODELE de creer le compte
 *   4. choisir la VUE a afficher
 *
 * Il ne contient aucune requete SQL (c'est le role du modele)
 * et aucun HTML (c'est le role de la vue).
 */

require_once __DIR__ . '/../models/utilisateur.php';

// Deja connecte ? Inutile de s'inscrire.
if (estConnecte()) {
    header('Location: index.php');
    exit;
}

// Les erreurs seront rangees ici : ['pseudo' => 'message', ...]
$erreurs = [];

// Les valeurs saisies, pour les reafficher si le formulaire est refuse.
$pseudo = '';
$email  = '';


// =====================================================================
//  1. TRAITEMENT DU FORMULAIRE
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // trim() enleve les espaces au debut et a la fin
    $pseudo       = trim($_POST['pseudo'] ?? '');
    $email        = trim($_POST['email'] ?? '');
    $motDePasse   = $_POST['mot_de_passe'] ?? '';
    $confirmation = $_POST['mot_de_passe_confirmation'] ?? '';

    // ---- Verification du pseudo ----
    if ($pseudo === '') {
        $erreurs['pseudo'] = 'Le pseudo est obligatoire.';
    } elseif (mb_strlen($pseudo) < 3 || mb_strlen($pseudo) > 30) {
        $erreurs['pseudo'] = 'Le pseudo doit faire entre 3 et 30 caractères.';
    } elseif (pseudoDejaPris($pdo, $pseudo)) {
        $erreurs['pseudo'] = 'Ce pseudo est déjà pris.';
    }

    // ---- Verification de l'email ----
    if ($email === '') {
        $erreurs['email'] = "L'adresse email est obligatoire.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erreurs['email'] = "Cette adresse email n'est pas valide.";
    } elseif (emailDejaUtilise($pdo, $email)) {
        $erreurs['email'] = 'Cette adresse email est déjà utilisée.';
    }

    // ---- Verification du mot de passe ----
    if ($motDePasse === '') {
        $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
    } elseif (mb_strlen($motDePasse) < 8) {
        $erreurs['mot_de_passe'] = 'Le mot de passe doit faire au moins 8 caractères.';
    }

    if ($motDePasse !== $confirmation) {
        $erreurs['mot_de_passe_confirmation'] = 'Les deux mots de passe ne sont pas identiques.';
    }

    // ---- Aucune erreur : on demande au modele de creer le compte ----
    if (count($erreurs) === 0) {

        $utilisateur = creerUtilisateur($pdo, $pseudo, $email, $motDePasse);

        // On range l'utilisateur en session : il est maintenant connecte.
        // On ne garde que ce dont on a besoin (surtout pas le mot de passe).
        $_SESSION['utilisateur'] = [
            'id'     => $utilisateur['id'],
            'pseudo' => $utilisateur['pseudo'],
        ];

        $_SESSION['message'] = "Bienvenue au tribunal, $pseudo !";

        header('Location: index.php');
        exit;   // exit est important : il arrete le script apres la redirection
    }
}


// =====================================================================
//  2. AFFICHAGE DE LA VUE
// =====================================================================

$titre = 'Inscription';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/inscription.php';
require __DIR__ . '/../views/footer.php';
