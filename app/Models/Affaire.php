<?php
declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

/**
 * Affaire
 *
 * Table `affaire` et vue `v_affaire_stats`.
 * lister(), trouver(), creer(), modifier(), supprimer(), estModifiable()
 *
 */
final class Affaire extends Model
{
	public static function creer(
		int $idUtilisateur,
		string $titre,
		string $description,
		string $argument1,
		?string $argument2
	): int {
		$sql = 'INSERT INTO affaire
					(id_utilisateur, titre, description, argument_1, argument_2)
				VALUES (?, ?, ?, ?, ?)';

		$requete = self::db()->prepare($sql);
		$requete->execute([
			$idUtilisateur,
			$titre,
			$description,
			$argument1,
			$argument2,
		]);

		return (int) self::db()->lastInsertId();
	}
}
