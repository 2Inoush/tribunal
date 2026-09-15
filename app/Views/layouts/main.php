<?php
/**
 * Gabarit principal.
 *
 * Le controleur a prepare $contenu (le HTML de la page) et $titre.
 * Ce fichier pose la structure commune : <head>, header, contenu, footer.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre ?? '') ?> — Le Tribunal des Disputes Minuscules</title>

    <!-- Tailwind en CDN : pratique pour le developpement, aucune installation.
         Pour la mise en production, on passera par une version compilee. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Les couleurs de la direction graphique (cahier des charges, section 5)
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acquitte: '#16a34a',   // vert
                        coupable: '#dc2626',   // rouge
                        dore:     '#c9a227',   // accent
                        encre:    '#111827',   // noir de texte
                    },
                    fontFamily: {
                        titre: ['Georgia', 'serif'],
                    },
                },
            },
        };
    </script>
</head>
<body class="min-h-screen bg-gray-50 text-encre flex flex-col">

    <?php require RACINE . '/app/Views/partials/header.php'; ?>

    <main class="flex-1 w-full max-w-4xl mx-auto px-4 py-8">
        <?php require RACINE . '/app/Views/partials/flash.php'; ?>
        <?= $contenu ?>
    </main>

    <?php require RACINE . '/app/Views/partials/footer.php'; ?>

</body>
</html>
