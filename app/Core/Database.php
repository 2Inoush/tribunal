<?php
declare(strict_types=1);

namespace App\Core;

use PDO;

/**
 * Database
 *
 * Ouvre UNE seule connexion a MySQL pour toute la duree de la page,
 * et la reutilise ensuite (c'est le principe du « singleton »).
 *
 * On utilise PDO avec des requetes preparees : c'est ce qui protege
 * des injections SQL.
 */
final class Database
{
    /** La connexion, gardee de cote apres le premier appel. */
    private static ?PDO $pdo = null;

    public static function connexion(): PDO
    {
        // Deja connecte ? On renvoie la connexion existante.
        if (self::$pdo !== null) {
            return self::$pdo;
        }

        $config = require RACINE . '/app/Config/database.php';

        // Exemple de DSN : mysql:host=127.0.0.1;port=8889;dbname=tribunal;charset=utf8mb4
        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            $config['hote'],
            $config['port'],
            $config['base'],
            $config['charset']
        );

        try {
            self::$pdo = new PDO(
                $dsn,
                $config['utilisateur'],
                $config['mot_de_passe'],
                $config['options']
            );
        } catch (\PDOException $e) {
            // On n'affiche jamais le message brut de MySQL a l'utilisateur :
            // il peut contenir le mot de passe de la base.
            throw new \RuntimeException(
                'Connexion a la base de donnees impossible. '
                . 'Verifiez le fichier .env et que MySQL est bien demarre.',
                0,
                $e
            );
        }

        return self::$pdo;
    }
}
