<?php
/**
 * VUE : formulaire de modification d'une affaire (F3)
 *
 * ---------------------------------------------------------------------
 *  Cette vue est prete. Le controleur reste a ecrire.
 *
 *  Variables attendues du controleur (app/controllers/affaire-modifier.php) :
 *
 *    $affaire       l'affaire chargee, pour recuperer son id
 *    $erreurs       ['titre' => 'message', ...]   (vide s'il n'y en a pas)
 *    $titreAffaire  \
 *    $description    | le contenu des champs :
 *    $argument1      | - au premier affichage : le contenu actuel de l'affaire
 *    $argument2     /  - apres une erreur : ce que l'utilisateur venait de taper
 *
 *  Tant que ces variables ne sont pas definies, la page s'affiche quand meme
 *  avec des champs vides : c'est le role des ?? '' ci-dessous. Ils pourront
 *  etre retires une fois le controleur termine.
 * ---------------------------------------------------------------------
 */

$erreurs      = $erreurs      ?? [];
$titreAffaire = $titreAffaire ?? '';
$description  = $description  ?? '';
$argument1    = $argument1    ?? '';
$argument2    = $argument2    ?? '';
?>

<div class="max-w-2xl mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">

    <h1 class="text-2xl font-bold mb-1" style="font-family: Georgia, serif;">Modifier une affaire</h1>
    <p class="text-sm text-gray-600 mb-6">
        Vous pouvez encore modifier cette affaire : elle n'a reçu aucun vote.
    </p>

    <?php
    /* action="" renvoie le formulaire sur la meme adresse,
       donc sur affaire-modifier.php?id=... : l'identifiant est conserve. */
    ?>
    <form method="post" action="" class="space-y-4" novalidate>

        <div>
            <label for="titre" class="block text-sm font-medium mb-1">Titre</label>
            <input type="text" id="titre" name="titre" value="<?= e($titreAffaire) ?>"
                   class="w-full rounded border px-3 py-2
                          <?= isset($erreurs['titre']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['titre'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['titre']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="description" class="block text-sm font-medium mb-1">Les faits</label>
            <textarea id="description" name="description" rows="5"
                      class="w-full rounded border px-3 py-2
                             <?= isset($erreurs['description']) ? 'border-coupable' : 'border-gray-300' ?>"><?= e($description) ?></textarea>
            <?php if (isset($erreurs['description'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['description']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="argument_1" class="block text-sm font-medium mb-1">Argument de défense 1</label>
            <textarea id="argument_1" name="argument_1" rows="3"
                      class="w-full rounded border px-3 py-2
                             <?= isset($erreurs['argument_1']) ? 'border-coupable' : 'border-gray-300' ?>"><?= e($argument1) ?></textarea>
            <?php if (isset($erreurs['argument_1'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['argument_1']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="argument_2" class="block text-sm font-medium mb-1">
                Argument de défense 2
                <span class="font-normal text-gray-500">(facultatif)</span>
            </label>
            <textarea id="argument_2" name="argument_2" rows="3"
                      class="w-full rounded border px-3 py-2
                             <?= isset($erreurs['argument_2']) ? 'border-coupable' : 'border-gray-300' ?>"><?= e($argument2) ?></textarea>
            <?php if (isset($erreurs['argument_2'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['argument_2']) ?></p>
            <?php endif; ?>
        </div>

        <div class="flex gap-3">
            <a href="profil.php"
               class="flex-1 text-center rounded border border-gray-300 py-2.5 font-semibold hover:bg-gray-50">
                Annuler
            </a>
            <button type="submit"
                    class="flex-1 rounded bg-dore py-2.5 font-semibold text-encre hover:bg-yellow-500">
                Enregistrer les modifications
            </button>
        </div>

    </form>

</div>
