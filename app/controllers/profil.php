<?php
/**
 * CONTROLEUR : profil  (fonctionnalite F8)
 *
 * Affiche les informations du compte et la liste des conflits deposes.
 * Il n'y a pas de formulaire ici : le controleur se contente de
 * demander les donnees au modele et de les passer a la vue.
 */

require_once __DIR__ . '/../models/utilisateur.php';
require_once __DIR__ . '/../models/affaire.php';

// Il faut etre connecte pour voir son profil.
if (!estConnecte()) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour voir votre profil.';
    header('Location: connexion.php');
    exit;
}

$idUtilisateur = (int) utilisateurConnecte()['id'];

// La session ne garde que l'id et le pseudo : on relit la base pour
// avoir l'email et la date d'inscription.
$utilisateur = trouverUtilisateurParId($pdo, $idUtilisateur);

// Ses conflits, du plus recent au plus ancien.
$mesConflits = affairesDeUtilisateur($pdo, $idUtilisateur);


// ---------------------------------------------------------------------
//  Affichage de la vue
// ---------------------------------------------------------------------

$titre = 'Mon profil';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/profil.php';
require __DIR__ . '/../views/footer.php';
