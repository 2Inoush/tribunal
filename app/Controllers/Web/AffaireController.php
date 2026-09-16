<?php
declare(strict_types=1);

namespace App\Controllers\Web;

use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Affaire;

/**
 * AffaireController
 *
 * Pages de creation et de modification d'une affaire (F2, F3, F4).
 *
 */
final class AffaireController extends Controller
{
	public function formulaireCreation(): void
	{
		$this->rendre('affaire-creer', [
			'titre'   => 'Créer une affaire',
			'erreurs' => Session::erreurs(),
			'saisie'  => Session::ancienneSaisie(),
		]);
	}

	public function creer(): void
	{
		if (!Csrf::estValide($_POST['csrf'] ?? null)) {
			Session::flash('erreur', 'Formulaire expiré, merci de réessayer.');
			$this->rediriger('/affaires/creer');
		}

		$titre = trim($_POST['titre'] ?? '');
		$description = trim($_POST['description'] ?? '');
		$argument1 = trim($_POST['argument_1'] ?? '');
		$argument2 = trim($_POST['argument_2'] ?? '');

		$regles = (require RACINE . '/app/Config/config.php')['regles'];

		$validation = new Validator($_POST);
		$validation
			->requis('titre', 'Le titre est obligatoire.')
			->longueur(
				'titre',
				$regles['titre_min'],
				$regles['titre_max'],
				"Le titre doit faire entre {$regles['titre_min']} et {$regles['titre_max']} caractères."
			)
			->requis('description', 'Les faits sont obligatoires.')
			->longueur(
				'description',
				$regles['description_min'],
				PHP_INT_MAX,
				"Les faits doivent faire au moins {$regles['description_min']} caractères."
			)
			->requis('argument_1', 'Le premier argument est obligatoire.')
			->longueur(
				'argument_1',
				$regles['argument_min'],
				PHP_INT_MAX,
				"Le premier argument doit faire au moins {$regles['argument_min']} caractères."
			);

		if ($argument2 !== '' && mb_strlen($argument2) < $regles['argument_min']) {
			$validation->ajouter(
				'argument_2',
				"Le deuxième argument doit faire au moins {$regles['argument_min']} caractères."
			);
		}

		if ($validation->echoue()) {
			Session::memoriserErreurs($validation->erreurs());
			Session::memoriserSaisie($_POST);
			$this->rediriger('/affaires/creer');
		}

		$utilisateur = Session::utilisateur();
		Affaire::creer(
			(int) $utilisateur['id'],
			$titre,
			$description,
			$argument1,
			$argument2 === '' ? null : $argument2
		);

		Session::flash('succes', 'Votre affaire a été créée.');
		$this->rediriger('/');
	}
}
