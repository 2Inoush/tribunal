<?php
declare(strict_types=1);

/**
 * Front controller — le point d'entree unique.
 *
 * Grace au .htaccess, TOUTES les URL du site arrivent dans ce fichier.
 * Son role : preparer l'application, puis confier l'URL au routeur.
 */

// ---------------------------------------------------------------------
// 1. Chemins
// ---------------------------------------------------------------------

/** Dossier racine du projet (le dossier qui contient app/, public/, etc.). */
define('RACINE', dirname(__DIR__));

require RACINE . '/app/Helpers/functions.php';
require RACINE . '/app/Core/Autoloader.php';

App\Core\Autoloader::enregistrer();
chargerEnv(RACINE . '/.env');

// ---------------------------------------------------------------------
// 2. Configuration
// ---------------------------------------------------------------------

$config = require RACINE . '/app/Config/config.php';

date_default_timezone_set($config['app']['timezone']);

// En developpement on veut voir les erreurs ; en production, jamais
// (elles pourraient reveler des chemins ou des identifiants).
if ($config['app']['debug']) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('error_log', RACINE . '/storage/logs/erreurs.log');
}

// ---------------------------------------------------------------------
// 3. Adresse de base du site
// ---------------------------------------------------------------------

/**
 * Le projet peut tourner a deux endroits differents :
 *   - MAMP          : http://localhost:8888/tribunal/public/  -> prefixe « /tribunal/public »
 *   - serveur PHP   : http://localhost:8000/                  -> prefixe vide
 *
 * On deduit ce prefixe du chemin de index.php, pour que les liens
 * fonctionnent dans les deux cas (voir la fonction url()).
 */
$base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');
define('BASE_URL', $base === '/' ? '' : $base);

// ---------------------------------------------------------------------
// 4. Session
// ---------------------------------------------------------------------

App\Core\Session::demarrer($config['session']);

// ---------------------------------------------------------------------
// 5. Routage
// ---------------------------------------------------------------------

$methode = $_SERVER['REQUEST_METHOD'];

// On isole le chemin demande, sans le prefixe ni les parametres ?x=y
// Exemple : /tribunal/public/connexion?erreur=1  ->  /connexion
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
if (BASE_URL !== '' && str_starts_with($uri, BASE_URL)) {
    $uri = substr($uri, strlen(BASE_URL));
}
$uri = '/' . trim($uri, '/');

$routes = require RACINE . '/app/Config/routes.php';

(new App\Core\Router($routes))->resoudre($methode, $uri);
