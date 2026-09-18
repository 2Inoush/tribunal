<?php
/**
 * VUE : page d'accueil — la carte de l'affaire a juger.
 *
 * Mise en page ecrite par Enora.s
 */

$affaire = null;
$resultats = null;
$messageVote = null;
$voteEffectue = false;

// verif si user connecté 
if (estConnecte()) {

    $idUtilisateur = (int) $_SESSION['utilisateur']['id'];

    //envoi des données de l'affaire à juger → acquitte ou coupable dans tab vote en fonction de l'id user et id affaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['choix'], $_POST['id_affaire'])) {

        $idAffaire = (int) $_POST['id_affaire'];
        $choix = $_POST['choix'];

        $choixAutorises = ['coupable', 'acquitte'];

        if (!in_array($choix, $choixAutorises, true)) {
            die('Choix invalide.');
        }

        //verification si l'utilisateur a déjà voté pour cette affaire
        $verification = $pdo->prepare(
            "SELECT id
             FROM vote
             WHERE id_affaire = :id_affaire
               AND id_utilisateur = :id_utilisateur"
        );

        $verification->execute([
            ':id_affaire' => $idAffaire,
            ':id_utilisateur' => $idUtilisateur,
        ]);

        if ($verification->fetch()) {
            die('Vous avez déjà voté pour cette affaire.');
        }

        $stmt = $pdo->prepare(
            "INSERT INTO vote (id_affaire, id_utilisateur, choix)
             VALUES (:id_affaire, :id_utilisateur, :choix)"
        );

        $stmt->execute([
            ':id_affaire' => $idAffaire,
            ':id_utilisateur' => $idUtilisateur,
            ':choix' => $choix,
        ]);

        $messageVote = "$choix enregistré";

        // Récupération des statistiques de l'affaire qui vient d'être votée
        $stmtStats = $pdo->prepare(
            "SELECT total_votes, votes_acquitte, votes_coupable,
                    pct_acquitte, pct_coupable
             FROM v_affaire_stats
             WHERE id = :id_affaire"
        );

        $stmtStats->execute([
            ':id_affaire' => $idAffaire
        ]);

        $resultats = $stmtStats->fetch();
    }

    //affichage ds infos bdd dans card dans ordre indefini + limite le vote à 1 par user sur chaque affaire
    $stmt = $pdo->prepare(
        "SELECT a.id, a.titre, a.description, a.argument_1, a.argument_2
         FROM affaire a
         WHERE NOT EXISTS (
             SELECT 1
             FROM vote v
             WHERE v.id_affaire = a.id
               AND v.id_utilisateur = :id_utilisateur
         )
         ORDER BY RAND()
         LIMIT 1"
    );

    $stmt->execute([
        ':id_utilisateur' => $idUtilisateur
    ]);

    $affaire = $stmt->fetch();
}
?>




<div class=" py-2 lg:py-12">
    <p class="text-5xl mb-4 text-center" aria-hidden="true">&#9878;</p>
    <!--si l'user est connecté, il a accès à la carte de l'affaire à juger, sinon il est invité à se connecter pour plaider sa cause-->
    <?php if (estConnecte()): ?>
        
        <?php if (isset($messageVote)): ?>
            <p class="text-center text-green-600 mb-4">
                <?= htmlspecialchars($messageVote, ENT_QUOTES, 'UTF-8') ?>
            </p>
        <?php endif; ?>
        <!-- si il y a des affaires, on les affiche, sinon message -->
        <?php if ($affaire): ?>
            <div class="rounded-lg shadow-md bg-white p-6">
                <h2 class="text-xl font-bold mb-2">
                    <?= e($affaire['titre']) ?>
                </h2>
                <p class="text-gray-600">
                    <?= (e($affaire['description'])) ?>
                </p>
                <div class="w-full  ">
                    <details class="group">
                        <summary class="flex cursor-pointer  items-center justify-between py-4 font-semibold">
                            <span>
                                Voir les arguments
                            </span>
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
                
                

                <form method="POST" class="flex justify-center gap-2 sm:gap-10 mt-6">
                    <input
                        type="hidden"
                        name="id_affaire"
                        value="<?= (int) $affaire['id'] ?>"
                    >
                    <button type="submit" name="choix" value="acquitte" class="text-white rounded-lg shadow-md bg-green-400 px-8 sm:px-12 py-4 hover:bg-acquitte font-medium text-sm text-center">
                        Acquitté
                    </button>
                    <button type="submit" name="choix" value="coupable" class="text-white rounded-lg shadow-md bg-red-400 px-8 sm:px-12 py-4 hover:bg-coupable font-medium text-sm text-center">
                        Coupable
                    </button>
                </form>
            </div>

            <!-- CARD DES RESULTATS -->
            <?php if ($resultats): ?>

                <div class="rounded-lg shadow-md bg-white p-6 mt-6">

                    <h2 class="text-xl font-bold mb-4 text-center">
                        Résultats des votes
                    </h2>

                    <p class="text-center text-gray-600 mb-4">
                        <?= (int) $resultats['total_votes'] ?>
                        vote<?= (int) $resultats['total_votes'] > 1 ? 's' : '' ?>
                    </p>

                    <div class="space-y-3">

                        <!-- Acquitté -->
                        <p class="text-green-600 text-center font-medium">
                            <?= (int) $resultats['votes_acquitte'] ?>
                            vote<?= (int) $resultats['votes_acquitte'] > 1 ? 's' : '' ?>
                            acquitté<?= (int) $resultats['votes_acquitte'] > 1 ? 's' : '' ?>
                            (<?= (int) $resultats['pct_acquitte'] ?>%)
                        </p>

                        <!-- Coupable -->
                        <p class="text-red-600 text-center font-medium">
                            <?= (int) $resultats['votes_coupable'] ?>
                            vote<?= (int) $resultats['votes_coupable'] > 1 ? 's' : '' ?>
                            coupable<?= (int) $resultats['votes_coupable'] > 1 ? 's' : '' ?>
                            (<?= (int) $resultats['pct_coupable'] ?>%)
                        </p>

                    </div>

                </div>

            <?php endif; ?>

        <?php else: ?>

            <p class="text-center">Aucune affaire disponible.</p>

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
