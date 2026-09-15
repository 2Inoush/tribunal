<?php
/**
 * Affiche les messages ponctuels (succes ou erreur).
 * lireFlash() efface le message apres lecture : il ne s'affiche qu'une fois.
 */
$succes = App\Core\Session::lireFlash('succes');
$erreur = App\Core\Session::lireFlash('erreur');
?>

<?php if ($succes !== null): ?>
    <div class="mb-6 rounded border-l-4 border-acquitte bg-green-50 px-4 py-3 text-sm text-green-800">
        <?= e($succes) ?>
    </div>
<?php endif; ?>

<?php if ($erreur !== null): ?>
    <div class="mb-6 rounded border-l-4 border-coupable bg-red-50 px-4 py-3 text-sm text-red-800">
        <?= e($erreur) ?>
    </div>
<?php endif; ?>
