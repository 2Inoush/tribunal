<?php
/**
 * VUE : mon profil (F8)
 *
 * Le controleur a prepare :
 *   $utilisateur -> ['pseudo' => ..., 'email' => ..., 'date_creation' => ...]
 *   $mesConflits -> la liste de ses affaires (tableau, eventuellement vide)
 */
?>

<!-- ===================== En-tête du juré ===================== -->

<div class="mb-7 flex items-center gap-4">

    <span class="flex h-14 w-14 shrink-0 items-center justify-center rounded-full
                 border border-trait bg-blanc text-xl font-bold text-or">
        <?= e(mb_strtoupper(mb_substr($utilisateur['pseudo'], 0, 1))) ?>
    </span>

    <div class="min-w-0">
        <p class="etiquette mb-1">Juré du tribunal</p>
        <h1 class="titre truncate text-3xl"><?= e($utilisateur['pseudo']) ?></h1>
    </div>

</div>


<!-- ===================== Fiche ===================== -->

<div class="carte mb-6 divide-y divide-[#efeae0]">

    <div class="flex items-center justify-between px-5 py-3.5 text-sm">
        <span class="texte-doux">Email</span>
        <span class="font-medium"><?= e($utilisateur['email']) ?></span>
    </div>

    <div class="flex items-center justify-between px-5 py-3.5 text-sm">
        <span class="texte-doux">Membre depuis</span>
        <span class="font-medium">
            <?= date('d/m/Y', strtotime($utilisateur['date_creation'])) ?>
        </span>
    </div>

    <div class="flex items-center justify-between px-5 py-3.5 text-sm">
        <span class="texte-doux">Affaires déposées</span>
        <span class="font-medium"><?= count($mesConflits) ?></span>
    </div>

</div>


<!-- ===================== Ses affaires ===================== -->

<div class="mb-4 flex items-center justify-between">
    <h2 class="titre text-xl">Mes affaires</h2>
    <a href="affaire-creer.php" class="bouton bouton-contour !py-2 text-[13px]">
        Déposer
    </a>
</div>

<?php if (count($mesConflits) === 0): ?>

    <!-- Liste vide : on l'affiche proprement, ce n'est pas une erreur. -->
    <div class="carte px-6 py-12 text-center">
        <p class="titre mb-1 text-lg">Aucune affaire déposée</p>
        <p class="mx-auto mb-6 max-w-xs text-sm texte-doux">
            Un litige à soumettre au peuple ? C'est le moment.
        </p>
        <a href="affaire-creer.php" class="bouton bouton-or">Déposer une affaire</a>
    </div>

<?php else: ?>

    <ul class="space-y-3">
        <?php foreach ($mesConflits as $conflit): ?>

            <li class="carte p-4 sm:p-5">

                <?php
                /* Mobile first : le titre occupe toute la largeur et n'est
                   donc jamais tronque. Les actions viennent EN DESSOUS.
                   A partir de 640 px (sm:), le bloc passe en ligne et les
                   actions remontent a droite du titre. */
                ?>
                <div class="sm:flex sm:items-start sm:justify-between sm:gap-4">

                    <div class="min-w-0 sm:flex-1">
                        <p class="titre text-base leading-snug"><?= e($conflit['titre']) ?></p>
                        <p class="mt-1 text-xs text-encre-pale">
                            Déposée le <?= date('d/m/Y', strtotime($conflit['date_creation'])) ?>
                        </p>
                    </div>

                    <!-- Le taux de vote de l'affaire -->
                    <?php if ((int) $conflit['total_votes'] === 0): ?>
                        <p class="mt-3 text-xs text-encre-pale sm:hidden">
                            Aucun vote pour l'instant
                        </p>
                    <?php else: ?>
                        <div class="mt-3 sm:hidden">
                            <div class="flex h-1.5 w-full overflow-hidden rounded-full bg-papier">
                                <div class="bg-acquitte" style="width: <?= (int) $conflit['pct_acquitte'] ?>%"></div>
                                <div class="bg-coupable" style="width: <?= (int) $conflit['pct_coupable'] ?>%"></div>
                            </div>
                            <p class="mt-1.5 text-xs text-encre-pale">
                                <?= (int) $conflit['total_votes'] ?> verdicts —
                                <span class="text-acquitte"><?= (int) $conflit['pct_acquitte'] ?>% acquitté</span>,
                                <span class="text-coupable"><?= (int) $conflit['pct_coupable'] ?>% coupable</span>
                            </p>
                        </div>
                    <?php endif; ?>

                    <!-- Les actions : pleine largeur sur telephone -->
                    <div class="mt-4 flex items-center gap-2 sm:mt-0 sm:shrink-0">

                        <?php
                        /* Le cahier des charges (F3) demande que « Modifier »
                           disparaisse des le premier vote. La colonne
                           modifiable vaut 1 tant qu'aucun vote n'est depose. */
                        ?>
                        <?php if ((int) $conflit['modifiable'] === 1): ?>
                            <a href="affaire-modifier.php?id=<?= (int) $conflit['id'] ?>"
                               class="bouton bouton-contour flex-1 !py-2 text-xs sm:flex-none sm:!px-3">
                                Modifier
                            </a>
                        <?php else: ?>
                            <span class="flex-1 rounded-lg bg-papier px-2.5 py-2 text-center
                                         text-[11px] uppercase tracking-wide text-encre-pale
                                         sm:flex-none">
                                verrouillée
                            </span>
                        <?php endif; ?>

                        <?php
                        /* Suppression possible meme apres des votes (F4).
                           Un formulaire POST plutot qu'un lien : une
                           suppression ne doit pas partir sur une visite d'URL. */
                        ?>
                        <form method="post" action="affaire-supprimer.php?id=<?= (int) $conflit['id'] ?>"
                              onsubmit="return confirm('Supprimer définitivement cette affaire ?');"
                              class="flex-1 sm:flex-none">
                            <button type="submit"
                                    class="w-full rounded-lg border border-transparent px-2.5 py-2
                                           text-xs font-semibold text-coupable
                                           hover:border-coupable hover:bg-[#fbeae8]">
                                Supprimer
                            </button>
                        </form>

                    </div>

                </div>

                <!-- Le taux de vote, version grand ecran : sous le tout -->
                <?php if ((int) $conflit['total_votes'] > 0): ?>
                    <div class="mt-3 hidden sm:block">
                        <div class="flex h-1.5 w-full overflow-hidden rounded-full bg-papier">
                            <div class="bg-acquitte" style="width: <?= (int) $conflit['pct_acquitte'] ?>%"></div>
                            <div class="bg-coupable" style="width: <?= (int) $conflit['pct_coupable'] ?>%"></div>
                        </div>
                        <p class="mt-1.5 text-xs text-encre-pale">
                            <?= (int) $conflit['total_votes'] ?> verdicts —
                            <span class="text-acquitte"><?= (int) $conflit['pct_acquitte'] ?>% acquitté</span>,
                            <span class="text-coupable"><?= (int) $conflit['pct_coupable'] ?>% coupable</span>
                        </p>
                    </div>
                <?php else: ?>
                    <p class="mt-3 hidden text-xs text-encre-pale sm:block">
                        Aucun vote pour l'instant
                    </p>
                <?php endif; ?>

            </li>

        <?php endforeach; ?>
    </ul>

<?php endif; ?>

<?php
/* Sur telephone, « Sortir » n'apparait pas dans la barre du haut faute
   de place : la deconnexion se fait donc ici. */
?>
<a href="deconnexion.php" class="bouton bouton-contour mt-7 w-full sm:hidden">
    Se déconnecter
</a>
