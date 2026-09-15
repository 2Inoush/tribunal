<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Controller
 *
 * Classe de base des controleurs. Elle leur donne deux outils :
 *   - rendre()    : afficher une vue dans le gabarit principal
 *   - rediriger() : envoyer l'utilisateur vers une autre page
 */
abstract class Controller
{
    /**
     * Affiche une page.
     *
     * @param string $vue     nom du fichier dans app/Views/pages (sans .php)
     * @param array  $donnees variables mises a disposition de la vue
     */
    protected function rendre(string $vue, array $donnees = [], string $gabarit = 'main'): void
    {
        // extract() transforme ['titre' => 'Connexion'] en une variable $titre
        // directement utilisable dans le fichier de vue.
        extract($donnees);

        // On capture l'affichage de la vue dans une variable...
        ob_start();
        require RACINE . '/app/Views/pages/' . $vue . '.php';
        $contenu = ob_get_clean();

        // ... puis on l'insere dans le gabarit, qui contient le <html>, le header, etc.
        require RACINE . '/app/Views/layouts/' . $gabarit . '.php';
    }

    /** Redirige vers une page du site, puis arrete le script. */
    protected function rediriger(string $chemin): void
    {
        header('Location: ' . url($chemin));
        exit;
    }
}
