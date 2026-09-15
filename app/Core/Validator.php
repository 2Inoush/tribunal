<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Validator
 *
 * Verifie les donnees envoyees par un formulaire.
 * On accumule les erreurs dans un tableau : « nom du champ » => « message »,
 * ce qui permet d'afficher le message juste sous le bon champ.
 *
 * Utilisation :
 *     $v = new Validator($_POST);
 *     $v->requis('pseudo', 'Le pseudo est obligatoire.')
 *       ->longueur('pseudo', 3, 30, 'Le pseudo doit faire entre 3 et 30 caracteres.');
 *     if ($v->echoue()) { ... $v->erreurs() ... }
 */
final class Validator
{
    private array $erreurs = [];

    public function __construct(private array $donnees)
    {
    }

    public function requis(string $champ, string $message): self
    {
        if (trim((string) ($this->donnees[$champ] ?? '')) === '') {
            $this->ajouter($champ, $message);
        }

        return $this;
    }

    public function longueur(string $champ, int $min, int $max, string $message): self
    {
        $valeur = trim((string) ($this->donnees[$champ] ?? ''));

        // Si le champ est vide, c'est « requis » qui l'a deja signale :
        // inutile d'afficher deux messages pour le meme champ.
        if ($valeur === '') {
            return $this;
        }

        $taille = mb_strlen($valeur);
        if ($taille < $min || $taille > $max) {
            $this->ajouter($champ, $message);
        }

        return $this;
    }

    public function email(string $champ, string $message): self
    {
        $valeur = trim((string) ($this->donnees[$champ] ?? ''));

        if ($valeur !== '' && !filter_var($valeur, FILTER_VALIDATE_EMAIL)) {
            $this->ajouter($champ, $message);
        }

        return $this;
    }

    /** Verifie que deux champs sont identiques (mot de passe et sa confirmation). */
    public function identiques(string $champ, string $autreChamp, string $message): self
    {
        if (($this->donnees[$champ] ?? '') !== ($this->donnees[$autreChamp] ?? '')) {
            $this->ajouter($champ, $message);
        }

        return $this;
    }

    public function echoue(): bool
    {
        return $this->erreurs !== [];
    }

    public function erreurs(): array
    {
        return $this->erreurs;
    }

    /** Permet d'ajouter une erreur venant d'ailleurs (ex. « email deja utilise »). */
    public function ajouter(string $champ, string $message): self
    {
        // On garde seulement la premiere erreur de chaque champ.
        $this->erreurs[$champ] ??= $message;

        return $this;
    }
}
