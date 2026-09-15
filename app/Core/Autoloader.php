<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Autoloader
 *
 * Evite d'ecrire un require pour chaque classe.
 * Des qu'on utilise une classe App\Quelque\Chose, PHP appelle
 * la fonction ci-dessous, qui va chercher app/Quelque/Chose.php.
 *
 * Exemple : App\Models\Utilisateur  ->  app/Models/Utilisateur.php
 */
final class Autoloader
{
    public static function enregistrer(): void
    {
        spl_autoload_register(function (string $classe): void {

            // On ne s'occupe que de nos classes (celles qui commencent par « App\ »)
            if (!str_starts_with($classe, 'App\\')) {
                return;
            }

            // On enleve le « App\ » du debut, puis on remplace les \ par des /
            $chemin = substr($classe, strlen('App\\'));
            $chemin = RACINE . '/app/' . str_replace('\\', '/', $chemin) . '.php';

            if (is_file($chemin)) {
                require $chemin;
            }
        });
    }
}
