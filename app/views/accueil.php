<?php /** VUE : contenu de la page d'accueil. */ ?>

<div class="text-center py-12">

    <p class="text-5xl mb-4">&#9878;</p>

    <h1 class="text-3xl font-bold mb-3" style="font-family: Georgia, serif;">
        La séance est ouverte
    </h1>

    <?php if (estConnecte()): ?>
        <p class="text-gray-600">
            Bonjour <span class="font-semibold"><?= e(utilisateurConnecte()['pseudo']) ?></span>,
            les affaires à juger arriveront bientôt ici.
        </p>
    <?php else: ?>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

</div>
