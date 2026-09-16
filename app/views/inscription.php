<?php
/**
 * VUE : formulaire d'inscription.
 *
 * La page inscription.php a prepare :
 *   $erreurs -> ['pseudo' => 'message', ...]  (vide s'il n'y a pas d'erreur)
 *   $pseudo, $email -> ce qui a deja ete tape, pour ne pas le retaper
 *
 * isset($erreurs['pseudo']) veut dire : « y a-t-il une erreur sur ce champ ? »
 */
?>

<div class="max-w-md mx-auto bg-white rounded-lg shadow-sm border border-gray-200 p-6 sm:p-8">

    <h1 class="text-2xl font-bold mb-1" style="font-family: Georgia, serif;">Prêter serment</h1>
    <p class="text-sm text-gray-600 mb-6">
        Créez votre compte pour plaider et rendre la justice.
    </p>

    <!-- action="" : le formulaire s'envoie sur cette meme page -->
    <form method="post" action="" class="space-y-4" novalidate>

        <div>
            <label for="pseudo" class="block text-sm font-medium mb-1">Pseudo</label>
            <input type="text" id="pseudo" name="pseudo" value="<?= e($pseudo) ?>"
                   class="w-full rounded border px-3 py-2
                          <?= isset($erreurs['pseudo']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['pseudo'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['pseudo']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium mb-1">Email</label>
            <input type="email" id="email" name="email" value="<?= e($email) ?>"
                   class="w-full rounded border px-3 py-2
                          <?= isset($erreurs['email']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['email'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['email']) ?></p>
            <?php endif; ?>
        </div>

        <div>
            <label for="mot_de_passe" class="block text-sm font-medium mb-1">Mot de passe</label>
            <!-- On ne remet jamais le mot de passe dans le champ : il faut le retaper. -->
            <input type="password" id="mot_de_passe" name="mot_de_passe"
                   class="w-full rounded border px-3 py-2
                          <?= isset($erreurs['mot_de_passe']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['mot_de_passe'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['mot_de_passe']) ?></p>
            <?php else: ?>
                <p class="mt-1 text-xs text-gray-500">8 caractères minimum.</p>
            <?php endif; ?>
        </div>

        <div>
            <label for="mot_de_passe_confirmation" class="block text-sm font-medium mb-1">
                Confirmation du mot de passe
            </label>
            <input type="password" id="mot_de_passe_confirmation" name="mot_de_passe_confirmation"
                   class="w-full rounded border px-3 py-2
                          <?= isset($erreurs['mot_de_passe_confirmation']) ? 'border-coupable' : 'border-gray-300' ?>">
            <?php if (isset($erreurs['mot_de_passe_confirmation'])): ?>
                <p class="mt-1 text-sm text-coupable"><?= e($erreurs['mot_de_passe_confirmation']) ?></p>
            <?php endif; ?>
        </div>

        <button type="submit"
                class="w-full rounded bg-dore py-2.5 font-semibold text-encre hover:bg-yellow-500">
            Créer mon compte
        </button>

    </form>

    <p class="mt-6 text-center text-sm text-gray-600">
        Déjà inscrit ?
        <a href="connexion.php" class="font-medium underline hover:text-dore">Se connecter</a>
    </p>

</div>
