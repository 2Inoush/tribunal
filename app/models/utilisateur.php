<?php
/**
 * MODELE : utilisateur
 *
 * C'est le « M » de MVC.
 * Toutes les requetes SQL sur la table `utilisateur` sont ici, et
 * nulle part ailleurs : si on veut changer une requete, on sait ou aller.
 *
 * Chaque fonction recoit $pdo (la connexion, creee dans config.php).
 *
 * Toutes les requetes sont PREPAREES : on ecrit des « ? » dans le SQL et
 * on envoie les valeurs a part, avec execute([...]). Du coup, meme si
 * quelqu'un tape du SQL dans un formulaire, ce sera traite comme du texte
 * et pas comme une commande. C'est la protection contre l'injection SQL.
 */


/**
 * Cherche un utilisateur a partir de son email.
 * Renvoie un tableau avec ses infos, ou null s'il n'existe pas.
 */
function trouverUtilisateurParEmail(PDO $pdo, string $email): ?array
{
    $requete = $pdo->prepare('SELECT * FROM utilisateur WHERE email = ?');
    $requete->execute([$email]);

    $utilisateur = $requete->fetch();

    // fetch() renvoie false quand il n'y a pas de resultat ; on prefere null.
    return $utilisateur ?: null;
}


/**
 * Cherche un utilisateur a partir de son identifiant.
 * Sert a la page profil : la session ne garde que l'id et le pseudo,
 * on relit donc la base pour avoir l'email et la date d'inscription.
 */
function trouverUtilisateurParId(PDO $pdo, int $id): ?array
{
    $requete = $pdo->prepare('SELECT * FROM utilisateur WHERE id = ?');
    $requete->execute([$id]);

    return $requete->fetch() ?: null;
}


/** Est-ce que cet email est deja pris ? */
function emailDejaUtilise(PDO $pdo, string $email): bool
{
    $requete = $pdo->prepare('SELECT id FROM utilisateur WHERE email = ?');
    $requete->execute([$email]);

    return $requete->fetch() !== false;
}


/** Est-ce que ce pseudo est deja pris ? */
function pseudoDejaPris(PDO $pdo, string $pseudo): bool
{
    $requete = $pdo->prepare('SELECT id FROM utilisateur WHERE pseudo = ?');
    $requete->execute([$pseudo]);

    return $requete->fetch() !== false;
}


/**
 * Cree un compte et renvoie le tableau du nouvel utilisateur.
 *
 * password_hash() transforme le mot de passe en une suite de caracteres
 * illisible. On ne stocke JAMAIS le mot de passe en clair : meme nous,
 * en regardant la base, on ne peut pas le lire.
 */
function creerUtilisateur(PDO $pdo, string $pseudo, string $email, string $motDePasse): array
{
    $requete = $pdo->prepare(
        'INSERT INTO utilisateur (pseudo, email, mot_de_passe) VALUES (?, ?, ?)'
    );

    $requete->execute([
        $pseudo,
        $email,
        password_hash($motDePasse, PASSWORD_DEFAULT),
    ]);

    // On relit l'utilisateur qu'on vient de creer pour avoir son id.
    return trouverUtilisateurParEmail($pdo, $email);
}


/**
 * Verifie un email et un mot de passe.
 * Renvoie l'utilisateur si c'est bon, null sinon.
 *
 * password_verify() compare le mot de passe tape avec le hachage stocke.
 * (On ne peut pas faire l'inverse : un hachage ne se « dehache » pas.)
 */
function verifierIdentifiants(PDO $pdo, string $email, string $motDePasse): ?array
{
    $utilisateur = trouverUtilisateurParEmail($pdo, $email);

    if ($utilisateur === null) {
        return null;
    }

    if (!password_verify($motDePasse, $utilisateur['mot_de_passe'])) {
        return null;
    }

    return $utilisateur;
}
