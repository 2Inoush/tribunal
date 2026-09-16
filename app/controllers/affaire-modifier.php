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
// ---------------------------------------------------------------------
// Identique a affaire-supprimer.php : message dans $_SESSION['erreur'],
// header('Location: connexion.php'), puis exit.

// TODO


// ---------------------------------------------------------------------
//  2. Recuperer l'identifiant
// ---------------------------------------------------------------------
// $_GET['id'] est du texte et peut etre absent : le convertir en entier.
// Si l'identifiant est absent ou <= 0, retour au profil.

// TODO


// ---------------------------------------------------------------------
//  3. Charger l'affaire
// ---------------------------------------------------------------------
//     $affaire = trouverAffaire($pdo, $id);
// Elle renvoie null si l'affaire n'existe pas -> message + retour au profil.

// TODO


// ---------------------------------------------------------------------
//  4. Les deux controles de F3
// ---------------------------------------------------------------------
//  a) Est-ce bien SON affaire ?
//     Comparer (int) $affaire['id_utilisateur']
//          avec (int) utilisateurConnecte()['id'].
//     Sans ce controle, changer le chiffre dans l'adresse permettrait
//     de modifier l'affaire de n'importe qui.
//
//  b) A-t-elle deja recu un vote ?
//     $affaire['modifiable'] vaut 1 tant qu'aucun vote n'a ete depose,
//     0 des le premier. Si c'est 0 : message d'erreur + retour au profil.
//
//  Ces deux controles se font ici, cote serveur. Le bouton « Modifier »
//  disparait deja du profil dans ce cas, mais cacher un bouton
//  n'empeche personne de taper l'adresse a la main.

// TODO


// ---------------------------------------------------------------------
//  5. Traitement du formulaire (POST)
// ---------------------------------------------------------------------
//  if ($_SERVER['REQUEST_METHOD'] === 'POST') { ... }
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

// TODO


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

$titre = 'Modifier une affaire';

require __DIR__ . '/../views/header.php';
require __DIR__ . '/../views/affaire-modifier.php';
require __DIR__ . '/../views/footer.php';
