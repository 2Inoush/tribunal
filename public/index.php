<?php
declare(strict_types=1);

/**
 * Front controller — unique point d'entree de l'application.
 * Le serveur web pointe sur /public : tout le reste du code
 * (app/, database/, storage/) n'est pas accessible depuis le navigateur.
 *
 * TODO : brancher l'autoloader, la session et le routeur.
 */

define('RACINE', dirname(__DIR__));

require RACINE . '/app/Core/Autoloader.php';
require RACINE . '/app/Helpers/functions.php';

// $config  = require RACINE . '/app/Config/config.php';
// $routes  = require RACINE . '/app/Config/routes.php';
//
// App\Core\Session::demarrer($config['session']);
// (new App\Core\Router($routes))->resoudre(new App\Core\Request());

echo 'Le Tribunal des Disputes Minuscules — squelette en place.';
