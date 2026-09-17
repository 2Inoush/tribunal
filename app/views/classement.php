<?php
/**
 * VUE : classement des jures (F9)
 *
 * Le controleur a prepare :
 *   $resultats   les jures a afficher (top 10, ou resultats de recherche)
 *   $recherche   le texte cherche ('' si aucune recherche)
 *   $maPosition  la ligne du jure connecte s'il n'est pas visible, sinon null
 *
 * Petite fonction d'affichage utilisee plus bas, pour ne pas repeter
 * trois fois le meme test dans la page.
 */

/** Les couleurs du podium : or, argent, bronze, puis neutre. */
function couleurRang(int $rang): string
{
    return match ($rang) {
        1       => 'bg-dore text-encre border-dore',
        2       => 'bg-gray-300 text-encre border-gray-300',
        3       => 'bg-amber-700 text-white border-amber-700',
        default => 'bg-white text-gray-400 border-gray-200',
    };
}

$monId = estConnecte() ? (int) utilisateurConnecte()['id'] : 0;
?>

<!-- ===================== En-tete ===================== -->

<div class="text-center mb-8">

    <p class="text-4xl mb-2">&#127942;</p>

    <h1 class="text-3xl font-bold mb-2" style="font-family: Georgia, serif;">
        Le banc des jurés
    </h1>

    <p class="text-sm text-gray-600 max-w-md mx-auto">
        Les jurés les plus assidus du tribunal, classés par nombre de verdicts rendus.
    </p>

</div>


<!-- ===================== Barre de recherche ===================== -->

<form method="get" action="" class="mb-8 max-w-md mx-auto">
    <div class="flex gap-2">

        <input type="search" name="recherche" value="<?= e($recherche) ?>"
               placeholder="Chercher un juré par son pseudo…"
               class="flex-1 rounded-lg border border-gray-300 px-4 py-2.5 text-sm
                      focus:outline-none focus:ring-2 focus:ring-dore focus:border-dore">

        <button type="submit"
                class="rounded-lg bg-encre px-5 py-2.5 text-sm font-semibold text-white hover:bg-gray-800">
            Chercher
        </button>

    </div>

    <?php if ($recherche !== ''): ?>
        <p class="mt-2 text-center text-xs text-gray-500">
            <?= count($resultats) ?> résultat<?= count($resultats) > 1 ? 's' : '' ?>
            pour « <?= e($recherche) ?> » —
            <a href="classement.php" class="underline hover:text-dore">voir le top 10</a>
        </p>
    <?php endif; ?>
</form>


<?php if (count($resultats) === 0): ?>

    <!-- ---------- Aucun resultat ---------- -->
    <div class="rounded-lg border border-dashed border-gray-300 bg-white py-12 text-center">
        <p class="text-4xl mb-3">&#128269;</p>
        <p class="text-sm text-gray-600">
            Aucun juré ne correspond à « <?= e($recherche) ?> ».
        </p>
    </div>

<?php else: ?>

    <!-- ===================== Le classement ===================== -->

    <ol class="space-y-2">
        <?php foreach ($resultats as $jure): ?>
            <?php
            $rang    = (int) $jure['rang'];
            $estMoi  = (int) $jure['id'] === $monId;
            // Les trois premiers ont droit a une carte un peu plus haute.
            $podium  = $rang <= 3 && $recherche === '';
            ?>

            <li class="flex items-center gap-4 rounded-lg border bg-white px-4
                       <?= $podium ? 'py-5 shadow-sm' : 'py-3' ?>
                       <?= $estMoi ? 'border-dore ring-2 ring-dore/30' : 'border-gray-200' ?>">

                <!-- Rang -->
                <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                             border-2 font-bold <?= couleurRang($rang) ?>
                             <?= $podium ? 'text-lg' : 'text-sm' ?>">
                    <?= $rang ?>
                </span>

                <!-- Pseudo -->
                <div class="min-w-0 flex-1">
                    <p class="truncate <?= $podium ? 'text-lg font-bold' : 'font-semibold' ?>">
                        <?= e($jure['pseudo']) ?>
                        <?php if ($estMoi): ?>
                            <span class="ml-1 rounded bg-dore px-1.5 py-0.5 text-xs font-semibold text-encre">
                                vous
                            </span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-gray-500">
                        <?= (int) $jure['nb_affaires'] ?> affaire<?= $jure['nb_affaires'] > 1 ? 's' : '' ?> déposée<?= $jure['nb_affaires'] > 1 ? 's' : '' ?>
                    </p>
                </div>

                <!-- Nombre de votes -->
                <div class="shrink-0 text-right">
                    <p class="<?= $podium ? 'text-2xl' : 'text-xl' ?> font-bold leading-none">
                        <?= (int) $jure['nb_votes'] ?>
                    </p>
                    <p class="text-xs text-gray-500">
                        verdict<?= $jure['nb_votes'] > 1 ? 's' : '' ?>
                    </p>
                </div>

            </li>

        <?php endforeach; ?>
    </ol>

<?php endif; ?>


<!-- ===================== Position du juré connecté ===================== -->
<!-- Affiche seulement s'il n'apparait pas deja dans la liste ci-dessus. -->

<?php if ($maPosition !== null): ?>

    <div class="mt-6 rounded-lg border-2 border-dashed border-dore bg-yellow-50 px-4 py-4">

        <p class="mb-3 text-center text-xs font-semibold uppercase tracking-wide text-gray-500">
            Votre position
        </p>

        <div class="flex items-center gap-4">

            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full
                         border-2 border-dore bg-white text-sm font-bold">
                <?= (int) $maPosition['rang'] ?>
            </span>

            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold"><?= e($maPosition['pseudo']) ?></p>
                <p class="text-xs text-gray-500">
                    <?= (int) $maPosition['nb_affaires'] ?> affaire<?= $maPosition['nb_affaires'] > 1 ? 's' : '' ?> déposée<?= $maPosition['nb_affaires'] > 1 ? 's' : '' ?>
                </p>
            </div>

            <div class="shrink-0 text-right">
                <p class="text-xl font-bold leading-none"><?= (int) $maPosition['nb_votes'] ?></p>
                <p class="text-xs text-gray-500">verdict<?= $maPosition['nb_votes'] > 1 ? 's' : '' ?></p>
            </div>

        </div>

        <p class="mt-3 text-center text-xs text-gray-600">
            Rendez quelques verdicts pour grimper au classement.
        </p>

    </div>

<?php endif; ?>
