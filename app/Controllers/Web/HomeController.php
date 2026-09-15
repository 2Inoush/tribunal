<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;

/**
 * HomeController — page d'accueil.
 *
 * Pour l'instant la page est volontairement vide : elle sert seulement
 * de point d'arrivee apres l'inscription et la connexion.
 * C'est ici que viendra plus tard la pile d'affaires a juger (F5, F6).
 */
final class HomeController extends Controller
{
    public function index(): void
    {
        $this->rendre('accueil', [
            'titre' => 'Accueil',
        ]);
    }
}
