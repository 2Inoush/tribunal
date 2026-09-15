<?php
declare(strict_types=1);

/**
 * Configuration generale de l'application.
 * Les valeurs sensibles sont lues dans le fichier .env (non versionne).
 */

return [
    'app' => [
        'nom'      => 'Le Tribunal des Disputes Minuscules',
        'url'      => getenv('APP_URL') ?: 'http://localhost:8000',
        'debug'    => (getenv('APP_DEBUG') ?: 'true') === 'true',
        'timezone' => 'Europe/Paris',
        'charset'  => 'UTF-8',
    ],

    // Regles de validation partagees entre le PHP et le schema SQL
    'regles' => [
        'pseudo_min'      => 3,
        'pseudo_max'      => 30,
        'mot_de_passe_min'=> 8,
        'titre_min'       => 5,     // erreur F2 « titre trop court »
        'titre_max'       => 120,   // erreur F2 « titre trop long »
        'description_min' => 20,
        'argument_min'    => 10,
    ],

    'session' => [
        'nom'      => 'tribunal_session',
        'duree'    => 60 * 60 * 24 * 7,   // 7 jours
        'httponly' => true,
        'samesite' => 'Lax',
    ],
];
