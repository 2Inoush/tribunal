<?php
/**
 * Formulaire de connexion (F1).
 */
?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">

    <h1 class="font-titre text-2xl font-bold mb-1">Entrer au tribunal</h1>
    <p class="text-sm text-gray-600 mb-6">
        Connectez-vous pour voter et defendre vos causes.
    </p>

    <form method="post" action="<?= url('/connexion') ?>" class="space-y-4" novalidate>

        <?= App\Core\Csrf::champ() ?>

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" id="email" name="email"
                   value="<?= ancien($saisie, 'email') ?>"
                   class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                          <?= isset($erreurs['email']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['email'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['email']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe</label>
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                   class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                          <?= isset($erreurs['mot_de_passe']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['mot_de_passe'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['mot_de_passe']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full rounded bg-encre py-2.5 font-semibold text-white hover:bg-gray-800 transition">
            Se connecter
        </button>

    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Pas encore de compte ?
        <a href="<?= url('/inscription') ?>" class="font-medium text-encre underline hover:text-dore">
            S'inscrire
        </a>
    </p>

</div>
