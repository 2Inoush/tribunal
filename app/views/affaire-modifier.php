<?php
/**
 * VUE : formulaire de modification d'une affaire (F3).
 *
 * Meme mise en forme que la creation : ce sont les memes champs.
 *
 * Variables attendues du controleur (a ecrire) :
 *   $erreurs, $titreAffaire, $description, $argument1, $argument2
 *
 * Les ?? '' ci-dessous permettent a la page de s'afficher meme tant que
 * le controleur ne remplit pas encore ces variables. Ils pourront etre
 * retires une fois le controleur termine.
 */

$erreurs      = $erreurs      ?? [];
$titreAffaire = $titreAffaire ?? '';
$description  = $description  ?? '';
$argument1    = $argument1    ?? '';
$argument2    = $argument2    ?? '';
?>

<div class="mx-auto max-w-xl">

    <div class="mb-7">
        <p class="etiquette mb-2">Affaire n° modifiable</p>
        <h1 class="titre text-3xl">Modifier l'affaire</h1>
        <p class="mt-2 text-sm texte-doux">
            Aucun vote n'a encore été déposé : le dossier est encore ouvert.
        </p>
    </div>

    <div class="carte p-6 sm:p-8">

        <form method="post" action="" class="space-y-5" novalidate>

            <div>
                <label for="titre" class="etiquette-champ">Titre de l'affaire</label>
                <input type="text" id="titre" name="titre" value="<?= e($titreAffaire) ?>"
                       placeholder="Ex : L'ananas sur la pizza"
                       class="champ <?= isset($erreurs['titre']) ? 'champ-errone' : '' ?>">
                <?php if (isset($erreurs['titre'])): ?>
                    <p class="message-erreur"><?= e($erreurs['titre']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="description" class="etiquette-champ">Exposé des faits</label>
                <!-- Attention : pas d'espace ni de retour a la ligne entre
                     <textarea> et sa valeur, ils apparaitraient dans le champ. -->
                <textarea id="description" name="description" rows="5"
                          placeholder="Racontez ce qui s'est passé, sans rien omettre."
                          class="champ <?= isset($erreurs['description']) ? 'champ-errone' : '' ?>"><?= e($description) ?></textarea>
                <?php if (isset($erreurs['description'])): ?>
                    <p class="message-erreur"><?= e($erreurs['description']) ?></p>
                <?php endif; ?>
            </div>

            <hr class="filet">

            <div>
                <label for="argument_1" class="etiquette-champ">Argument de défense n° 1</label>
                <textarea id="argument_1" name="argument_1" rows="3"
                          class="champ <?= isset($erreurs['argument_1']) ? 'champ-errone' : '' ?>"><?= e($argument1) ?></textarea>
                <?php if (isset($erreurs['argument_1'])): ?>
                    <p class="message-erreur"><?= e($erreurs['argument_1']) ?></p>
                <?php endif; ?>
            </div>

            <div>
                <label for="argument_2" class="etiquette-champ">
                    Argument de défense n° 2
                    <span class="font-normal text-encre-pale">— facultatif</span>
                </label>
                <textarea id="argument_2" name="argument_2" rows="3"
                          class="champ <?= isset($erreurs['argument_2']) ? 'champ-errone' : '' ?>"><?= e($argument2) ?></textarea>
                <?php if (isset($erreurs['argument_2'])): ?>
                    <p class="message-erreur"><?= e($erreurs['argument_2']) ?></p>
                <?php endif; ?>
            </div>

            <div class="flex gap-3 pt-1">
                <a href="profil.php" class="bouton bouton-contour flex-1">Annuler</a>
                <button type="submit" class="bouton bouton-or flex-1">
                    Enregistrer
                </button>
            </div>

        </form>

    </div>

</div>
