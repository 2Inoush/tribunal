<?php
declare(strict_types=1);

/**
 * Table de routage.
 * Format : 'METHODE URI' => [Controleur::class, 'methode', 'middleware?']
 *
 * Deux familles de routes :
 *   - Web  : rend une vue HTML (premier chargement, referencement, no-JS)
 *   - Api  : repond en JSON, consommee par fetch (cahier des charges, section 7)
 */

use App\Controllers\Web\{HomeController, AuthController, AffaireController,
                         ProfilController, ClassementController, PageController};
use App\Controllers\Api\{AuthApiController, AffaireApiController,
                         VoteApiController, ClassementApiController};

return [

    // ---- Pages -------------------------------------------------------
    'GET /'                     => [HomeController::class,       'index'],
    'GET /connexion'            => [AuthController::class,       'formulaireConnexion'],
    'GET /inscription'          => [AuthController::class,       'formulaireInscription'],
    'GET /deconnexion'          => [AuthController::class,       'deconnexion'],
    'GET /affaires/creer'       => [AffaireController::class,    'formulaireCreation', 'auth'],
    'GET /affaires/{id}'        => [AffaireController::class,    'detail'],
    'GET /affaires/{id}/modifier'=> [AffaireController::class,   'formulaireModification', 'auth'],
    'GET /profil'               => [ProfilController::class,     'index', 'auth'],
    'GET /classement'           => [ClassementController::class, 'index'],
    'GET /reglement'            => [PageController::class,       'reglement'],

    // ---- API REST ----------------------------------------------------
    'POST   /api/inscription'          => [AuthApiController::class,       'inscription'],
    'POST   /api/connexion'            => [AuthApiController::class,       'connexion'],
    'POST   /api/deconnexion'          => [AuthApiController::class,       'deconnexion'],

    'GET    /api/affaires'             => [AffaireApiController::class,    'lister'],
    'GET    /api/affaires/{id}'        => [AffaireApiController::class,    'afficher'],
    'POST   /api/affaires'             => [AffaireApiController::class,    'creer',     'auth'],
    'PUT    /api/affaires/{id}'        => [AffaireApiController::class,    'modifier',  'auth|owner'],
    'DELETE /api/affaires/{id}'        => [AffaireApiController::class,    'supprimer', 'auth|owner'],

    'POST   /api/affaires/{id}/vote'      => [VoteApiController::class,    'voter',     'auth'],
    'GET    /api/affaires/{id}/resultats' => [VoteApiController::class,    'resultats'],

    'GET    /api/classement'           => [ClassementApiController::class, 'index'],
];
