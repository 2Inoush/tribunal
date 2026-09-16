<?php
/**
 * Configuration du site.
 *
 * Ce fichier est inclus au debut de CHAQUE page.
 * Il fait trois choses :
 *   1. ouvrir la session
 *   2. se connecter a la base de donnees
 *   3. definir deux petites fonctions utiles partout
 */

// ---------------------------------------------------------------------
// 1. Affichage des erreurs
// ---------------------------------------------------------------------
// Pratique pendant le developpement. A mettre a 0 si le site est mis en ligne.
ini_set('display_errors', '1');
error_reporting(E_ALL);


// ---------------------------------------------------------------------
// 2. Session
// ---------------------------------------------------------------------
// La session permet de retenir qui est connecte d'une page a l'autre.
// session_start() doit etre appele avant tout affichage.
session_start();


// ---------------------------------------------------------------------
// 3. Connexion a la base de donnees
// ---------------------------------------------------------------------
// Avec MAMP : MySQL ecoute sur le port 8889, login root, mot de passe root.
// (Si vous utilisez autre chose que MAMP, changez le port et le mot de passe.)
$hote        = '127.0.0.1';
$port        = '8889';
$nomBase     = 'tribunal';
$identifiant = 'root';
$motDePasse  = 'root';

// ---------------------------------------------------------------------
//  Configuration propre a chaque machine
// ---------------------------------------------------------------------
// Les valeurs ci-dessus sont celles de MAMP. Si votre installation est
// differente (XAMPP, WAMP, MySQL sous Linux...), ne les changez PAS ici :
// ce fichier est partage sur Git, et votre modification casserait le
// projet chez les autres au prochain pull.
//
// Creez plutot un fichier app/config-local.php, qui n'est pas versionne :
//
//     <?php
//     $port       = '3306';
//     $motDePasse = '';
//
// Il est charge juste apres et remplace les valeurs par defaut.
// Un modele est fourni : app/config-local.exemple.php
if (file_exists(__DIR__ . '/config-local.php')) {
    require __DIR__ . '/config-local.php';
}

try {
    $pdo = new PDO(
        "mysql:host=$hote;port=$port;dbname=$nomBase;charset=utf8mb4",
        $identifiant,
        $motDePasse,
        [
            // Si une requete echoue, PDO leve une erreur au lieu de l'ignorer
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Les resultats sont renvoyes sous forme de tableaux ['pseudo' => '...']
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $erreur) {
    die('Impossible de se connecter à la base. MySQL est-il démarré dans MAMP ?');
}


// ---------------------------------------------------------------------
// 4. Petites fonctions utiles
// ---------------------------------------------------------------------

/**
 * e() = « echapper ».
 *
 * A utiliser pour afficher TOUT texte venant de la base ou d'un formulaire.
 * Sans elle, quelqu'un qui mettrait <script>...</script> comme pseudo
 * verrait son code s'executer chez les autres visiteurs : c'est la faille XSS.
 *
 * Exemple :  <?= e($utilisateur['pseudo']) ?>
 */
function e(?string $texte): string
{
    return htmlspecialchars($texte ?? '', ENT_QUOTES, 'UTF-8');
}

/**
 * Renvoie l'utilisateur connecte, ou null si personne ne l'est.
 * L'utilisateur est range dans $_SESSION au moment de la connexion.
 */
function utilisateurConnecte(): ?array
{
    return $_SESSION['utilisateur'] ?? null;
}

/** Raccourci : y a-t-il quelqu'un de connecte ? */
function estConnecte(): bool
{
    return isset($_SESSION['utilisateur']);
}
