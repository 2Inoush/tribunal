<?php
/**
 * Page d'accueil.
 *
 * Volontairement vide pour le moment : c'est ici que viendra la pile
 * d'affaires a juger (F5 et F6).
 */
?>
<div class="text-center py-12">

    <p class="text-5xl mb-4" aria-hidden="true">&#9878;</p>

    <h1 class="font-titre text-3xl font-bold mb-3">
        La seance est ouverte
    </h1>


    <?php if (estConnecte()): ?>
        <p class="text-gray-600">
            Bonjour <span class="font-semibold"><?= e(utilisateurConnecte()['pseudo']) ?></span>,
            les affaires a juger arriveront bientot ici.
        </p>
    <?php else: ?>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

    <a href="<?= url('/affaires/creer') ?>">Créer</a>

</div>
