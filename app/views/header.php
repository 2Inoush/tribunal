<?php
/**
 * VUE : haut de page (commun a toutes les pages).
 *
 * Contient le debut du HTML, la barre du haut, et l'affichage
 * du message de bienvenue / d'au revoir s'il y en a un.
 *
 * La page qui inclut ce fichier a defini $titre juste avant.
 */
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre) ?> — Le Tribunal des Disputes Minuscules</title>

    <!-- Chemins relatifs a l'URL de la page (.../tribunal/public/),
         et non au fichier sur le disque : css/ est a cote des pages. -->
    <link rel="stylesheet" href="css/index.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rammetto+One&display=swap" rel="stylesheet">

    <!-- Tailwind par lien direct : pas d'installation, les classes
         comme « text-center » ou « bg-white » fonctionnent tout de suite. -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Nos couleurs (cahier des charges, section 5), utilisables ensuite
        // comme n'importe quelle classe Tailwind : bg-dore, text-coupable...
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        acquitte: '#16a34a',   // vert
                        coupable: '#dc2626',   // rouge
                        dore: '#c9a227',   // accent
                        encre: '#111827',   // noir du texte
                    },
                },
            },
        };
    </script>
</head>

<body class="min-h-screen bg-gray-50 text-encre flex flex-col">

    <header class="bg-encre text-white border-b-4 border-dore">
        <div class="max-w-4xl mx-auto px-4 py-4">

            <!--desktop -->
            <div class="flex items-center justify-between gap-4">

                <a href="index.php" class="flex items-center gap-2 hover:opacity-80">
                    <img
                        src="images/Logo_tribunal.png"
                        alt="Logo du Tribunal"
                        class="w-20 h-20"
                    >
                    <span class="text-lg sm:text-3xl rammetto-one-regular">
                        TDM
                    </span>
                </a>

                <nav class="hidden sm:flex items-center gap-3 text-sm">

                    <a href="classement.php" class="hover:text-dore">
                        Classement
                    </a>

                    <?php if (estConnecte()): ?>

                        <a href="profil.php"
                            class="text-gray-300 hover:text-dore">
                            <?= e(utilisateurConnecte()['pseudo']) ?>
                        </a>

                        <a href="deconnexion.php"
                            class="px-3 py-1.5 rounded border border-gray-500 hover:bg-white hover:text-encre">
                            Déconnexion
                        </a>

                    <?php else: ?>

                        <a href="connexion.php" class="hover:text-dore">
                            Connexion
                        </a>

                        <a href="inscription.php"
                            class="px-3 py-1.5 rounded bg-dore text-encre font-semibold hover:bg-yellow-500">
                            Inscription
                        </a>

                    <?php endif; ?>

                </nav>

                <!-- mobile -->
                <button
                    id="menu-mobile-btn"
                    type="button"
                    class="sm:hidden p-2 rounded border border-gray-500 hover:bg-white hover:text-encre"
                    aria-label="Ouvrir le menu"
                    aria-expanded="false"
                    aria-controls="menu-mobile"
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        class="w-6 h-6"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            
                            d="M4 6h16M4 12h16M4 18h16"
                        />
                    </svg>
                </button>

            </div>

            <nav id="menu-mobile" class="hidden sm:hidden mt-4 pt-4 border-t border-gray-700">

                <div class="flex flex-col gap-3 text-sm">

                    <a href="classement.php"
                        class="py-2 hover:text-dore">
                        Classement
                    </a>

                    <?php if (estConnecte()): ?>

                        <a href="profil.php"
                            class="py-2 text-gray-300 hover:text-dore">
                            <?= e(utilisateurConnecte()['pseudo']) ?>
                        </a>

                        <a href="deconnexion.php"
                            class="py-2 px-3 rounded border border-gray-500 text-center hover:bg-white hover:text-encre">
                            Déconnexion
                        </a>

                    <?php else: ?>

                        <a href="connexion.php"
                            class="py-2 hover:text-dore">
                            Connexion
                        </a>

                        <a href="inscription.php"
                            class="py-2 px-3 rounded bg-dore text-encre font-semibold text-center hover:bg-yellow-500">
                            Inscription
                        </a>

                    <?php endif; ?>

                </div>

            </nav>

        </div>
    </header>

    <script>
        const menuBtn = document.getElementById('menu-mobile-btn');
        const menuMobile = document.getElementById('menu-mobile');

        menuBtn.addEventListener('click', () => {
            const isHidden = menuMobile.classList.toggle('hidden');

            menuBtn.setAttribute('aria-expanded', String(!isHidden));
            menuBtn.setAttribute(
                'aria-label',
                isHidden ? 'Ouvrir le menu' : 'Fermer le menu'
            );
        });
    </script>

    <main class="flex-1 w-full max-w-4xl mx-auto px-4 py-8">

        <?php
        // Message ponctuel (« Bienvenue... »), range en session par la page
        // precedente. On l'affiche puis on l'efface avec unset() : il ne
        // doit apparaitre qu'une seule fois.
        if (isset($_SESSION['message'])):
            ?>
            <div class="mb-6 rounded border-l-4 border-acquitte bg-green-50 px-4 py-3 text-sm text-green-800">
                <?= e($_SESSION['message']) ?>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php
        // Meme chose pour les messages d'erreur, affiches en rouge.
        if (isset($_SESSION['erreur'])):
            ?>
            <div class="mb-6 rounded border-l-4 border-coupable bg-red-50 px-4 py-3 text-sm text-red-800">
                <?= e($_SESSION['erreur']) ?>
            </div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>