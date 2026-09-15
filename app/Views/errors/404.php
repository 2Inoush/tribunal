<?php /** Page 404 : l'URL demandee ne correspond a aucune route. */ ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Page introuvable — Le Tribunal des Disputes Minuscules</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50 text-gray-900 flex items-center justify-center px-4">
    <div class="text-center">
        <p class="text-5xl mb-4" aria-hidden="true">&#9878;</p>
        <h1 class="text-2xl font-bold mb-2" style="font-family: Georgia, serif;">
            Affaire classee sans suite
        </h1>
        <p class="text-gray-600 mb-6">Cette page n'existe pas.</p>
        <a href="<?= url('/') ?>"
           class="inline-block rounded bg-gray-900 px-4 py-2 text-white hover:bg-gray-700 transition">
            Retour a l'accueil
        </a>
    </div>
</body>
</html>
