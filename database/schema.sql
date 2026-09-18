-- =====================================================================
--  LE TRIBUNAL DES DISPUTES MINUSCULES
--  Schéma de la base de données — MySQL 8 / MariaDB 10.4+
--  Réf. cahier des charges § 8 « Modèle de données »
--
--  Trois entités : utilisateur, affaire, vote.
--    - une affaire appartient à un utilisateur (auteur)
--    - un vote relie un utilisateur à une affaire avec un choix
--      (« acquitte » ou « coupable »), une seule fois par affaire.
--
--  Import :  mysql -u root -p < database/schema.sql
-- =====================================================================

-- Ce fichier est en UTF-8 : on le dit au client avant toute chose.
SET NAMES utf8mb4;

DROP DATABASE IF EXISTS tribunal;
CREATE DATABASE tribunal
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
USE tribunal;


-- ---------------------------------------------------------------------
--  Table : utilisateur                                          (F1, F9)
-- ---------------------------------------------------------------------
CREATE TABLE utilisateur (
    id              INT UNSIGNED    NOT NULL AUTO_INCREMENT,
    pseudo          VARCHAR(30)     NOT NULL                      COMMENT 'Nom affiché publiquement',
    email           VARCHAR(180)    NOT NULL                      COMMENT 'Identifiant de connexion',
    mot_de_passe    VARCHAR(255)    NOT NULL                      COMMENT 'Hachage password_hash() — jamais en clair',
    date_creation   DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    UNIQUE KEY uq_utilisateur_email  (email),   -- erreur F1 « email déjà utilisé »
    UNIQUE KEY uq_utilisateur_pseudo (pseudo),

    -- Garde-fous applicatifs doublés en base
    CONSTRAINT ck_utilisateur_pseudo CHECK (CHAR_LENGTH(pseudo) >= 3),
    CONSTRAINT ck_utilisateur_email  CHECK (email LIKE '%_@_%._%')
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
--  Table : affaire                                          (F2, F3, F4)
-- ---------------------------------------------------------------------
CREATE TABLE affaire (
    id                INT UNSIGNED  NOT NULL AUTO_INCREMENT,
    id_utilisateur    INT UNSIGNED  NOT NULL                      COMMENT 'Auteur de l''affaire',
    titre             VARCHAR(120)  NOT NULL,
    description       TEXT          NOT NULL                      COMMENT 'Exposé des faits',
    argument_1        TEXT          NOT NULL                      COMMENT 'Argument de défense n°1',
    argument_2        TEXT          NULL                          COMMENT 'Argument de défense n°2 (facultatif)',
    date_creation     DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    date_modification DATETIME      NULL ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- F4 : l'auteur supprimé emporte ses affaires (et, en cascade, leurs votes)
    CONSTRAINT fk_affaire_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
        ON DELETE CASCADE
        ON UPDATE CASCADE,

    -- Erreur F2 « titre trop court ou trop long »
    CONSTRAINT ck_affaire_titre CHECK (CHAR_LENGTH(titre) BETWEEN 5 AND 120),

    KEY idx_affaire_auteur (id_utilisateur),
    KEY idx_affaire_date   (date_creation DESC)   -- flux d'accueil : plus récentes d'abord
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
--  Table : vote                                             (F5, F6, F9)
-- ---------------------------------------------------------------------
CREATE TABLE vote (
    id              INT UNSIGNED                  NOT NULL AUTO_INCREMENT,
    id_utilisateur  INT UNSIGNED                  NOT NULL         COMMENT 'Votant',
    id_affaire      INT UNSIGNED                  NOT NULL         COMMENT 'Affaire concernée',
    choix           ENUM('acquitte','coupable')   NOT NULL,
    date_vote       DATETIME                      NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Règle de sécurité § 7 : un seul vote par utilisateur et par affaire.
    -- Contrôlée côté serveur ET verrouillée ici (erreur F5 « double vote »).
    UNIQUE KEY uq_vote_utilisateur_affaire (id_utilisateur, id_affaire),

    CONSTRAINT fk_vote_utilisateur
        FOREIGN KEY (id_utilisateur) REFERENCES utilisateur (id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    -- F4 : supprimer une affaire supprime ses votes
    CONSTRAINT fk_vote_affaire
        FOREIGN KEY (id_affaire) REFERENCES affaire (id)
        ON DELETE CASCADE ON UPDATE CASCADE,

    KEY idx_vote_affaire (id_affaire, choix)      -- comptage F6
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
--  VUES DE LECTURE
--
--  « SQL SECURITY INVOKER » est important : sans lui, MySQL retient le
--  compte qui a cree la vue (son DEFINER) et applique SES droits a
--  chaque lecture. Une vue importee chez un hebergeur mutualise garde
--  alors en memoire un compte qui n'y existe pas, et toute lecture
--  echoue avec « Access denied », meme avec des identifiants corrects.
--  Avec INVOKER, ce sont les droits du compte qui interroge la vue
--  qui s'appliquent : le schema devient portable.
--  Elles évitent de recalculer les compteurs dans le PHP et gardent
--  les contrôleurs courts (un SELECT = une réponse JSON).
-- =====================================================================

-- ---------------------------------------------------------------------
--  v_affaire_stats — l'affaire + son taux de vote en direct        (F6)
--  Sert aussi à F3 : modification autorisée tant que total_votes = 0.
-- ---------------------------------------------------------------------
CREATE OR REPLACE SQL SECURITY INVOKER VIEW v_affaire_stats AS
SELECT
    a.id,
    a.id_utilisateur,
    u.pseudo                AS auteur,
    a.titre,
    a.description,
    a.argument_1,
    a.argument_2,
    a.date_creation,
    a.date_modification,
    COUNT(v.id)                                                     AS total_votes,
    COALESCE(SUM(v.choix = 'acquitte'), 0)                          AS votes_acquitte,
    COALESCE(SUM(v.choix = 'coupable'), 0)                          AS votes_coupable,
    -- État neutre quand aucun vote : 0 %, pas une erreur (cf. F6)
    CASE WHEN COUNT(v.id) = 0 THEN 0
         ELSE ROUND(SUM(v.choix = 'acquitte') * 100.0 / COUNT(v.id))
    END                                                             AS pct_acquitte,
    CASE WHEN COUNT(v.id) = 0 THEN 0
         ELSE ROUND(SUM(v.choix = 'coupable') * 100.0 / COUNT(v.id))
    END                                                             AS pct_coupable,
    (COUNT(v.id) = 0)                                               AS modifiable
FROM affaire a
JOIN utilisateur u ON u.id = a.id_utilisateur
LEFT JOIN vote   v ON v.id_affaire = a.id
GROUP BY a.id, u.pseudo;


-- ---------------------------------------------------------------------
--  v_classement_jures — classement par activité            (F9, bonus)
--  Départage les égalités : votes, puis affaires, puis ancienneté.
-- ---------------------------------------------------------------------
CREATE OR REPLACE SQL SECURITY INVOKER VIEW v_classement_jures AS
SELECT
    u.id,
    u.pseudo,
    u.date_creation,
    COUNT(DISTINCT v.id)                            AS nb_votes,
    COUNT(DISTINCT a.id)                            AS nb_affaires,
    (COUNT(DISTINCT v.id) + COUNT(DISTINCT a.id) * 5) AS score_reputation
FROM utilisateur u
LEFT JOIN vote    v ON v.id_utilisateur = u.id
LEFT JOIN affaire a ON a.id_utilisateur = u.id
GROUP BY u.id, u.pseudo, u.date_creation
ORDER BY score_reputation DESC, nb_votes DESC, u.date_creation ASC;


-- =====================================================================
--  COMPTE DE SERVICE (développement local — à adapter en production)
-- =====================================================================
-- CREATE USER IF NOT EXISTS 'tribunal'@'localhost' IDENTIFIED BY 'change_moi';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON tribunal.* TO 'tribunal'@'localhost';
-- FLUSH PRIVILEGES;
