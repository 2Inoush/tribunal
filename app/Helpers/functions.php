<?php
declare(strict_types=1);

/**
 * Fonctions utilitaires disponibles partout (surtout dans les vues).
 */

/**
 * e() = « echapper ».
 *
 * A utiliser pour TOUT texte venant de la base ou d'un formulaire.
 * Sans elle, un utilisateur qui saisirait <script>...</script> comme pseudo
 * verrait son code execute chez les autres visiteurs : c'est la faille XSS.
 */
function e(?string $texte): string
{
    return htmlspecialchars((string) $texte, ENT_QUOTES, 'UTF-8');
}

/**
 * Construit une URL du site.
 *
 * Le projet peut etre installe a la racine du serveur (http://localhost:8000/)
 * ou dans un sous-dossier MAMP (http://localhost:8888/tribunal/public/).
 * BASE_URL, calcule dans public/index.php, contient ce prefixe eventuel ;
 * on ecrit donc toujours url('/connexion') et jamais '/connexion' en dur.
 */
function url(string $chemin = '/'): string
{
    return BASE_URL . '/' . ltrim($chemin, '/');
}

/** Meme chose pour les fichiers CSS, JS et images. */
function asset(string $chemin): string
{
    return BASE_URL . '/assets/' . ltrim($chemin, '/');
}

/**
 * Reaffiche ce que l'utilisateur avait saisi apres une erreur de formulaire.
 * $saisie est fourni a la vue par le controleur.
 */
function ancien(array $saisie, string $champ): string
{
    return e($saisie[$champ] ?? '');
}

/** Raccourci pour les vues : l'utilisateur est-il connecte ? */
function estConnecte(): bool
{
    return App\Core\Session::estConnecte();
}

/** Raccourci pour les vues : l'utilisateur connecte (ou null). */
function utilisateurConnecte(): ?array
{
    return App\Core\Session::utilisateur();
}

/**
 * Charge le fichier .env dans les variables d'environnement.
 *
 * Le .env contient les identifiants de la base : il n'est pas versionne
 * (voir .gitignore), chacun a le sien. Le modele est dans .env.example.
 */
function chargerEnv(string $fichier): void
{
    if (!is_file($fichier)) {
        return;
    }

    foreach (file($fichier, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $ligne) {
        $ligne = trim($ligne);

        // On saute les commentaires et les lignes sans « = »
        if ($ligne === '' || str_starts_with($ligne, '#') || !str_contains($ligne, '=')) {
            continue;
        }

        [$cle, $valeur] = explode('=', $ligne, 2);
        putenv(trim($cle) . '=' . trim($valeur));
    }
}
