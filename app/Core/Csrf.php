<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Csrf
 *
 * Protection contre le CSRF (Cross-Site Request Forgery) : un site
 * malveillant qui ferait envoyer un formulaire a notre site depuis le
 * navigateur de l'utilisateur, en profitant de sa session ouverte.
 *
 * Principe : chaque formulaire contient un jeton secret, connu de notre
 * seule session. Si le jeton recu ne correspond pas, on refuse.
 */
final class Csrf
{
    /** Cree le jeton au premier appel, puis renvoie toujours le meme. */
    public static function jeton(): string
    {
        if (empty($_SESSION['csrf'])) {
            $_SESSION['csrf'] = bin2hex(random_bytes(32));
        }

        return $_SESSION['csrf'];
    }

    public static function estValide(?string $jetonRecu): bool
    {
        if (empty($_SESSION['csrf']) || $jetonRecu === null) {
            return false;
        }

        // hash_equals compare sans laisser deviner le jeton par le temps de reponse.
        return hash_equals($_SESSION['csrf'], $jetonRecu);
    }

    /** Le champ cache a coller dans chaque formulaire. */
    public static function champ(): string
    {
        return '<input type="hidden" name="csrf" value="' . self::jeton() . '">';
    }
}
