<?php
/**
 * VUE : page d'accueil — la carte de l'affaire a juger.
 *
 * Mise en page ecrite par Enora.s
 */







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
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ]
    );
} catch (PDOException $erreur) {
    die('Impossible de se connecter à la base.');
}




if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choix'], $_POST['id_affaire'])) {

    $idUtilisateur = (int) $_SESSION['utilisateur']['id'];

    $idAffaire = (int) $_POST['id_affaire'];

    $choix = $_POST['choix'];

    $choixAutorises = ['coupable', 'acquitte'];

    if (!in_array($choix, $choixAutorises, true)) {
        die('Choix invalide.');
    }

    $stmt = $pdo->prepare(
        "INSERT INTO vote (id_affaire, id_utilisateur, choix)
         VALUES (:id_affaire, :id_utilisateur, :choix)"
    );

    $stmt->execute([
        ':id_affaire'     => $idAffaire,
        ':id_utilisateur' => $idUtilisateur,
        ':choix'          => $choix,
    ]);

    $messageVote = "Votre vote « $choix » a été enregistré !";
}




$stmt = $pdo->query(
    "SELECT id, titre, description, argument_1, argument_2
     FROM affaire
     ORDER BY RAND()
     LIMIT 1"
);

$affaire = $stmt->fetch();



?>




<div class=" py-2 lg:py-12">
    <p class="text-5xl mb-4 text-center" aria-hidden="true">&#9878;</p>

    <?php if (estConnecte()): ?>
        
        

        <?php if ($affaire): ?>
            <div class="rounded-lg shadow-md bg-white p-6">
            <h2 class="text-xl font-bold mb-2"><?= e($affaire['titre']) ?></h2>
            <p class="text-gray-600">
                <?= (e($affaire['description'])) ?>
                <br>
                <?= e($affaire['id']) ?>
                
            
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
                    <?= e($affaire['argument_1']) ?>
                </div>
                <?php if ($affaire['argument_2']): ?>
                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= e($affaire['argument_2']) ?>
                </div>
                <?php endif; ?>
            </details>
            </div>
            
            

            <form method="POST">
                <input
                    type="hidden"
                    name="id_affaire"
                    value="<?= (int) $affaire['id'] ?>"
                >
                <a href="">
                    <button type="submit" name="choix" value="acquitte" 
                class="text-white rounded-lg shadow-md bg-green-400 px-8 py-4  hover:bg-acquitte  font-medium  text-sm  text-center leading-5">Acquitté</button>
                </a>
                <a href="">
                    <button type="submit" name="choix" value="coupable"
                class="text-white rounded-lg shadow-md bg-red-400 px-8 py-4  hover:bg-coupable  font-medium  text-sm  text-center leading-5">Coupable</button>
                </a>
            </form>
        </div>



        <?php else: ?>

            <p>Aucune affaire disponible.</p>

        <?php endif; ?>
        
        

    <?php else: ?>

        <h1 class="text-3xl font-bold mb-3 text-center" style="font-family: Georgia, serif;">
            La séance est ouverte
        </h1>
        <br>
        <p class="text-gray-600 text-center">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

</div>
