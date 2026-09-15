<?php
/**
 * Formulaire d'inscription (F1).
 *
 * $erreurs : ['nom_du_champ' => 'message'] apres une tentative ratee
 * $saisie  : ce que l'utilisateur avait deja tape (sauf le mot de passe)
 */
?>
<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">

    <h1 class="font-titre text-2xl font-bold mb-1">Preter serment</h1>
    <p class="text-sm text-gray-600 mb-6">
        Creez votre compte pour plaider et rendre la justice.
    </p>

    <form method="post" action="<?= url('/inscription') ?>" class="space-y-4" novalidate>

        <?= App\Core\Csrf::champ() ?>

        <div>
            <label for="pseudo" class="block text-sm font-medium mb-1">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo"
                   value="<?= ancien($saisie, 'pseudo') ?>"
                   class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                          <?= isset($erreurs['pseudo']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['pseudo'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['pseudo']) ?></p>
            <?php endif; ?>
        </div>

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
            <?php else: ?>
                <p class="mt-1 text-xs text-gray-500">8 caracteres minimum.</p>
            <?php endif; ?>
        </div>

        <div>
            <label for="mot_de_passe_confirmation" class="block text-sm font-medium mb-1">
                Confirmation du mot de passe
            </label>
            <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation"
                   class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                          <?= isset($erreurs['mot_de_passe_confirmation']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['mot_de_passe_confirmation'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['mot_de_passe_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full rounded bg-dore py-2.5 font-semibold text-encre hover:bg-yellow-500 transition">
            Creer mon compte
        </button>

    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Deja inscrit ?
        <a href="<?= url('/connexion') ?>" class="font-medium text-encre underline hover:text-dore">
            Se connecter
        </a>
    </p>

</div>
