<?php
declare(strict_types=1);

/**
 * Parametres de connexion a la base MySQL / MariaDB.
 * Schema : database/schema.sql
 */

return [
    'hote'         => getenv('DB_HOST') ?: '127.0.0.1',
    'port'         => getenv('DB_PORT') ?: '3306',
    'base'         => getenv('DB_NAME') ?: 'tribunal',
    'utilisateur'  => getenv('DB_USER') ?: 'root',
    'mot_de_passe' => getenv('DB_PASS') ?: '',
    'charset'      => 'utf8mb4',

    'options' => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,   // vraies requetes preparees
    ],
];
