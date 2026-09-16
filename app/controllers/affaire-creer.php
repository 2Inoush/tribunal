<?php
/**
 * CONTROLEUR : creer une affaire  (fonctionnalite F2)
 *
 * Reprend le formulaire ecrit par Ines, adapte a l'architecture actuelle.
 *
 * Il faut etre connecte pour deposer une affaire : on le verifie ici,
 * cote serveur. Cacher le lien dans le menu ne suffirait pas, quelqu'un
 * pourrait taper l'adresse directement.
 */

require_once __DIR__ . '/../models/affaire.php';

if (!estConnecte()) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour créer une affaire.';
    header('Location: connexion.php');
    exit;
}

// Longueurs attendues (elles correspondent aux contraintes de la base,
// voir database/schema.sql)
const TITRE_MIN       = 5;
const TITRE_MAX       = 120;
const DESCRIPTION_MIN = 20;
const ARGUMENT_MIN    = 10;

$erreurs = [];

// Valeurs saisies, reaffichees si le formulaire est refuse.
$titreAffaire = '';
$description  = '';
$argument1    = '';
$argument2    = '';


// =====================================================================
//  1. TRAITEMENT DU FORMULAIRE
// =====================================================================

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $titreAffaire = trim($_POST['titre'] ?? '');
    $description  = trim($_POST['description'] ?? '');
    $argument1    = trim($_POST['argument_1'] ?? '');
    $argument2    = trim($_POST['argument_2'] ?? '');

    // ---- Titre ----
    if ($titreAffaire === '') {
        $erreurs['titre'] = 'Le titre est obligatoire.';
    } elseif (mb_strlen($titreAffaire) < TITRE_MIN || mb_strlen($titreAffaire) > TITRE_MAX) {
        $erreurs['titre'] = 'Le titre doit faire entre ' . TITRE_MIN . ' et ' . TITRE_MAX . ' caractères.';
    }

    // ---- Les faits ----
    if ($description === '') {
        $erreurs['description'] = 'Les faits sont obligatoires.';
    } elseif (mb_strlen($description) < DESCRIPTION_MIN) {
        $erreurs['description'] = 'Les faits doivent faire au moins ' . DESCRIPTION_MIN . ' caractères.';
    }

    // ---- Premier argument (obligatoire) ----
    if ($argument1 === '') {
        $erreurs['argument_1'] = 'Le premier argument est obligatoire.';
    } elseif (mb_strlen($argument1) < ARGUMENT_MIN) {
        $erreurs['argument_1'] = 'Le premier argument doit faire au moins ' . ARGUMENT_MIN . ' caractères.';
    }

    // ---- Deuxieme argument (facultatif, mais pas bacle s'il est rempli) ----
    if ($argument2 !== '' && mb_strlen($argument2) < ARGUMENT_MIN) {
        $erreurs['argument_2'] = 'Le deuxième argument doit faire au moins ' . ARGUMENT_MIN . ' caractères.';
    }

    // ---- Aucune erreur : on enregistre ----
    if (count($erreurs) === 0) {

        $utilisateur = utilisateurConnecte();

        creerAffaire(
            $pdo,
            (int) $utilisateur['id'],
            $titreAffaire,
            $description,
            $argument1,
            // Un champ vide devient NULL en base, pas une chaine vide.
            $argument2 === '' ? null : $argument2
        );

        $_SESSION['message'] = 'Votre affaire a été créée.';

        header('Location: index.php');
        exit;
    }
}


// =====================================================================
//  2. AFFICHAGE DE LA VUE
// =====================================================================

$titre = 'Créer une affaire';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/affaire-creer.php';
require __DIR__ . '/../views/footer.php';
