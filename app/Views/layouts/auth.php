<?php
/**
 * Gabarit des pages de connexion et d'inscription.
 *
 * Meme principe que main.php, mais le contenu est centre dans une carte
 * etroite : on veut que l'utilisateur se concentre sur le formulaire.
 */
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre ?? '') ?> — Le Tribunal des Disputes Minuscules</title>

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acquitte: '#16a34a',
                        coupable: '#dc2626',
                        dore:     '#c9a227',
                        encre:    '#111827',
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

    <main class="flex-1 w-full flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-md">
            <?php require RACINE . '/app/Views/partials/flash.php'; ?>
            <?= $contenu ?>
        </div>
    </main>

    <?php require RACINE . '/app/Views/partials/footer.php'; ?>

</body>
</html>
