<?php
/**
 * VUE : formulaire de connexion (F1).
 * Le controleur a prepare $erreurs et $email.
 */
?>

<div class="mx-auto max-w-md">

    <div class="mb-7 text-center">
        <p class="etiquette mb-2">Accès au prétoire</p>
        <h1 class="titre text-3xl">Entrer au tribunal</h1>
    </div>

    <div class="carte p-6 sm:p-8">

        <form method="post" action="" class="space-y-4" novalidate>

            <div>
                <label for="email" class="etiquette-champ">Email</label>
                <input type="email" id="email" name="email" value="<?= e($email) ?>"
                       class="champ <?= isset($erreurs['email']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['email'])): ?>
                    <p class="message-erreur"><?= e($erreurs['email']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="mot_de_passe" class="etiquette-champ">Mot de passe</label>
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       class="champ <?= isset($erreurs['mot_de_passe']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['mot_de_passe'])): ?>
                    <p class="message-erreur"><?= e($erreurs['mot_de_passe']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="bouton bouton-encre w-full">
                Se connecter
            </button>

        </form>

    </div>

    <p class="mt-5 text-center text-sm texte-doux">
        Pas encore juré ?
        <a href="inscription.php" class="font-semibold text-or hover:underline">Prêter serment</a>
    </p>

</div>
