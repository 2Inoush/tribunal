<?php
/**
 * VUE : formulaire d'inscription (F1).
 *
 * $erreurs : ['nom_du_champ' => 'message'] apres une tentative ratee
 * $pseudo, $email : ce qui a deja ete tape (jamais le mot de passe)
 */
?>

<div class="mx-auto max-w-md">

    <div class="mb-7 text-center">
        <p class="etiquette mb-2">Nouveau juré</p>
        <h1 class="titre text-3xl">Prêter serment</h1>
        <p class="mt-2 text-sm texte-doux">
            Pour plaider vos causes et rendre la justice.
        </p>
    </div>

    <div class="carte p-6 sm:p-8">

        <form method="post" action="" class="space-y-4" novalidate>

            <div>
                <label for="pseudo" class="etiquette-champ">Pseudo</label>
                <input type="text" id="pseudo" name="pseudo" value="<?= e($pseudo) ?>"
                       class="champ <?= isset($erreurs['pseudo']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['pseudo'])): ?>
                    <p class="message-erreur"><?= e($erreurs['pseudo']) ?></p>
                <?php endif; ?>
            </div>

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
                <!-- On ne remet jamais le mot de passe dans le champ. -->
                <input type="password" id="mot_de_passe" name="mot_de_passe"
                       class="champ <?= isset($erreurs['mot_de_passe']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['mot_de_passe'])): ?>
                    <p class="message-erreur"><?= e($erreurs['mot_de_passe']) ?></p>
                <?php else: ?>
                    <p class="mt-1.5 text-xs text-encre-pale">8 caractères minimum.</p>
                <?php endif; ?>
            </div>

            <div>
                <label for="mot_de_passe_confirmation" class="etiquette-champ">
                    Confirmation
                </label>
                <input type="password" id="mot_de_passe_confirmation"
                       name="mot_de_passe_confirmation"
                       class="champ <?= isset($erreurs['mot_de_passe_confirmation']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['mot_de_passe_confirmation'])): ?>
                    <p class="message-erreur"><?= e($erreurs['mot_de_passe_confirmation']) ?></p>
                <?php endif; ?>
            </div>

            <button type="submit" class="bouton bouton-or w-full">
                Créer mon compte
            </button>

        </form>

    </div>

    <p class="mt-5 text-center text-sm texte-doux">
        Déjà juré ?
        <a href="connexion.php" class="font-semibold text-or hover:underline">Se connecter</a>
    </p>

</div>
