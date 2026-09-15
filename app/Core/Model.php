<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Model
 *
 * Classe de base des modeles (Utilisateur, Affaire, Vote).
 * Elle ne sert qu'a leur donner un raccourci vers la connexion PDO.
 */
abstract class Model
{
    protected static function db(): PDO
    {
        return Database::connexion();
    }
}
