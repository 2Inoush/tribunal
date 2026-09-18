<?php
/**
 * VUE : haut de page (commun a toutes les pages).
 *
 * Conçu en « mobile first » : la mise en page de base est celle du
 * telephone, et les prefixes « sm: » de Tailwind viennent ELARGIR
 * l'affichage a partir de 640 px. On n'ecrit jamais l'inverse.
 *
 *   Sur telephone : logo + bouton burger. Le menu se deplie dessous.
 *   A partir de 640 px : tout tient sur une ligne, le burger disparait.
 *
 * Le bandeau est decoupe en trois zones nettement separees :
 *   [ logo ]   [ navigation ]   │   [ compte : action + juré ]
 *
 * La page qui inclut ce fichier a defini $titre juste avant.
 */

// De « /tribunal/public/classement.php » basename() ne garde que
// « classement.php ». Sert a souligner la page ouverte dans le menu.
$pageCourante = basename($_SERVER['SCRIPT_NAME']);

// Les pages du site : adresse => libelle
$pages = [
    'index.php' => 'Les affaires',
    'classement.php' => 'Le banc des jurés',
];
?>
<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($titre) ?> — Le Tribunal des Disputes Minuscules</title>

    <!-- Chemins relatifs a l'URL de la page (.../tribunal/public/),
         et non au fichier sur le disque : css/ est a cote des pages.
         Le « ?v= » force le navigateur a recharger la feuille plutot
         que de ressortir son ancienne copie en cache. -->
    <link rel="stylesheet" href="css/index.css?v=15">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <!-- Trois polices, trois roles :
         Fraunces -> les titres (serif a caractere)
         Inter    -> le texte courant
         Rammetto -> le sigle TDM uniquement -->
    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700&family=Rammetto+One&display=swap">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        // Les couleurs de la charte, reprises de public/css/index.css
        // pour pouvoir ecrire bg-papier, text-or, border-trait...
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        papier: '#f7f4ee',
                        blanc: '#fffefb',
                        encre: '#17212e',
                        'encre-douce': '#5c6672',
                        'encre-pale': '#9aa3ad',
                        or: '#b8901f',
                        acquitte: '#16794a',
                        coupable: '#b3261e',
                        trait: '#e2dcd0',
                    },
                },
            },
        };
    </script>
</head>

<body class="min-h-screen bg-papier text-encre flex flex-col">

    <header class="bandeau">

        <!-- ================= LA BARRE ================= -->
        <div class="mx-auto flex h-14 max-w-5xl items-center gap-3 px-4 sm:h-16 sm:gap-8">

            <!-- ---- ZONE 1 : l'identite ---- -->
            <a href="index.php" class="flex shrink-0 items-center gap-2.5">
                <img src="images/Logo_tribunal.png" alt=""
                    class="h-8 w-8 rounded-full border border-trait sm:h-9 sm:w-9">
                <span class="logo-sigle">TDM</span>
            </a>

            <!-- ---- ZONE 2 : ou aller (masquee sur telephone) ---- -->
            <nav class="hidden items-center gap-7 sm:flex">
                <?php foreach ($pages as $fichier => $libelle): ?>
                    <a href="<?= $fichier ?>" <?= $fichier === $pageCourante ? 'aria-current="page"' : '' ?>
                        class="lien-page whitespace-nowrap">
                        <?= e($libelle) ?>
                    </a>
                <?php endforeach; ?>
            </nav>

            <!-- ---- ZONE 3 : qui je suis (masquee sur telephone) ---- -->
            <div class="ml-auto hidden shrink-0 items-center gap-4 sm:flex">

                <span class="separateur-vertical"></span>

                <?php if (estConnecte()): ?>

                    <a href="affaire-creer.php" class="bouton bouton-or">
                        <span aria-hidden="true">+</span>
                        Déposer une affaire
                    </a>

                    <a href="profil.php" class="flex items-center gap-2" title="Mon profil">
                        <span class="pastille">
                            <?= e(mb_strtoupper(mb_substr(utilisateurConnecte()['pseudo'], 0, 1))) ?>
                        </span>
                        <span class="hidden text-sm font-medium lg:inline">
                            <?= e(utilisateurConnecte()['pseudo']) ?>
                        </span>
                    </a>

                    <a href="deconnexion.php" class="text-sm text-encre-pale hover:text-coupable">
                        Se déconnecter
                    </a>

                <?php else: ?>

                    <a href="connexion.php" class="text-sm font-medium text-encre-douce hover:text-encre">
                        Connexion
                    </a>

                    <a href="inscription.php" class="bouton bouton-encre">
                        Prêter serment
                    </a>

                <?php endif; ?>

            </div>

            <!-- ---- Le bouton burger : telephone uniquement ---- -->
            <button type="button" data-menu-bouton class="ml-auto flex h-10 w-10 shrink-0 items-center justify-center
                       rounded-lg bg-encre text-blanc hover:bg-[#22303f] sm:hidden" aria-label="Ouvrir le menu"
                aria-expanded="false">

                <!-- Trois traits : l'icone burger -->
                <svg data-icone-ouvrir width="18" height="18" viewBox="0 0 18 18" fill="none" stroke="currentColor"
                    stroke-width="1.8" stroke-linecap="round">
                    <path d="M2 4.5h14M2 9h14M2 13.5h14" />
                </svg>

                <!-- Une croix : affichee quand le menu est ouvert -->
                <svg data-icone-fermer class="hidden" width="18" height="18" viewBox="0 0 18 18" fill="none"
                    stroke="currentColor" stroke-width="1.8" stroke-linecap="round">
                    <path d="M4 4l10 10M14 4L4 14" />
                </svg>

            </button>

        </div>

        <!-- ================= LE MENU DEPLIE (telephone) =================
         « hidden » au depart : le JavaScript juste en dessous retire
         cette classe quand on appuie sur le burger. -->
        <div data-menu class="hidden border-t border-trait bg-blanc sm:hidden">
            <nav class="mx-auto max-w-5xl px-4 py-3">

                <?php foreach ($pages as $fichier => $libelle): ?>
                    <a href="<?= $fichier ?>" <?= $fichier === $pageCourante ? 'aria-current="page"' : '' ?> class="block rounded-lg px-3 py-2.5 text-[15px] font-medium
                          <?= $fichier === $pageCourante
                              ? 'bg-papier text-encre'
                              : 'text-encre-douce' ?>">
                        <?= e($libelle) ?>
                    </a>
                <?php endforeach; ?>

                <hr class="filet my-3">

                <?php if (estConnecte()): ?>

                    <a href="affaire-creer.php" class="bouton bouton-or w-full">
                        <span aria-hidden="true">+</span>
                        Déposer une affaire
                    </a>

                    <a href="profil.php" class="mt-3 flex items-center gap-3 rounded-lg px-3 py-2.5">
                        <span class="pastille">
                            <?= e(mb_strtoupper(mb_substr(utilisateurConnecte()['pseudo'], 0, 1))) ?>
                        </span>
                        <span class="min-w-0 flex-1">
                            <span class="block truncate text-[15px] font-medium">
                                <?= e(utilisateurConnecte()['pseudo']) ?>
                            </span>
                            <span class="block text-xs text-encre-pale">Mon profil</span>
                        </span>
                    </a>

                    <a href="deconnexion.php" class="block rounded-lg px-3 py-2.5 text-[15px] text-coupable">
                        Se déconnecter
                    </a>

                <?php else: ?>

                    <a href="inscription.php" class="bouton bouton-or w-full">
                        Prêter serment
                    </a>

                    <a href="connexion.php" class="mt-2 block rounded-lg px-3 py-2.5 text-center text-[15px]
                          font-medium text-encre-douce">
                        J'ai déjà un compte
                    </a>

                <?php endif; ?>

            </nav>
        </div>

    </header>

    <script>
        // Le menu burger : on affiche ou on cache le panneau, et on echange
        // les deux icones. classList.toggle('hidden') ajoute la classe si
        // elle est absente, et l'enleve si elle est presente.
        (function () {
            const bouton = document.querySelector('[data-menu-bouton]');
            const menu = document.querySelector('[data-menu]');

            bouton.addEventListener('click', function () {
                const ouvert = menu.classList.toggle('hidden') === false;

                document.querySelector('[data-icone-ouvrir]').classList.toggle('hidden', ouvert);
                document.querySelector('[data-icone-fermer]').classList.toggle('hidden', !ouvert);

                // aria-expanded indique l'etat aux lecteurs d'ecran.
                bouton.setAttribute('aria-expanded', ouvert ? 'true' : 'false');
                bouton.setAttribute('aria-label', ouvert ? 'Fermer le menu' : 'Ouvrir le menu');
            });
        })();
    </script>


    <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-6 sm:py-10">

        <?php
        // Message ponctuel (« Bienvenue... »), range en session par la page
        // precedente. On l'affiche puis on l'efface avec unset() : il ne
        // doit apparaitre qu'une seule fois.
        if (isset($_SESSION['message'])):
            ?>
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-acquitte/25 bg-[#e7f3ec] px-4 py-3">
                <span class="mt-px text-acquitte" aria-hidden="true">✓</span>
                <p class="text-sm text-acquitte"><?= e($_SESSION['message']) ?></p>
            </div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>

        <?php
        // Meme chose pour les messages d'erreur.
        if (isset($_SESSION['erreur'])):
            ?>
            <div class="mb-5 flex items-start gap-3 rounded-xl border border-coupable/25 bg-[#fbeae8] px-4 py-3">
                <span class="mt-px text-coupable" aria-hidden="true">!</span>
                <p class="text-sm text-coupable"><?= e($_SESSION['erreur']) ?></p>
            </div>
            <?php unset($_SESSION['erreur']); ?>
        <?php endif; ?>