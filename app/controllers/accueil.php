<?php
/**
 * CONTROLEUR : accueil
 *
 * Tire une affaire au hasard et la passe a la vue.
 * C'est le debut de la « pile d'affaires a juger » du cahier des charges.
 */

require_once __DIR__ . '/../models/affaire.php';

// Peut valoir null si la base ne contient aucune affaire :
// la vue previent alors l'utilisateur, ce n'est pas une erreur.
$affaire = affaireAuHasard($pdo);

$titre = 'Accueil';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/accueil.php';
require __DIR__ . '/../views/footer.php';
