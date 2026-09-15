<?php
/**
 * En-tete du site : logo a gauche, liens de compte a droite.
 * Les liens changent selon que l'utilisateur est connecte ou non.
 */
?>
<header class="bg-encre text-white border-b-4 border-dore">
    <div class="max-w-4xl mx-auto px-4 py-4 flex items-center justify-between gap-4">

        <a href="<?= url('/') ?>" class="flex items-center gap-2 hover:opacity-80 transition">
            <span class="text-2xl" aria-hidden="true">&#9878;</span>
            <span class="font-titre text-lg sm:text-xl font-bold leading-tight">
                Le Tribunal
                <span class="hidden sm:inline text-dore">des Disputes Minuscules</span>
            </span>
        </a>

        <nav class="flex items-center gap-3 text-sm">
            <?php if (estConnecte()): ?>

                <span class="hidden sm:inline text-gray-300">
                    <?= e(utilisateurConnecte()['pseudo']) ?>
                </span>
                <a href="<?= url('/deconnexion') ?>"
                   class="px-3 py-1.5 rounded border border-gray-500 hover:bg-white hover:text-encre transition">
                    Deconnexion
                </a>

            <?php else: ?>

                <a href="<?= url('/connexion') ?>" class="hover:text-dore transition">
                    Connexion
                </a>
                <a href="<?= url('/inscription') ?>"
                   class="px-3 py-1.5 rounded bg-dore text-encre font-semibold hover:bg-yellow-500 transition">
                    Inscription
                </a>

            <?php endif; ?>
        </nav>

    </div>
</header>
