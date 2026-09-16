<?php /** VUE : contenu de la page d'accueil. */ ?>

<div class="text-center py-12">

    <p class="text-5xl mb-4">&#9878;</p>

    <a href=""><button type="button"
            class="text-white bg-gradient-to-r from-blue-500 via-blue-600 to-blue-700 hover:bg-gradient-to-br focus:ring-4 focus:outline-none focus:ring-blue-300 dark:focus:ring-blue-800 font-medium rounded-base text-sm px-4 py-2.5 text-center leading-5">Blue</button>
    </a>

    <h1 class="text-3xl font-bold mb-3" style="font-family: Georgia, serif;">
        La séance est ouverte
    </h1>

    <?php if (estConnecte()): ?>
        <p class="text-gray-600 mb-6">
            Bonjour <span class="font-semibold"><?= e(utilisateurConnecte()['pseudo']) ?></span>,
            les affaires à juger arriveront bientôt ici.
        </p>
        <a href="affaire-creer.php"
           class="inline-block rounded bg-dore px-5 py-2.5 font-semibold text-encre hover:bg-yellow-500">
            Créer une affaire
        </a>
    <?php else: ?>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

</div>