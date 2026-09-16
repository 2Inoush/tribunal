<?php
/**
 * Page d'accueil.
 *
 * Volontairement vide pour le moment : c'est ici que viendra la pile
 * d'affaires a juger (F5 et F6).
 */
/**$user = 'root';
$pass = '';

$db = new PDO ('mysql:host=localhost;dbname=tribunal', $user, $pass)*/
/**require_once __DIR__ . '/./config.php';*/
$hote        = '127.0.0.1';
$port        = '3306';
$nomBase     = 'tribunal';
$identifiant = 'root';
$motDePasse  = '';

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
    die('Impossible de se connecter à la base. MySQL est-il démarré dans XAMPP/MAMP ?');
}


$stmt = $pdo->query("SELECT id, titre, description, argument_1, argument_2 FROM affaire ORDER BY RAND() LIMIT 1");
$affaire = $stmt->fetch();
?>
<div class=" py-12">
    <p class="text-5xl mb-4 text-center" aria-hidden="true">&#9878;</p>

        <h1 class="font-titre text-3xl font-bold mb-3 text-center">
            La seance est ouverte
        </h1>

    <?php if (estConnecte()): ?>
        
        

        <?php if ($affaire): ?>
            <div class="rounded-lg shadow-md bg-white p-6">
            <h2 class="text-xl font-bold mb-2"><?= htmlspecialchars($affaire['titre'], ENT_QUOTES, 'UTF-8') ?></h2>
            <p class="text-gray-600">
                <?= nl2br(htmlspecialchars($affaire['description'], ENT_QUOTES, 'UTF-8')) ?>
            </p>
            <div class="w-full  ">
            <details class="group">
                <summary class="flex cursor-pointer  items-center justify-between py-4 font-semibold">
                <span>Voir les arguments</span>
                <span class="transition-transform group-open:rotate-180">
                    ↓
                </span>
                </summary>

                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= htmlspecialchars($affaire['argument_1'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php if ($affaire['argument_2']): ?>
                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= htmlspecialchars($affaire['argument_2'], ENT_QUOTES, 'UTF-8') ?>
                </div>
                <?php endif; ?>
            </details>
            </div>
            
            <a href="">
                <button type="button"
            class="text-white rounded-lg shadow-md bg-green-400 px-8 py-4  hover:bg-green-600  font-medium  text-sm  text-center leading-5">Acquitté</button>
            </a>
            <a href="">
                <button type="button"
            class="text-white rounded-lg shadow-md bg-red-400 px-8 py-4  hover:bg-red-600  font-medium  text-sm  text-center leading-5">Coupable</button>
            </a>
        </div>



        <?php else: ?>

            <p>Aucune affaire disponible.</p>

        <?php endif; ?>
        
        

    <?php else: ?>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

</div>
