<?php
/**
 * CONTROLEUR : deconnexion  (fonctionnalite F1)
 *
 * Pas de formulaire ni de vue ici : on vide la session
 * et on renvoie l'utilisateur a l'accueil.
 */

// On vide la session, puis on la detruit.
$_SESSION = [];
session_destroy();

// On en ouvre une nouvelle, juste pour afficher le message d'au revoir.
session_start();
$_SESSION['message'] = 'Vous êtes déconnecté. À bientôt !';

header('Location: index.php');
exit;
