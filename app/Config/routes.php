<?php
declare(strict_types=1);

/**
 * Table de routage.
 *
 * Format :  'METHODE /chemin' => [Controleur::class, 'methode', 'auth?']
 * Le 3e element, optionnel, signifie « il faut etre connecte ».
 *
 * Les routes commentees en bas correspondent aux fonctionnalites
 * pas encore developpees (affaires, votes, profil, classement).
 */

use App\Controllers\Web\AuthController;
use App\Controllers\Web\AffaireController;
use App\Controllers\Web\HomeController;

return [

    // ---- Accueil -----------------------------------------------------
    'GET /'               => [HomeController::class, 'index'],

    // ---- Inscription et connexion (F1) -------------------------------
    'GET  /inscription'   => [AuthController::class, 'formulaireInscription'],
    'POST /inscription'   => [AuthController::class, 'inscription'],

    'GET  /connexion'     => [AuthController::class, 'formulaireConnexion'],
    'POST /connexion'     => [AuthController::class, 'connexion'],

    'GET  /deconnexion'   => [AuthController::class, 'deconnexion'],

    // ---- Affaires ----------------------------------------------------
    'GET  /affaires/creer' => [AffaireController::class, 'formulaireCreation', 'auth'],
    'POST /affaires'       => [AffaireController::class, 'creer',              'auth'],

    // ---- A venir -----------------------------------------------------
    // 'GET  /affaires/{id}'         => [AffaireController::class, 'detail'],
    // 'GET  /profil'                => [ProfilController::class,  'index',              'auth'],
    // 'GET  /classement'            => [ClassementController::class, 'index'],
    //
    // API REST consommee par fetch (votes en direct, F5 et F6) :
    // 'POST /api/affaires/{id}/vote'      => [VoteApiController::class, 'voter',     'auth'],
    // 'GET  /api/affaires/{id}/resultats' => [VoteApiController::class, 'resultats'],
];
