-- =====================================================================
--  CORRECTIF — rend les vues utilisables chez un hebergeur
--
--  A QUOI CA SERT
--  Les pages « classement », « profil » et l'affichage du taux de vote
--  lisent deux vues SQL. Une vue MySQL retient le compte qui l'a creee
--  (son DEFINER) et, par defaut, s'execute avec LES DROITS DE CE COMPTE.
--
--  Quand le schema est importe chez un hebergeur, ce compte n'existe pas
--  sur place : chaque lecture de la vue est alors refusee avec
--      « Access denied for user '...'@'localhost' »
--  alors que les identifiants de connexion sont parfaitement corrects.
--  C'est pour cela que les autres pages, qui ne lisent que des tables,
--  fonctionnent normalement.
--
--  La mention « SQL SECURITY INVOKER » ci-dessous corrige le probleme :
--  la vue s'execute desormais avec les droits du compte qui l'interroge.
--
--  MODE D'EMPLOI
--  1. ouvrir phpMyAdmin sur le serveur
--  2. selectionner la base du site dans la colonne de gauche
--  3. onglet SQL, coller ce fichier entier, executer
--
--  Aucune donnee n'est touchee : on ne recree que deux vues.
-- =====================================================================


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
