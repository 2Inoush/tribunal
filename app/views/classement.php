<?php
/**
 * VUE : classement des jures (F9)
 *
 * Le controleur a prepare :
 *   $resultats   les jures a afficher (top 10, ou resultats de recherche)
 *   $recherche   le texte cherche ('' si aucune recherche)
 *   $maPosition  la ligne du jure connecte s'il n'est pas visible, sinon null
 */

/** La couleur du rang : or, argent, bronze, puis neutre. */
function styleRang(int $rang): string
{
    return match ($rang) {
        1       => 'bg-or text-white border-or',
        2       => 'bg-[#c5c9ce] text-white border-[#c5c9ce]',
        3       => 'bg-[#b07a4a] text-white border-[#b07a4a]',
        default => 'bg-papier text-encre-pale border-trait',
    };
}

$monId = estConnecte() ? (int) utilisateurConnecte()['id'] : 0;
?>

<div class="mb-7 text-center">
    <p class="etiquette mb-2">Registre du tribunal</p>
    <h1 class="titre text-3xl sm:text-4xl">Le banc des jurés</h1>
    <p class="mx-auto mt-2 max-w-sm text-sm texte-doux">
        Les jurés les plus assidus, classés par nombre de verdicts rendus.
    </p>
</div>


<!-- ===================== Recherche ===================== -->

<form method="get" action="" class="mx-auto mb-6 max-w-md">
    <div class="flex gap-2">
        <input type="search" name="recherche" value="<?= e($recherche) ?>"
               placeholder="Chercher un juré…" class="champ">
        <button type="submit" class="bouton bouton-encre shrink-0">Chercher</button>
    </div>

    <?php if ($recherche !== ''): ?>
        <p class="mt-2.5 text-center text-xs text-encre-pale">
            <?= count($resultats) ?> résultat<?= count($resultats) > 1 ? 's' : '' ?>
            pour « <?= e($recherche) ?> » —
            <a href="classement.php" class="text-or hover:underline">voir le top 10</a>
        </p>
    <?php endif; ?>
</form>


<?php if (count($resultats) === 0): ?>

    <div class="carte px-6 py-14 text-center">
        <p class="titre mb-1 text-lg">Aucun juré trouvé</p>
        <p class="text-sm texte-doux">
            Personne ne correspond à « <?= e($recherche) ?> ».
        </p>
    </div>

<?php else: ?>

    <!-- ===================== Le classement ===================== -->
    <ol class="carte divide-y divide-[#efeae0] overflow-hidden">
        <?php foreach ($resultats as $jure): ?>
            <?php
            $rang   = (int) $jure['rang'];
            $estMoi = (int) $jure['id'] === $monId;
            ?>

            <li class="flex items-center gap-4 px-4 py-3.5 sm:px-6
                       <?= $estMoi ? 'bg-[#fdf9ee]' : '' ?>">

                <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                             border text-[13px] font-bold <?= styleRang($rang) ?>">
                    <?= $rang ?>
                </span>

                <div class="min-w-0 flex-1">
                    <p class="truncate font-semibold">
                        <?= e($jure['pseudo']) ?>
                        <?php if ($estMoi): ?>
                            <span class="ml-1.5 rounded bg-or px-1.5 py-0.5 text-[10px]
                                         font-bold uppercase tracking-wide text-white">
                                vous
                            </span>
                        <?php endif; ?>
                    </p>
                    <p class="text-xs text-encre-pale">
                        <?= (int) $jure['nb_affaires'] ?>
                        affaire<?= $jure['nb_affaires'] > 1 ? 's' : '' ?> déposée<?= $jure['nb_affaires'] > 1 ? 's' : '' ?>
                    </p>
                </div>

                <div class="shrink-0 text-right">
                    <p class="titre text-xl leading-none"><?= (int) $jure['nb_votes'] ?></p>
                    <p class="mt-1 text-[11px] uppercase tracking-wider text-encre-pale">
                        verdict<?= $jure['nb_votes'] > 1 ? 's' : '' ?>
                    </p>
                </div>

            </li>

        <?php endforeach; ?>
    </ol>

<?php endif; ?>


<!-- ============== Position du juré connecté ==============
     Affichee seulement s'il n'apparait pas dans la liste ci-dessus. -->

<?php if ($maPosition !== null): ?>

    <div class="mt-5 rounded-xl border border-dashed border-or bg-[#fdf9ee] px-4 py-4 sm:px-6">

        <p class="etiquette mb-3 text-center">Votre position</p>

        <div class="flex items-center gap-4">

            <span class="flex h-8 w-8 shrink-0 items-center justify-center rounded-full
                         border border-or bg-white text-[13px] font-bold text-or">
                <?= (int) $maPosition['rang'] ?>
            </span>

            <div class="min-w-0 flex-1">
                <p class="truncate font-semibold"><?= e($maPosition['pseudo']) ?></p>
                <p class="text-xs text-encre-pale">
                    <?= (int) $maPosition['nb_affaires'] ?>
                    affaire<?= $maPosition['nb_affaires'] > 1 ? 's' : '' ?> déposée<?= $maPosition['nb_affaires'] > 1 ? 's' : '' ?>
                </p>
            </div>

            <div class="shrink-0 text-right">
                <p class="titre text-xl leading-none"><?= (int) $maPosition['nb_votes'] ?></p>
                <p class="mt-1 text-[11px] uppercase tracking-wider text-encre-pale">
                    verdict<?= $maPosition['nb_votes'] > 1 ? 's' : '' ?>
                </p>
            </div>

        </div>

        <p class="mt-3 text-center text-xs texte-doux">
            Rendez quelques verdicts pour grimper au classement.
        </p>

    </div>

<?php endif; ?>
