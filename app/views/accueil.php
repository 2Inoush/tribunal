<?php
/**
 * VUE : page d'accueil.
 *
 * Quatre etats, dans cet ordre :
 *   1. visiteur non connecte  -> invitation a prendre place
 *   2. $resultats rempli      -> la jauge du verdict (F6)
 *   3. $affaire rempli        -> la piece a juger     (F5)
 *   4. rien a juger           -> message de fin
 *
 * Le controleur a prepare $affaire, $resultats et $monChoix.
 * Il n'y a ni connexion ni requete SQL ici.
 */
?>

<?php if (!estConnecte()): ?>

    <!-- ============ 1. Le visiteur ============ -->
    <div class="py-10 text-center sm:py-16">

        <p class="etiquette mb-4">Audience publique</p>

        <h1 class="titre mx-auto mb-5 max-w-lg text-4xl leading-[1.1] sm:text-5xl">
            Le peuple tranche les querelles minuscules
        </h1>

        <p class="mx-auto mb-8 max-w-md text-[15px] leading-relaxed texte-doux">
            L'ananas sur la pizza, les poubelles jamais sorties, la dernière part
            de gâteau. Exposez les faits, plaidez votre cause, et laissez le jury
            populaire rendre son verdict.
        </p>

        <div class="flex flex-col items-center justify-center gap-3 sm:flex-row">
            <a href="inscription.php" class="bouton bouton-or w-full sm:w-auto">
                Prêter serment
            </a>
            <a href="connexion.php" class="bouton bouton-contour w-full sm:w-auto">
                J'ai déjà un compte
            </a>
        </div>

    </div>

<?php elseif ($resultats !== null): ?>

    <!-- ============ 2. Le verdict du peuple (F6) ============ -->
    <div>

        <p class="etiquette mb-2 text-center">Verdict du peuple</p>

        <h1 class="titre mb-7 text-center text-2xl sm:text-3xl">
            <?= e($resultats['titre']) ?>
        </h1>

        <div class="carte p-6 sm:p-8">

            <?php
            /* La jauge.
               Les pourcentages viennent de la vue v_affaire_stats : MySQL
               les a deja calcules, on ne fait que les afficher.

               La largeur est ecrite en style="width: X%" et non en classe
               Tailwind : une classe est un texte fige, elle ne peut pas
               contenir une valeur calculee pendant l'execution. */
            $pctAcquitte = (int) $resultats['pct_acquitte'];
            $pctCoupable = (int) $resultats['pct_coupable'];
            ?>

            <div class="mb-3 flex items-baseline justify-between">
                <div>
                    <p class="titre text-3xl text-acquitte"><?= $pctAcquitte ?>%</p>
                    <p class="etiquette mt-0.5">Acquitté</p>
                </div>
                <div class="text-right">
                    <p class="titre text-3xl text-coupable"><?= $pctCoupable ?>%</p>
                    <p class="etiquette mt-0.5">Coupable</p>
                </div>
            </div>

            <!-- La barre : deux blocs cote a cote dans une gouttiere -->
            <div class="flex h-3 w-full overflow-hidden rounded-full bg-papier">
                <div class="bg-acquitte transition-all duration-700"
                     style="width: <?= $pctAcquitte ?>%"></div>
                <div class="bg-coupable transition-all duration-700"
                     style="width: <?= $pctCoupable ?>%"></div>
            </div>

            <div class="mt-3 flex items-center justify-between text-xs text-encre-pale">
                <span><?= (int) $resultats['votes_acquitte'] ?> voix</span>
                <span class="font-medium text-encre-douce">
                    <?= (int) $resultats['total_votes'] ?>
                    verdict<?= (int) $resultats['total_votes'] > 1 ? 's' : '' ?> rendu<?= (int) $resultats['total_votes'] > 1 ? 's' : '' ?>
                </span>
                <span><?= (int) $resultats['votes_coupable'] ?> voix</span>
            </div>

            <?php if ($monChoix !== null): ?>
                <hr class="filet my-5">
                <p class="text-center text-sm texte-doux">
                    Votre verdict :
                    <?php if ($monChoix === 'acquitte'): ?>
                        <span class="font-semibold text-acquitte">Acquitté</span>
                    <?php else: ?>
                        <span class="font-semibold text-coupable">Coupable</span>
                    <?php endif; ?>
                </p>
            <?php endif; ?>

        </div>

        <a href="index.php" class="bouton bouton-encre mt-5 w-full">
            Affaire suivante →
        </a>

    </div>

<?php elseif ($affaire !== null): ?>

    <!-- ============ 3. La piece a juger (F5) ============ -->
    <div>

        <p class="etiquette mb-3 text-center">
            Affaire n° <?= (int) $affaire['id'] ?>
        </p>

        <div class="carte overflow-hidden">

            <div class="p-6 sm:p-8">

                <h1 class="titre mb-4 text-2xl leading-tight sm:text-[28px]">
                    <?= e($affaire['titre']) ?>
                </h1>

                <p class="text-[15px] leading-relaxed texte-doux">
                    <?= nl2br(e($affaire['description'])) ?>
                </p>

                <hr class="filet my-5">

                <details class="group">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3">
                        <span class="etiquette">Arguments de la défense</span>
                        <span class="text-encre-pale transition-transform group-open:rotate-180"
                              aria-hidden="true">↓</span>
                    </summary>

                    <div class="mt-4 space-y-3">
                        <p class="rounded-lg bg-papier px-4 py-3 text-sm leading-relaxed texte-doux">
                            <?= nl2br(e($affaire['argument_1'])) ?>
                        </p>
                        <?php if ($affaire['argument_2']): ?>
                            <p class="rounded-lg bg-papier px-4 py-3 text-sm leading-relaxed texte-doux">
                                <?= nl2br(e($affaire['argument_2'])) ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </details>

            </div>

            <?php
            /* Les deux boutons sont dans le meme formulaire : ils portent
               le meme name="choix" mais une value differente, donc c'est
               le bouton clique qui decide du vote envoye. */
            ?>
            <form method="post" action="index.php"
                  class="flex gap-3 border-t border-trait bg-papier p-4 sm:p-5">

                <input type="hidden" name="id_affaire" value="<?= (int) $affaire['id'] ?>">

                <button type="submit" name="choix" value="acquitte"
                        class="bouton-verdict verdict-acquitte">
                    Acquitté
                </button>

                <button type="submit" name="choix" value="coupable"
                        class="bouton-verdict verdict-coupable">
                    Coupable
                </button>

            </form>

        </div>

    </div>

<?php else: ?>

    <!-- ============ 4. Plus rien a juger ============ -->
    <div class="carte px-6 py-14 text-center">
        <p class="etiquette mb-3">Séance levée</p>
        <h1 class="titre mb-2 text-2xl">Vous avez jugé toutes les affaires</h1>
        <p class="mx-auto mb-7 max-w-sm text-sm texte-doux">
            Le tribunal n'a plus rien à vous soumettre. À vous de lui donner
            du travail.
        </p>
        <a href="affaire-creer.php" class="bouton bouton-or">
            Déposer une affaire
        </a>
    </div>

<?php endif; ?>
