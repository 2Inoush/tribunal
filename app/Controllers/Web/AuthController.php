<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Utilisateur;

/**
 * AuthController — F1 : inscription et connexion
 *
 * Chaque formulaire a deux methodes :
 *   - une qui AFFICHE le formulaire        (requete GET)
 *   - une qui TRAITE l'envoi du formulaire (requete POST)
 *
 * En cas d'erreur, on remet l'utilisateur sur le formulaire avec
 * les messages et ce qu'il avait deja saisi. En cas de succes,
 * on ouvre la session et on redirige vers l'accueil.
 */
final class AuthController extends Controller
{
    // =====================================================================
    //  INSCRIPTION
    // =====================================================================

    public function formulaireInscription(): void
    {
        // Deja connecte : rien a faire ici.
        if (Session::estConnecte()) {
            $this->rediriger('/');
        }

        $this->rendre('inscription', [
            'titre'   => 'Inscription',
            'erreurs' => Session::erreurs(),
            'saisie'  => Session::ancienneSaisie(),
        ], 'auth');
    }

    public function inscription(): void
    {
        if (!Csrf::estValide($_POST['csrf'] ?? null)) {
            Session::flash('erreur', 'Formulaire expire, merci de reessayer.');
            $this->rediriger('/inscription');
        }

        $pseudo     = trim($_POST['pseudo'] ?? '');
        $email      = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        $regles = (require RACINE . '/app/Config/config.php')['regles'];

        // 1. Les champs sont-ils correctement remplis ?
        $validation = new Validator($_POST);
        $validation
            ->requis('pseudo', 'Le pseudo est obligatoire.')
            ->longueur('pseudo', $regles['pseudo_min'], $regles['pseudo_max'],
                "Le pseudo doit faire entre {$regles['pseudo_min']} et {$regles['pseudo_max']} caracteres.")
            ->requis('email', "L'adresse email est obligatoire.")
            ->email('email', "Cette adresse email n'est pas valide.")
            ->requis('mot_de_passe', 'Le mot de passe est obligatoire.')
            ->longueur('mot_de_passe', $regles['mot_de_passe_min'], 72,
                "Le mot de passe doit faire au moins {$regles['mot_de_passe_min']} caracteres.")
            ->identiques('mot_de_passe_confirmation', 'mot_de_passe',
                'Les deux mots de passe ne sont pas identiques.');

        // 2. Le pseudo ou l'email sont-ils deja pris ?
        //    (la base l'interdit aussi, mais on prefere un message clair)
        if ($email !== '' && Utilisateur::emailExiste($email)) {
            $validation->ajouter('email', 'Cette adresse email est deja utilisee.');
        }
        if ($pseudo !== '' && Utilisateur::pseudoExiste($pseudo)) {
            $validation->ajouter('pseudo', 'Ce pseudo est deja pris.');
        }

        if ($validation->echoue()) {
            Session::memoriserErreurs($validation->erreurs());
            Session::memoriserSaisie($_POST);
            $this->rediriger('/inscription');
        }

        // 3. Tout est bon : on cree le compte et on connecte directement.
        $id = Utilisateur::creer($pseudo, $email, $motDePasse);

        Session::connecter(Utilisateur::trouverParId($id));
        Session::flash('succes', "Bienvenue au tribunal, {$pseudo} !");

        $this->rediriger('/');
    }

    // =====================================================================
    //  CONNEXION
    // =====================================================================

    public function formulaireConnexion(): void
    {
        if (Session::estConnecte()) {
            $this->rediriger('/');
        }

        $this->rendre('connexion', [
            'titre'   => 'Connexion',
            'erreurs' => Session::erreurs(),
            'saisie'  => Session::ancienneSaisie(),
        ], 'auth');
    }

    public function connexion(): void
    {
        if (!Csrf::estValide($_POST['csrf'] ?? null)) {
            Session::flash('erreur', 'Formulaire expire, merci de reessayer.');
            $this->rediriger('/connexion');
        }

        $email      = trim($_POST['email'] ?? '');
        $motDePasse = $_POST['mot_de_passe'] ?? '';

        $validation = new Validator($_POST);
        $validation
            ->requis('email', "L'adresse email est obligatoire.")
            ->requis('mot_de_passe', 'Le mot de passe est obligatoire.');

        if ($validation->echoue()) {
            Session::memoriserErreurs($validation->erreurs());
            Session::memoriserSaisie($_POST);
            $this->rediriger('/connexion');
        }

        $utilisateur = Utilisateur::verifierIdentifiants($email, $motDePasse);

        if ($utilisateur === null) {
            // Message volontairement vague : on ne dit pas si c'est l'email
            // ou le mot de passe qui est faux, pour ne pas reveler quels
            // comptes existent.
            Session::memoriserErreurs(['email' => 'Email ou mot de passe incorrect.']);
            Session::memoriserSaisie($_POST);
            $this->rediriger('/connexion');
        }

        Session::connecter($utilisateur);
        Session::flash('succes', "Content de vous revoir, {$utilisateur['pseudo']} !");

        $this->rediriger('/');
    }

    // =====================================================================
    //  DECONNEXION
    // =====================================================================

    public function deconnexion(): void
    {
        Session::deconnecter();

        // La session vient d'etre detruite : on en ouvre une nouvelle
        // juste pour pouvoir afficher le message sur la page d'arrivee.
        $config = require RACINE . '/app/Config/config.php';
        Session::demarrer($config['session']);
        Session::flash('succes', 'Vous etes deconnecte. A bientot !');

        $this->rediriger('/');
    }
}
