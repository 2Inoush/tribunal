<?php
/**
 * Page d'accueil — http://localhost:8888/tribunal/public/
 *
 * Ce fichier ne fait que deux choses : charger la configuration,
 * puis passer la main au controleur. Tout le travail est dans
 * app/controllers/accueil.php.
 */

require_once __DIR__ . '/../app/config.php';
require __DIR__ . '/../app/controllers/accueil.php';
