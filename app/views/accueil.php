<?php
/**
 * VUE : page d'accueil.
 *
 * Trois etats possibles, dans cet ordre :
 *   1. visiteur non connecte        -> invitation a se connecter
 *   2. $resultats rempli            -> la jauge du verdict (F6)
 *   3. $affaire rempli              -> la carte a juger     (F5)
 *   4. rien a juger                 -> message de fin
 *
 * Mise en page de la carte et des boutons de vote : Enora.
 *
 * Le controleur (app/controllers/accueil.php) a prepare $affaire,
 * $resultats et $monChoix. Il n'y a ni connexion ni requete ici.
 */
?>

<?php if (!estConnecte()): ?>

    <!-- ============ 1. Visiteur non connecte ============ -->
    <div class="py-12 text-center">
        <p class="mb-4 text-5xl" aria-hidden="true">&#9878;</p>
        <h1 class="mb-3 text-3xl font-bold" style="font-family: Georgia, serif;">
            La séance est ouverte
        </h1>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    </div>

<?php elseif ($resultats !== null): ?>

    <!-- ============ 2. Le verdict du peuple (F6) ============ -->
    <div class="mx-auto max-w-xl">

        <p class="mb-1 text-center text-xs font-semibold uppercase tracking-widest text-gray-400">
            Verdict du peuple
        </p>

        <h2 class="mb-6 text-center text-xl font-bold">
            <?= e($resultats['titre']) ?>
        </h2>

        <div class="rounded-lg border border-gray-200 bg-white p-6 shadow-sm">

            <?php
            /* La jauge.
               Les deux pourcentages viennent de la vue v_affaire_stats :
               MySQL les a deja calcules, on ne fait que les afficher.

               La largeur est ecrite en style="width: X%" et non en classe
               Tailwind : une classe est un texte fige, elle ne peut pas
               contenir une valeur calculee pendant l'execution. */
            $pctAcquitte = (int) $resultats['pct_acquitte'];
            $pctCoupable = (int) $resultats['pct_coupable'];
            ?>

            <!-- Les chiffres, de part et d'autre -->
            <div class="mb-2 flex items-end justify-between text-sm">
                <span class="font-semibold text-acquitte">
                    Acquitté <?= $pctAcquitte ?>%
                </span>
                <span class="font-semibold text-coupable">
                    <?= $pctCoupable ?>% Coupable
                </span>
            </div>

            <!-- La barre : deux blocs cote a cote dans une gouttiere -->
            <div class="flex h-5 w-full overflow-hidden rounded-full bg-gray-100">
                <div class="bg-acquitte transition-all duration-700"
                     style="width: <?= $pctAcquitte ?>%"></div>
                <div class="bg-coupable transition-all duration-700"
                     style="width: <?= $pctCoupable ?>%"></div>
            </div>

            <!-- Le detail des voix -->
            <div class="mt-2 flex items-center justify-between text-xs text-gray-500">
                <span>
                    <?= (int) $resultats['votes_acquitte'] ?>
                    voix
                </span>
                <span class="font-medium text-gray-600">
                    <?= (int) $resultats['total_votes'] ?>
                    verdict<?= (int) $resultats['total_votes'] > 1 ? 's' : '' ?> rendu<?= (int) $resultats['total_votes'] > 1 ? 's' : '' ?>
                </span>
                <span>
                    <?= (int) $resultats['votes_coupable'] ?>
                    voix
                </span>
            </div>

            <?php if ($monChoix !== null): ?>
                <p class="mt-5 border-t border-gray-100 pt-4 text-center text-sm text-gray-600">
                    Votre verdict :
                    <?php if ($monChoix === 'acquitte'): ?>
                        <span class="font-semibold text-acquitte">Acquitté</span>
                    <?php else: ?>
                        <span class="font-semibold text-coupable">Coupable</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

        </div>

        <!-- Le cahier des charges ne prevoit pas de cloture du vote :
             on invite simplement a passer a l'affaire suivante. -->
        <a href="index.php"
           class="mt-5 block rounded-lg bg-encre py-3 text-center text-sm font-semibold
                  text-white hover:bg-gray-800">
            Affaire suivante →
        </a>

    </div>

<?php elseif ($affaire !== null): ?>

    <!-- ============ 3. La carte a juger (F5) ============ -->
    <div class="py-2 lg:py-8">

        <p class="mb-4 text-center text-5xl" aria-hidden="true">&#9878;</p>

        <div class="rounded-lg bg-white p-6 shadow-md">

            <h2 class="mb-2 text-xl font-bold"><?= e($affaire['titre']) ?></h2>

            <p class="text-gray-600">
                <?= nl2br(e($affaire['description'])) ?>
            </p>

            <details class="group w-full">
                <summary class="flex cursor-pointer items-center justify-between py-4 font-semibold">
                    <span>Voir les arguments</span>
                    <span class="transition-transform group-open:rotate-180">↓</span>
                </summary>

                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= nl2br(e($affaire['argument_1'])) ?>
                </div>

                <?php if ($affaire['argument_2']): ?>
                    <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                        <?= nl2br(e($affaire['argument_2'])) ?>
                    </div>
                <?php endif; ?>
            </details>

            <?php
            /* Les deux boutons sont dans le meme formulaire : ils portent
               le meme name="choix" mais une value differente, donc c'est
               le bouton clique qui decide du vote envoye. */
            ?>
            <form method="post" action="index.php" class="flex flex-wrap justify-center gap-3">

                <input type="hidden" name="id_affaire" value="<?= (int) $affaire['id'] ?>">

                <button type="submit" name="choix" value="acquitte"
                        class="rounded-lg bg-green-400 px-8 py-4 text-sm font-medium leading-5
                               text-white shadow-md hover:bg-acquitte">
                    Acquitté
                </button>

                <button type="submit" name="choix" value="coupable"
                        class="rounded-lg bg-red-400 px-8 py-4 text-sm font-medium leading-5
                               text-white shadow-md hover:bg-coupable">
                    Coupable
                </button>

            </form>

        </div>

    </div>

<?php else: ?>

    <!-- ============ 4. Plus rien a juger ============ -->
    <div class="py-12 text-center">
        <p class="mb-4 text-5xl" aria-hidden="true">&#9878;</p>
        <h2 class="mb-2 text-xl font-bold">Vous avez jugé toutes les affaires</h2>
        <p class="mb-6 text-sm text-gray-600">
            Le tribunal n'a plus rien à vous soumettre pour le moment.
        </p>
        <a href="affaire-creer.php"
           class="inline-block rounded-lg bg-dore px-5 py-2.5 text-sm font-semibold text-encre
                  hover:bg-yellow-500">
            Déposer une affaire
        </a>
    </div>

<?php endif; ?>
