<?php
/**
 * CONTROLEUR : connexion  (fonctionnalite F1)
 *
 * Meme principe que le controleur d'inscription :
 * on verifie le formulaire, on demande au modele de controler
 * les identifiants, puis on ouvre la session si tout est bon.
 */

require_once __DIR__ . '/../models/utilisateur.php';

if (estConnecte()) {
    header('Location: index.php');
    exit;
}

$erreurs = [];
$email   = '';


// =====================================================================
//  1. TRAITEMENT DU FORMULAIRE
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email      = trim($_POST['email'] ?? '');
    $motDePasse = $_POST['mot_de_passe'] ?? '';

    if ($email === '') {
        $erreurs['email'] = "L'adresse email est obligatoire.";
    }

    if ($motDePasse === '') {
        $erreurs['mot_de_passe'] = 'Le mot de passe est obligatoire.';
    }

    // Les deux champs sont remplis : on verifie le couple email / mot de passe.
    if (count($erreurs) === 0) {

        $utilisateur = verifierIdentifiants($pdo, $email, $motDePasse);

        if ($utilisateur === null) {
            // On reste volontairement vague : on ne dit pas si c'est l'email
            // ou le mot de passe qui est faux. Sinon, n'importe qui pourrait
            // deviner quelles adresses ont un compte chez nous.
            $erreurs['email'] = 'Email ou mot de passe incorrect.';
        } else {

            $_SESSION['utilisateur'] = [
                'id'     => $utilisateur['id'],
                'pseudo' => $utilisateur['pseudo'],
            ];

            $_SESSION['message'] = "Content de vous revoir, {$utilisateur['pseudo']} !";

            header('Location: index.php');
            exit;
        }
    }
}


// =====================================================================
//  2. AFFICHAGE DE LA VUE
// =====================================================================

$titre = 'Connexion';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/connexion.php';
require __DIR__ . '/../views/footer.php';
