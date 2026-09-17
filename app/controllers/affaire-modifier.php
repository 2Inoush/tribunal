<?php
/**
 * CONTROLEUR : modifier une affaire  (fonctionnalite F3)
 *
 * =====================================================================
 *  POUR INES — le code est a ecrire, la structure est posee.
 *
 *  Un exemple complet et tres proche existe deja :
 *      app/controllers/affaire-supprimer.php
 *  Les etapes 1 a 5 ci-dessous y sont ecrites presque a l'identique.
 *  Ouvre-le a cote, c'est le plus simple.
 *
 *  Ce qui est deja pret :
 *    - la vue                app/views/affaire-modifier.php
 *    - les fonctions SQL     trouverAffaire() et modifierAffaire()
 *                            dans app/models/affaire.php
 *    - le bouton « Modifier » dans le profil, qui n'apparait que si
 *      l'affaire n'a aucun vote
 * =====================================================================
 *
 * Cahier des charges (F3) :
 *   « modification possible uniquement si aucun vote n'a encore ete depose »
 *   Erreurs : modification tentee apres un premier vote,
 *             ou par un autre utilisateur que l'auteur.
 *
 * Adresse de la page :  affaire-modifier.php?id=12   ->  $_GET['id']
 */

require_once __DIR__ . '/../models/affaire.php';


// ---------------------------------------------------------------------
//  1. Il faut etre connecte

if (!estConnecte()) {
    $_SESSION['erreur'] = 'Vous devez être connecté pour modifier une affaire.';
    header('Location: connexion.php');
    exit;
}

// ---------------------------------------------------------------------
//  2. Recuperer l'identifiant
// ---------------------------------------------------------------------
// $_GET['id'] est du texte et peut etre absent : le convertir en entier.
// Si l'identifiant est absent ou <= 0, retour au profil.

$id = (int) ($_GET['id'] ?? 0);

if ($id <= 0) {
    $_SESSION['erreur'] = 'Affaire introuvable.';
    header('Location: profil.php');
    exit;
}


// ---------------------------------------------------------------------
//  3. Charger l'affaire
// ---------------------------------------------------------------------
//     $affaire = trouverAffaire($pdo, $id);
// Elle renvoie null si l'affaire n'existe pas -> message + retour au profil.

$affaire = trouverAffaire($pdo, $id);

if ($affaire === null) {
    $_SESSION['erreur'] = "Cette affaire n'existe pas.";
    header('Location: profil.php');
    exit;
}

// ---------------------------------------------------------------------
//  4. Les deux controles de F3
// ---------------------------------------------------------------------
//  a) Est-ce bien SON affaire ?
//     Comparer (int) $affaire['id_utilisateur']
//          avec (int) utilisateurConnecte()['id'].
//     Sans ce controle, changer le chiffre dans l'adresse permettrait
//     de modifier l'affaire de n'importe qui.

if ((int) $affaire['id_utilisateur'] !== (int) utilisateurConnecte()['id']) {
    $_SESSION['erreur'] = 'Vous ne pouvez modifier que vos propres affaires.';
    header('Location: profil.php');
    exit;
}


//
//  b) A-t-elle deja recu un vote ?
//     $affaire['modifiable'] vaut 1 tant qu'aucun vote n'a ete depose,
//     0 des le premier. Si c'est 0 : message d'erreur + retour au profil.


if ((int) $affaire['modifiable'] === 0) {
    $_SESSION['erreur'] = 'Cette affaire a déjà reçu un vote et ne peut plus être modifiée.';
    header('Location: profil.php');
    exit;
}
//  Ces deux controles se font ici, cote serveur. Le bouton « Modifier »
//  disparait deja du profil dans ce cas, mais cacher un bouton
//  n'empeche personne de taper l'adresse a la main.
// ---------------------------------------------------------------------
//  5. Traitement du formulaire (POST)
// ---------------------------------------------------------------------

const TITRE_MIN       = 5;
const TITRE_MAX       = 120;
const DESCRIPTION_MIN = 20;
const ARGUMENT_MIN    = 10;

$erreurs = [];

$titreAffaire = '';
$description  = '';
$argument1    = '';
$argument2    = '';


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

        modifierAffaire(
            $pdo,
            $id,
            $titreAffaire,
            $description,
            $argument1,
            // Un champ vide devient NULL en base, pas une chaine vide.
            $argument2 === '' ? null : $argument2
        );

        $_SESSION['message'] = 'Votre affaire a été modifiée.';

        header('Location: profil.php');
        exit;
    }
}

//
//  Les regles de verification sont EXACTEMENT celles de la creation :
//  le plus rapide est de recopier le bloc de app/controllers/affaire-creer.php
//    - titre        : obligatoire, entre 5 et 120 caracteres
//    - description  : obligatoire, au moins 20 caracteres
//    - argument_1   : obligatoire, au moins 10 caracteres
//    - argument_2   : facultatif, mais au moins 10 caracteres s'il est rempli
//
//  Si aucune erreur :
//      modifierAffaire($pdo, $id, $titreAffaire, $description,
//                      $argument1, $argument2 === '' ? null : $argument2);
//      $_SESSION['message'] = 'Votre affaire a été modifiée.';
//      header('Location: profil.php');
//      exit;
//
//  Pas besoin de toucher a date_modification : la base s'en occupe seule.

// ---------------------------------------------------------------------
//  6. Affichage de la vue
// ---------------------------------------------------------------------
//  La vue attend ces variables (voir le commentaire en tete du fichier) :
//      $affaire, $erreurs, $titreAffaire, $description, $argument1, $argument2
//
//  Au premier affichage (GET), les remplir avec le contenu actuel :
//      $titreAffaire = $affaire['titre'];
//      $description  = $affaire['description'];       etc.
//
//  Apres un POST refuse, elles contiennent deja ce que l'utilisateur
//  venait de taper : ne pas les ecraser.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $titreAffaire = $affaire['titre'];
    $description  = $affaire['description'];
    $argument1    = $affaire['argument_1'];
    $argument2    = $affaire['argument_2'] ?? '';
}

$titre = 'Modifier une affaire';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/affaire-modifier.php';
require __DIR__ . '/../views/footer.php';
