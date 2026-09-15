<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Utilisateur — table `utilisateur`
 *
 * Le modele est le SEUL endroit qui parle a la base pour cette table.
 * Les controleurs ne font jamais de SQL eux-memes.
 *
 * Toutes les requetes sont preparees (les « ? ») : les valeurs sont
 * envoyees separement du SQL, donc une saisie malveillante ne peut pas
 * modifier la requete. C'est la protection contre l'injection SQL.
 */
final class Utilisateur extends Model
{
    /**
     * Cree un compte et renvoie son identifiant.
     * Le mot de passe est hache ici : il n'est jamais stocke en clair.
     */
    public static function creer(string $pseudo, string $email, string $motDePasse): int
    {
        $sql = 'INSERT INTO utilisateur (pseudo, email, mot_de_passe)
                VALUES (?, ?, ?)';

        self::db()->prepare($sql)->execute([
            $pseudo,
            $email,
            password_hash($motDePasse, PASSWORD_DEFAULT),
        ]);

        return (int) self::db()->lastInsertId();
    }

    public static function trouverParId(int $id): ?array
    {
        $requete = self::db()->prepare('SELECT * FROM utilisateur WHERE id = ?');
        $requete->execute([$id]);

        // fetch() renvoie false s'il n'y a pas de resultat : on prefere null.
        return $requete->fetch() ?: null;
    }

    public static function trouverParEmail(string $email): ?array
    {
        $requete = self::db()->prepare('SELECT * FROM utilisateur WHERE email = ?');
        $requete->execute([$email]);

        return $requete->fetch() ?: null;
    }

    public static function emailExiste(string $email): bool
    {
        $requete = self::db()->prepare('SELECT 1 FROM utilisateur WHERE email = ?');
        $requete->execute([$email]);

        return (bool) $requete->fetchColumn();
    }

    public static function pseudoExiste(string $pseudo): bool
    {
        $requete = self::db()->prepare('SELECT 1 FROM utilisateur WHERE pseudo = ?');
        $requete->execute([$pseudo]);

        return (bool) $requete->fetchColumn();
    }

    /**
     * Verifie un couple email / mot de passe.
     * Renvoie l'utilisateur si tout est bon, sinon null.
     */
    public static function verifierIdentifiants(string $email, string $motDePasse): ?array
    {
        $utilisateur = self::trouverParEmail($email);

        if ($utilisateur === null) {
            // On calcule quand meme un hachage pour que la reponse prenne
            // le meme temps qu'avec un email existant : sinon, la duree de
            // reponse permettrait de deviner quels emails sont inscrits.
            password_verify($motDePasse, '$2y$10$usesomesillystringfore7hnbRJHxXVLeakoG8K30oukPsA.ztMG');

            return null;
        }

        if (!password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
            return null;
        }

        return $utilisateur;
    }
}
