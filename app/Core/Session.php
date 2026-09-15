<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Session
 *
 * Tout ce qui concerne la session PHP : demarrage, utilisateur connecte,
 * et « messages flash » (un message affiche une seule fois, par exemple
 * « Compte cree avec succes », puis efface).
 */
final class Session
{
    public static function demarrer(array $config): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name($config['nom']);
        session_set_cookie_params([
            'lifetime' => $config['duree'],
            'path'     => '/',
            'httponly' => $config['httponly'],   // le cookie est invisible pour le JavaScript
            'samesite' => $config['samesite'],   // protection de base contre le CSRF
        ]);
        session_start();
    }

    // ---- Utilisateur connecte -------------------------------------------

    /** Enregistre l'utilisateur en session (appele apres inscription/connexion). */
    public static function connecter(array $utilisateur): void
    {
        // On regenere l'identifiant de session a la connexion :
        // cela empeche la « fixation de session » (un attaquant qui aurait
        // impose son propre identifiant de session a la victime).
        session_regenerate_id(true);

        $_SESSION['utilisateur'] = [
            'id'     => $utilisateur['id'],
            'pseudo' => $utilisateur['pseudo'],
            'email'  => $utilisateur['email'],
        ];
    }

    public static function deconnecter(): void
    {
        $_SESSION = [];
        session_destroy();
    }

    public static function estConnecte(): bool
    {
        return isset($_SESSION['utilisateur']);
    }

    /** Renvoie l'utilisateur connecte, ou null si personne ne l'est. */
    public static function utilisateur(): ?array
    {
        return $_SESSION['utilisateur'] ?? null;
    }

    // ---- Messages flash --------------------------------------------------

    public static function flash(string $type, string $message): void
    {
        $_SESSION['flash'][$type] = $message;
    }

    /** Lit le message puis l'efface : il ne s'affichera qu'une fois. */
    public static function lireFlash(string $type): ?string
    {
        $message = $_SESSION['flash'][$type] ?? null;
        unset($_SESSION['flash'][$type]);

        return $message;
    }

    // ---- Memorisation des champs de formulaire ---------------------------

    /**
     * Garde les valeurs saisies pour les reafficher apres une erreur :
     * l'utilisateur ne retape pas tout son formulaire.
     * On ne garde JAMAIS le mot de passe.
     */
    public static function memoriserSaisie(array $champs): void
    {
        unset($champs['mot_de_passe'], $champs['mot_de_passe_confirmation']);
        $_SESSION['ancienne_saisie'] = $champs;
    }

    public static function ancienneSaisie(): array
    {
        $saisie = $_SESSION['ancienne_saisie'] ?? [];
        unset($_SESSION['ancienne_saisie']);

        return $saisie;
    }

    public static function memoriserErreurs(array $erreurs): void
    {
        $_SESSION['erreurs'] = $erreurs;
    }

    public static function erreurs(): array
    {
        $erreurs = $_SESSION['erreurs'] ?? [];
        unset($_SESSION['erreurs']);

        return $erreurs;
    }
}
