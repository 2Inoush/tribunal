<?php
/**
 * Configuration du site.
 *
 * Ce fichier est inclus au debut de CHAQUE page. Il fait quatre choses :
 *   1. definir les reglages (base de donnees, mode developpement)
 *   2. ouvrir la session
 *   3. se connecter a la base
 *   4. definir quelques fonctions utiles partout
 *
 * ---------------------------------------------------------------------
 *  IMPORTANT — ne modifiez PAS les reglages ci-dessous pour votre machine,
 *  et n'ajoutez surtout pas de deuxieme bloc de variables plus bas dans
 *  le fichier : la derniere valeur ecrite est celle qui compte, et on se
 *  retrouve avec une connexion qui echoue sans comprendre pourquoi.
 *
 *  Ce fichier est partage sur Git : si vous y mettez vos identifiants,
 *  vous cassez le projet chez les autres a leur prochain pull.
 *
 *  Creez plutot le fichier  app/config-local.php  (non versionne),
 *  en partant du modele  app/config-local.exemple.php.
 *  Il est charge juste apres et remplace les valeurs par defaut.
 * ---------------------------------------------------------------------
 */


// =====================================================================
//  1. REGLAGES
// =====================================================================

// Mode developpement : true sur votre ordinateur, false une fois en ligne.
// Il ne change qu'une chose : l'affichage des erreurs (voir section 2).
$modeDeveloppement = true;

// Base de donnees — valeurs par defaut : MAMP.
$hote        = '127.0.0.1';
$port        = '3306';
$nomBase     = 'tribunal';
$identifiant = 'root';
$motDePasse  = '';


// ---- Reglages propres a la machine (fichier non versionne) ----------
// C'est le SEUL endroit ou ces variables doivent etre redefinies.
if (file_exists(__DIR__ . '/config-local.php')) {
    require __DIR__ . '/config-local.php';
}


// =====================================================================
//  2. AFFICHAGE DES ERREURS
// =====================================================================

if ($modeDeveloppement) {

    // Sur notre ordinateur : on veut voir les erreurs pour corriger.
    ini_set('display_errors', '1');
    error_reporting(E_ALL);

} else {

    // En ligne : jamais d'erreur affichee au visiteur. Un message PHP
    // peut reveler les chemins des fichiers, voire les identifiants
    // de la base. On les enregistre dans un fichier a la place.
    ini_set('display_errors', '0');
    error_reporting(E_ALL);
    ini_set('log_errors', '1');
}


// =====================================================================
//  3. SESSION
// =====================================================================
// La session permet de retenir qui est connecte d'une page a l'autre.
// session_start() doit etre appele avant tout affichage.

session_start();


// =====================================================================
//  4. CONNEXION A LA BASE DE DONNEES
// =====================================================================

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

    if ($modeDeveloppement) {
        // En developpement : un message qui aide a trouver la panne.
        // Il affiche les reglages REELLEMENT utilises au moment de la
        // connexion, ce qui permet de reperer tout de suite une valeur
        // ecrasee ailleurs dans le fichier.
        die('Connexion a la base impossible.<br>'
            . "Reglages utilises : $identifiant@$hote:$port, base « $nomBase ».<br>"
            . 'Verifiez que MySQL est demarre, et que ces valeurs sont les bonnes.');
    }

    // En ligne : un message neutre. Le detail part dans le journal
    // d'erreurs du serveur, pas sous les yeux du visiteur.
    error_log('Connexion PDO impossible : ' . $erreur->getMessage());
    die('Le site est momentanement indisponible. Merci de reessayer plus tard.');
}


// =====================================================================
//  5. PETITES FONCTIONS UTILES
// =====================================================================

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
