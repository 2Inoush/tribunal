<?php
/**
 * VUE : page d'accueil — la carte de l'affaire a juger.
 *
 * Mise en page ecrite par Enora.
 *
 * Le controleur (app/controllers/accueil.php) a prepare $affaire,
 * qui vaut null s'il n'y a aucune affaire en base.
 *
 * Il n'y a ni connexion ni requete SQL ici : une vue ne fait qu'afficher.
 * C'est le modele qui parle a la base (app/models/affaire.php), et le
 * controleur qui l'appelle.
 */
?>
<div class=" py-12">
    <p class="text-5xl mb-4 text-center" aria-hidden="true">&#9878;</p>

        <h1 class="text-3xl font-bold mb-3 text-center" style="font-family: Georgia, serif;">
            La séance est ouverte
        </h1>

    <?php if (estConnecte()): ?>
        
        

        <?php if ($affaire): ?>
            <div class="rounded-lg shadow-md bg-white p-6">
            <h2 class="text-xl font-bold mb-2"><?= e($affaire['titre']) ?></h2>
            <p class="text-gray-600">
                <?= nl2br(e($affaire['description'])) ?>
            </p>
            <div class="w-full  ">
            <details class="group">
                <summary class="flex cursor-pointer  items-center justify-between py-4 font-semibold">
                <span>Voir les arguments</span>
                <span class="transition-transform group-open:rotate-180">
                    ↓
                </span>
                </summary>

                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= e($affaire['argument_1']) ?>
                </div>
                <?php if ($affaire['argument_2']): ?>
                <div class="border-t border-gray-200 px-4 pb-4 pt-3 text-gray-600">
                    <?= e($affaire['argument_2']) ?>
                </div>
                <?php endif; ?>
            </details>
            </div>
            
            <a href="">
                <button type="button"
            class="text-white rounded-lg shadow-md bg-green-400 px-8 py-4  hover:bg-green-600  font-medium  text-sm  text-center leading-5">Acquitté</button>
            </a>
            <a href="">
                <button type="button"
            class="text-white rounded-lg shadow-md bg-red-400 px-8 py-4  hover:bg-red-600  font-medium  text-sm  text-center leading-5">Coupable</button>
            </a>
        </div>



        <?php else: ?>

            <p>Aucune affaire disponible.</p>

        <?php endif; ?>
        
        

    <?php else: ?>
        <p class="text-gray-600">
            Connectez-vous pour plaider votre cause et rendre la justice.
        </p>
    <?php endif; ?>

</div>
