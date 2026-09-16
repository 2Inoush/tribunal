-- =====================================================================
--  Jeu de données de démonstration — Le Tribunal des Disputes Minuscules
--  À importer APRÈS database/schema.sql :
--      mysql -u root -p tribunal < database/seeds.sql
--
--  Mot de passe de tous les comptes de test : « tribunal »
--  (hachage password_hash('tribunal', PASSWORD_DEFAULT))
-- =====================================================================

USE tribunal;

-- Indique au client MySQL que ce fichier est en UTF-8.
-- Sans cette ligne, phpMyAdmin (ou la ligne de commande) peut annoncer une
-- connexion en latin1 : les accents sont alors encodes une seconde fois et
-- « derniere » se retrouve stocke en « derniÃ¨re » dans la base.
SET NAMES utf8mb4;

-- Remise a zero.
-- On utilise DELETE et non TRUNCATE : MySQL refuse de tronquer une table
-- referencee par une cle etrangere (erreur 1701), et l'onglet SQL de
-- phpMyAdmin reactive la verification des cles etrangeres malgre un
-- SET FOREIGN_KEY_CHECKS = 0. DELETE dans l'ordre enfant -> parent
-- fonctionne partout, sans reglage particulier.
DELETE FROM vote;
DELETE FROM affaire;
DELETE FROM utilisateur;

-- DELETE ne remet pas les compteurs a zero, contrairement a TRUNCATE.
ALTER TABLE vote        AUTO_INCREMENT = 1;
ALTER TABLE affaire     AUTO_INCREMENT = 1;
ALTER TABLE utilisateur AUTO_INCREMENT = 1;


-- --- Utilisateurs ----------------------------------------------------
INSERT INTO utilisateur (id, pseudo, email, mot_de_passe) VALUES
(1, 'JugeCitron',   'juge.citron@tribunal.test',   '$2y$10$BBVceI/FnxGqFIPzla1Jme2eWARqRiDhFVhBxJcQ0uTfSPCfb677m'),
(2, 'MaitreCaillou','maitre.caillou@tribunal.test','$2y$10$BBVceI/FnxGqFIPzla1Jme2eWARqRiDhFVhBxJcQ0uTfSPCfb677m'),
(3, 'ColocAnonyme', 'coloc.anonyme@tribunal.test', '$2y$10$BBVceI/FnxGqFIPzla1Jme2eWARqRiDhFVhBxJcQ0uTfSPCfb677m'),
(4, 'GreffierBob',  'greffier.bob@tribunal.test',  '$2y$10$BBVceI/FnxGqFIPzla1Jme2eWARqRiDhFVhBxJcQ0uTfSPCfb677m'),
(5, 'JureNumero7',  'jure.sept@tribunal.test',     '$2y$10$BBVceI/FnxGqFIPzla1Jme2eWARqRiDhFVhBxJcQ0uTfSPCfb677m');


-- --- Affaires --------------------------------------------------------
INSERT INTO affaire (id, id_utilisateur, titre, description, argument_1, argument_2) VALUES
(1, 1, 'L''ananas sur la pizza',
    'Mon colocataire commande systématiquement une pizza ananas pour deux et refuse d''en discuter.',
    'Le sucré-salé est une tradition culinaire millénaire, pas un crime.',
    'Il paie la pizza. Celui qui paie choisit la garniture.'),
(2, 2, 'Les poubelles de la semaine dernière',
    'Nous avions un tour de rôle écrit sur le frigo. Il a sauté son tour deux fois d''affilée.',
    'J''étais en partiels, le sac n''était qu''à moitié plein.',
    'Personne n''a signalé que le camion passait le mardi et non le mercredi.'),
(3, 3, 'Le siège des toilettes laissé relevé',
    'Débat ouvert depuis trois ans dans cet appartement. Aucune trêve à ce jour.',
    'Relever, c''est aussi un geste. Pourquoi serait-ce toujours à moi de rabattre ?',
    NULL),
(4, 1, 'La dernière part de gâteau mangée sans prévenir',
    'Le gâteau d''anniversaire était étiqueté à mon nom. Il en restait une part. Elle a disparu.',
    'Elle était là depuis quatre jours, je pensais sincèrement qu''elle était perdue pour tous.',
    'Aucune étiquette n''était visible sur la part elle-même, seulement sur la boîte.'),
(5, 4, 'Parler pendant le générique de fin',
    'Je demande le silence pendant le générique. On me traite de tyran.',
    'Le générique fait partie de l''œuvre, les noms défilent pour être lus.',
    'Trente secondes de silence ne mettent en péril aucune amitié.');


-- --- Votes -----------------------------------------------------------
--  Affaire 1 : très disputée   |  Affaire 2 : verdict net
--  Affaire 3 : un seul vote    |  Affaire 5 : AUCUN vote
--  (l'affaire 5 reste donc modifiable/supprimable par son auteur — test F3)
INSERT INTO vote (id_utilisateur, id_affaire, choix) VALUES
(2, 1, 'acquitte'),
(3, 1, 'coupable'),
(4, 1, 'acquitte'),
(5, 1, 'coupable'),

(1, 2, 'coupable'),
(3, 2, 'coupable'),
(4, 2, 'coupable'),
(5, 2, 'acquitte'),

(1, 3, 'acquitte'),

(2, 4, 'coupable'),
(3, 4, 'acquitte'),
(5, 4, 'coupable');


-- --- Vérification ----------------------------------------------------
SELECT id, titre, total_votes, pct_acquitte, pct_coupable, modifiable
FROM v_affaire_stats
ORDER BY id;
