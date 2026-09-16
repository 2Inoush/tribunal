<?php
/**
 * Formulaire : titre, faits, arguments de defense (F2).
 */

$saisie ??= [];
$erreurs ??= [];
?>

<div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">
    <h1 class="font-titre text-2xl font-bold mb-1">Créer une affaire</h1>
    <p class="text-sm text-gray-600 mb-6">
        Exposez les faits et présentez vos arguments au tribunal.
    </p>

    <form method="post" action="<?= url('/affaires') ?>" class="space-y-4" novalidate>
        <?= App\Core\Csrf::champ() ?>

        <div>
            <label for="titre" class="block text-sm font-medium mb-1">Titre</label>
            <input type="text" id="titre" name="titre"
                   value="<?= ancien($saisie, 'titre') ?>"
                   class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                          <?= isset($erreurs['titre']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['titre'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['titre']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-1">Les faits</label>
            <textarea id="description" name="description" rows="5"
                      class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                             <?= isset($erreurs['description']) ? 'border-coupable' : 'border-gray-300' ?>"><?= ancien($saisie, 'description') ?></textarea>
            <?php if (isset($erreurs['description'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['description']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="argument_1" class="block text-sm font-medium mb-1">Argument de défense 1</label>
            <textarea id="argument_1" name="argument_1" rows="3"
                      class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                             <?= isset($erreurs['argument_1']) ? 'border-coupable' : 'border-gray-300' ?>"><?= ancien($saisie, 'argument_1') ?></textarea>
            <?php if (isset($erreurs['argument_1'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['argument_1']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="argument_2" class="block text-sm font-medium mb-1">
                Argument de défense 2 <span class="font-normal text-gray-500">(facultatif)</span>
            </label>
            <textarea id="argument_2" name="argument_2" rows="3"
                      class="w-full rounded border px-3 py-2 focus:outline-none focus:ring-2 focus:ring-dore
                             <?= isset($erreurs['argument_2']) ? 'border-coupable' : 'border-gray-300' ?>"><?= ancien($saisie, 'argument_2') ?></textarea>
            <?php if (isset($erreurs['argument_2'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['argument_2']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full rounded bg-dore py-2.5 font-semibold text-encre hover:bg-yellow-500 transition">
            Créer l'affaire
        </button>
    </form>
</div>