<?php
/**
 * VUE : mon profil (F8)
 *
 * Le controleur a prepare :
 *   $utilisateur -> ['pseudo' => ..., 'email' => ..., 'date_creation' => ...]
 *   $mesConflits -> la liste de ses affaires (tableau, eventuellement vide)
 */
?>

<!-- ===================== Informations du compte ===================== -->

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8 mb-8">

    <h1 class="text-2xl font-bold mb-6" style="font-family: Georgia, serif;">Mon profil</h1>

    <dl class="space-y-3 text-sm">
        <div class="flex justify-between border-b border-gray-100 pb-3">
            <dt class="text-gray-500">Pseudo</dt>
            <dd class="font-semibold"><?= e($utilisateur['pseudo']) ?></dd>
        </div>
        <div class="flex justify-between border-b border-gray-100 pb-3">
            <dt class="text-gray-500">Email</dt>
            <dd><?= e($utilisateur['email']) ?></dd>
        </div>
        <div class="flex justify-between border-b border-gray-100 pb-3">
            <dt class="text-gray-500">Membre depuis</dt>
            <dd><?= date('d/m/Y', strtotime($utilisateur['date_creation'])) ?></dd>
        </div>
        <div class="flex justify-between">
            <dt class="text-gray-500">Conflits déposés</dt>
            <dd class="font-semibold"><?= count($mesConflits) ?></dd>
        </div>
    </dl>

</div>


<!-- ===================== Mes conflits ===================== -->

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">

    <div class="flex items-center justify-between mb-6">
        <h2 class="text-xl font-bold" style="font-family: Georgia, serif;">Mes conflits</h2>
        <a href="affaire-creer.php"
           class="text-sm px-3 py-1.5 rounded bg-dore text-encre font-semibold hover:bg-yellow-500">
            Créer une affaire
        </a>
    </div>

    <?php if (count($mesConflits) === 0): ?>

        <!-- Liste vide : on l'affiche proprement, ce n'est pas une erreur. -->
        <p class="text-sm text-gray-500 py-6 text-center">
            Vous n'avez encore déposé aucun conflit.
        </p>

    <?php else: ?>

        <ul class="divide-y divide-gray-100">
            <?php foreach ($mesConflits as $conflit): ?>

                <li class="py-4">

                    <div class="flex items-start justify-between gap-4">

                        <div class="min-w-0">
                            <p class="font-semibold truncate"><?= e($conflit['titre']) ?></p>
                            <p class="text-xs text-gray-500 mt-0.5">
                                Déposé le <?= date('d/m/Y', strtotime($conflit['date_creation'])) ?>
                                —
                                <?php if ((int) $conflit['total_votes'] === 0): ?>
                                    aucun vote pour l'instant
                                <?php else: ?>
                                    <?= (int) $conflit['total_votes'] ?> vote<?= $conflit['total_votes'] > 1 ? 's' : '' ?>
                                    (<?= (int) $conflit['pct_acquitte'] ?>% acquitté,
                                     <?= (int) $conflit['pct_coupable'] ?>% coupable)
                                <?php endif; ?>
                            </p>
                        </div>

                        <div class="shrink-0 flex items-center gap-2">

                            <?php
                            /* Le cahier des charges (F3) demande que « Modifier »
                               disparaisse des le premier vote. La colonne modifiable
                               vaut 1 tant qu'aucun vote n'a ete depose. */
                            ?>
                            <?php if ((int) $conflit['modifiable'] === 1): ?>
                                <a href="affaire-modifier.php?id=<?= (int) $conflit['id'] ?>"
                                   class="text-xs px-2 py-1 rounded border border-gray-300 hover:bg-gray-50 btn-modifier">
                                    Modifier
                                </a>
                            <?php else: ?>
                                <span class="text-xs px-2 py-1 rounded bg-gray-50 text-gray-400 border border-gray-200"
                                      title="Une affaire deja votee ne peut plus etre modifiee">
                                    verrouillé
                                </span>
                            <?php endif; ?>

                            <?php
                            /* Suppression possible meme apres des votes (F4).
                               Un formulaire POST plutot qu'un lien : une suppression
                               ne doit pas partir sur une simple visite d'URL. */
                            ?>
                            <form method="post" action="affaire-supprimer.php?id=<?= (int) $conflit['id'] ?>"
                                  onsubmit="return confirm('Supprimer définitivement cette affaire ?');">
                                <button type="submit"
                                        class="text-xs px-2 py-1 rounded border border-coupable text-coupable hover:bg-red-50">
                                    Supprimer
                                </button>
                            </form>

                        </div>

                    </div>

                </li>

            <?php endforeach; ?>
        </ul>

    <?php endif; ?>

</div>
