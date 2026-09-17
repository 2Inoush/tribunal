<?php
/**
 * CONTROLEUR : classement des jures  (fonctionnalite F9)
 *
 * Affiche les dix jures les plus actifs, et permet de chercher
 * quelqu'un par son pseudo pour connaitre sa position exacte.
 *
 * Pas de formulaire a traiter ici : la recherche se fait en GET
 * (l'adresse devient classement.php?recherche=bob), ce qui permet
 * de partager ou de recharger la page sans rien perdre.
 */

require_once __DIR__ . '/../models/utilisateur.php';

// Ce que l'utilisateur a tape dans la barre de recherche.
$recherche = trim($_GET['recherche'] ?? '');

// Le classement complet, deja trie par le modele.
$classement = classementJures($pdo);


// ---------------------------------------------------------------------
//  1. Attribuer son rang a chaque jure
// ---------------------------------------------------------------------
// Le tableau est deja dans le bon ordre : le rang est donc simplement
// la position dans la liste. On le range dans chaque ligne pour pouvoir
// l'afficher meme apres avoir filtre la liste.
// $position commence a 0, d'ou le « + 1 ».

foreach ($classement as $position => $ligne) {
    $classement[$position]['rang'] = $position + 1;
}


// ---------------------------------------------------------------------
//  2. Choisir ce qu'on affiche
// ---------------------------------------------------------------------

if ($recherche !== '') {

    // Recherche en cours : on garde les jures dont le pseudo contient
    // le texte tape. mb_stripos() ignore la casse et gere les accents,
    // donc « bob » trouve « GreffierBob ».
    $resultats = [];

    foreach ($classement as $ligne) {
        if (mb_stripos($ligne['pseudo'], $recherche) !== false) {
            $resultats[] = $ligne;
        }
    }

} else {

    // Pas de recherche : les dix premiers.
    // array_slice($tableau, 0, 10) prend les 10 premieres lignes.
    $resultats = array_slice($classement, 0, 10);
}


// ---------------------------------------------------------------------
//  3. La position du juré connecte
// ---------------------------------------------------------------------
// Si l'utilisateur connecte n'apparait pas dans ce qui est affiche,
// on le lui rappelle en bas de page : « Vous etes 14e ».
// Reste a null s'il est visible, ou si personne n'est connecte.

$maPosition = null;

if (estConnecte()) {

    $monId = (int) utilisateurConnecte()['id'];

    // Est-il deja visible dans la liste affichee ?
    $dejaVisible = false;
    foreach ($resultats as $ligne) {
        if ((int) $ligne['id'] === $monId) {
            $dejaVisible = true;
            break;
        }
    }

    if (!$dejaVisible) {
        foreach ($classement as $ligne) {
            if ((int) $ligne['id'] === $monId) {
                $maPosition = $ligne;
                break;
            }
        }
    }
}


// ---------------------------------------------------------------------
//  4. Affichage de la vue
// ---------------------------------------------------------------------

$titre = 'Classement des jurés';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/classement.php';
require __DIR__ . '/../views/footer.php';
