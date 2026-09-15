<?php
declare(strict_types=1);

namespace App\Core;

/**
 * Router
 *
 * Fait le lien entre une URL et la methode d'un controleur.
 * Les routes sont declarees dans app/Config/routes.php, sous la forme :
 *
 *     'GET /connexion' => [AuthController::class, 'formulaireConnexion']
 *     'GET /affaires/{id}' => [AffaireController::class, 'detail', 'auth']
 *
 * Le 3e element, optionnel, indique qu'il faut etre connecte.
 */
final class Router
{
    public function __construct(private array $routes)
    {
    }

    public function resoudre(string $methode, string $uri): void
    {
        foreach ($this->routes as $route => $action) {

            // 'GET /affaires/{id}'  ->  $routeMethode = 'GET', $routeUri = '/affaires/{id}'
            [$routeMethode, $routeUri] = preg_split('/\s+/', trim($route), 2);

            if ($routeMethode !== $methode) {
                continue;
            }

            $parametres = $this->correspond($routeUri, $uri);
            if ($parametres === null) {
                continue;   // cette route ne correspond pas, on passe a la suivante
            }

            // La route demande d'etre connecte ?
            $protection = $action[2] ?? null;
            if ($protection !== null && str_contains($protection, 'auth') && !Session::estConnecte()) {
                Session::flash('erreur', 'Vous devez etre connecte pour acceder a cette page.');
                header('Location: ' . url('/connexion'));
                exit;
            }

            [$classe, $methodeControleur] = $action;
            (new $classe())->$methodeControleur(...$parametres);

            return;
        }

        $this->pageIntrouvable();
    }

    /**
     * Compare l'URL demandee au motif de la route.
     *
     * Renvoie la liste des valeurs dynamiques si ca correspond
     * (['12'] pour /affaires/12 face au motif /affaires/{id}),
     * ou null si ca ne correspond pas.
     */
    private function correspond(string $motif, string $uri): ?array
    {
        // Cas simple : aucune partie dynamique.
        if (!str_contains($motif, '{')) {
            return $motif === $uri ? [] : null;
        }

        // /affaires/{id}  ->  expression reguliere  #^/affaires/([^/]+)$#
        $regex = preg_replace('#\{[a-z_]+\}#', '([^/]+)', $motif);

        if (!preg_match('#^' . $regex . '$#', $uri, $correspondances)) {
            return null;
        }

        array_shift($correspondances);   // on retire l'URL complete, on garde les valeurs

        return $correspondances;
    }

    private function pageIntrouvable(): void
    {
        http_response_code(404);
        require RACINE . '/app/Views/errors/404.php';
        exit;
    }
}
