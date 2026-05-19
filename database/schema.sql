-- -- phpMyAdmin SQL Dump
-- -- version 5.2.1
-- -- https://www.phpmyadmin.net/
-- --
-- -- Hôte : localhost
-- -- Généré le : mar. 19 mai 2026 à 13:54
-- -- Version du serveur : 10.4.28-MariaDB
-- -- Version de PHP : 8.2.4

-- SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
-- START TRANSACTION;
-- SET time_zone = "+00:00";


-- /*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
-- /*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
-- /*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
-- /*!40101 SET NAMES utf8mb4 */;

-- --
-- -- Base de données : `gestion_notes`
-- --

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `classes`
-- --

-- CREATE TABLE `classes` (
--   `id` int(11) NOT NULL,
--   `nom` varchar(50) NOT NULL,
--   `niveau` varchar(50) NOT NULL,
--   `filiere` varchar(50) NOT NULL,
--   `annee_scolaire` varchar(20) NOT NULL,
--   `description` text DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `classes`
-- --

-- INSERT INTO `classes` (`id`, `nom`, `niveau`, `filiere`, `annee_scolaire`, `description`) VALUES
-- (1, 'L1-INFO', 'Licence 1', 'Informatique', '2025-2026', 'Première année Informatique'),
-- (2, 'L2-INFO', 'Licence 2', 'Informatique', '2025-2026', 'Deuxième année Informatique'),
-- (3, 'L3-INFO', 'Licence 3', 'Informatique', '2025-2026', 'Troisième année Informatique'),
-- (4, 'L1-MATH', 'Licence 1', 'Mathématiques', '2025-2026', 'Première année Mathématiques'),
-- (5, 'L2-MATH', 'Licence 2', 'Mathématiques', '2025-2026', 'Deuxième année Mathématiques'),
-- (6, 'L3-MATH', 'Licence 3', 'Mathématiques', '2025-2026', 'Troisième année Mathématiques');

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `enseignants`
-- --

-- CREATE TABLE `enseignants` (
--   `id` int(11) NOT NULL,
--   `utilisateur_id` int(11) DEFAULT NULL,
--   `matricule` varchar(30) NOT NULL,
--   `nom` varchar(100) NOT NULL,
--   `prenom` varchar(100) NOT NULL,
--   `specialite` varchar(100) DEFAULT NULL,
--   `telephone` varchar(20) DEFAULT NULL,
--   `email_pro` varchar(150) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `enseignants`
-- --

-- INSERT INTO `enseignants` (`id`, `utilisateur_id`, `matricule`, `nom`, `prenom`, `specialite`, `telephone`, `email_pro`) VALUES
-- (1, 3, 'ENS001', 'HOUNKPATIN', 'Marc', 'Mathématiques', '+229 97 11 22 33', 'm.hounkpatin@institut.bj'),
-- (2, 4, 'ENS002', 'AGOSSOU', 'Bernadette', 'Algorithmique', '+229 96 44 55 66', 'b.agossou@institut.bj'),
-- (3, 5, 'ENS003', 'HOUNKPE', 'Alain', 'Base de données', '+229 95 55 77 88', 'a.hounkpe@institut.bj'),
-- (4, 6, 'ENS004', 'SALIFOU', 'Rachid', 'Réseaux', '+229 94 33 22 11', 'r.salifou@institut.bj'),
-- (5, 7, 'ENS005', 'ADJOVI', 'Jocelyn', 'Intelligence Artificielle', '+229 93 11 44 55', 'j.adjovi@institut.bj'),
-- (6, 8, 'ENS006', 'BOKO', 'Séraphine', 'Analyse Mathématique', '+229 92 66 77 88', 's.boko@institut.bj'),
-- (7, 9, 'ENS007', 'TOGNIDE', 'Franck', 'Développement Web', '+229 91 88 99 00', 'f.tognide@institut.bj');

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `etudiants`
-- --

-- CREATE TABLE `etudiants` (
--   `id` int(11) NOT NULL,
--   `utilisateur_id` int(11) DEFAULT NULL,
--   `matricule` varchar(30) NOT NULL,
--   `nom` varchar(100) NOT NULL,
--   `prenom` varchar(100) NOT NULL,
--   `date_naissance` date DEFAULT NULL,
--   `lieu_naissance` varchar(100) DEFAULT NULL,
--   `sexe` enum('M','F') DEFAULT 'M',
--   `classe_id` int(11) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `etudiants`
-- --

-- INSERT INTO `etudiants` (`id`, `utilisateur_id`, `matricule`, `nom`, `prenom`, `date_naissance`, `lieu_naissance`, `sexe`, `classe_id`) VALUES
-- (1, 310, 'INF-L1-001', 'ADJOVI', 'Kevin', '2003-04-12', 'Cotonou', 'M', 1),
-- (2, 311, 'INF-L1-002', 'AHOLOU', 'Mariette', '2003-08-25', 'Porto-Novo', 'F', 1),
-- (3, 312, 'INF-L1-003', 'GBAGUIDI', 'Rodrigue', '2003-01-30', 'Abomey', 'M', 1),
-- (4, 313, 'INF-L1-004', 'ZINSOU', 'Léontine', '2003-11-14', 'Ouidah', 'F', 1),
-- (5, 314, 'INF-L1-005', 'TOSSOU', 'Émilien', '2003-06-19', 'Parakou', 'M', 1),
-- (6, 315, 'INF-L1-006', 'KPADONOU', 'Sandrine', '2003-02-08', 'Bohicon', 'F', 1),
-- (7, 316, 'INF-L1-007', 'HOUEDANOU', 'Olivier', '2003-09-22', 'Natitingou', 'M', 1),
-- (8, 317, 'INF-L1-008', 'AGBODJAN', 'Florine', '2003-12-05', 'Lokossa', 'F', 1),
-- (9, 318, 'INF-L1-009', 'SOSSOU', 'Wilfried', '2003-03-17', 'Cotonou', 'M', 1),
-- (10, 319, 'INF-L1-010', 'AKPOVI', 'Carmen', '2003-07-28', 'Calavi', 'F', 1),
-- (11, 320, 'INF-L2-001', 'ATTIOGBE', 'Komlan', '2002-04-15', 'Lomé', 'M', 2),
-- (12, 321, 'INF-L2-002', 'ADJANOHOUN', 'Jules', '2002-09-10', 'Cotonou', 'M', 2),
-- (13, 322, 'INF-L2-003', 'HOUNDJAGBA', 'Eloi', '2002-11-20', 'Porto-Novo', 'M', 2),
-- (14, 323, 'INF-L2-004', 'HOUNTONDJI', 'Marcel', '2002-05-05', 'Abomey', 'M', 2),
-- (15, 324, 'INF-L2-005', 'KPOSSOU', 'Gildas', '2002-08-18', 'Parakou', 'M', 2),
-- (16, 325, 'INF-L2-006', 'AGBOTON', 'Sébastien', '2002-12-01', 'Cotonou', 'M', 2),
-- (17, 326, 'INF-L2-007', 'AKAKPO', 'Anatole', '2002-03-22', 'Ouidah', 'M', 2),
-- (18, 327, 'INF-L2-008', 'DEGBEY', 'Mireille', '2002-07-30', 'Bohicon', 'F', 2),
-- (19, 328, 'INF-L2-009', 'FAGLA', 'David', '2002-10-14', 'Natitingou', 'M', 2),
-- (20, 329, 'INF-L2-010', 'GLELE', 'Ruth', '2002-01-25', 'Lokossa', 'F', 2),
-- (21, 330, 'INF-L3-001', 'HOUNSOUNOU', 'Gabin', '2001-06-09', 'Calavi', 'M', 3),
-- (22, 331, 'INF-L3-002', 'IDOHOU', 'Ange', '2001-11-17', 'Cotonou', 'F', 3),
-- (23, 332, 'INF-L3-003', 'JANVIER', 'Bertin', '2001-04-28', 'Porto-Novo', 'M', 3),
-- (24, 333, 'INF-L3-004', 'KOUAGOU', 'Nadia', '2001-09-02', 'Abomey', 'F', 3),
-- (25, 334, 'INF-L3-005', 'LOKO', 'Blaise', '2001-12-12', 'Parakou', 'M', 3),
-- (26, 335, 'INF-L3-006', 'METOGO', 'Israël', '2001-05-18', 'Cotonou', 'M', 3),
-- (27, 336, 'INF-L3-007', 'NOUDJIHOUDJI', 'Fabrice', '2001-10-22', 'Porto-Novo', 'M', 3),
-- (28, 337, 'INF-L3-008', 'OGOUBIYI', 'Sonia', '2001-03-08', 'Abomey', 'F', 3),
-- (29, 338, 'INF-L3-009', 'OSSAH', 'Géraud', '2001-07-15', 'Parakou', 'M', 3),
-- (30, 339, 'INF-L3-010', 'POUSSOU', 'Alphonse', '2001-11-30', 'Ouidah', 'M', 3),
-- (31, 340, 'MATH-L1-001', 'SEGBEDJI', 'Romain', '2003-02-25', 'Bohicon', 'M', 4),
-- (32, 341, 'MATH-L1-002', 'TCHEDRE', 'Victoire', '2003-08-19', 'Natitingou', 'F', 4),
-- (33, 342, 'MATH-L1-003', 'TOGNIDE', 'Franck', '2003-12-04', 'Lokossa', 'M', 4),
-- (34, 343, 'MATH-L1-004', 'TONATO', 'Muriel', '2003-04-09', 'Calavi', 'F', 4),
-- (35, 344, 'MATH-L1-005', 'TOUGOUMA', 'Jules', '2003-09-14', 'Cotonou', 'M', 4),
-- (36, 345, 'MATH-L1-006', 'VIGNON', 'Alain', '2003-01-27', 'Porto-Novo', 'M', 4),
-- (37, 346, 'MATH-L1-007', 'YABI', 'Bertrand', '2003-06-12', 'Abomey', 'M', 4),
-- (38, 347, 'MATH-L1-008', 'ZOHOUN', 'Aimé', '2003-10-03', 'Parakou', 'M', 4),
-- (39, 348, 'MATH-L1-009', 'AVLESSI', 'Régina', '2003-03-21', 'Ouidah', 'F', 4),
-- (40, 349, 'MATH-L1-010', 'BIAOU', 'Gabin', '2003-07-08', 'Bohicon', 'M', 4),
-- (41, 350, 'MATH-L2-001', 'ADAM', 'Fatima', '2002-05-12', 'Cotonou', 'F', 5),
-- (42, 351, 'MATH-L2-002', 'AMOUSSOU', 'Eric', '2002-09-25', 'Porto-Novo', 'M', 5),
-- (43, 352, 'MATH-L2-003', 'BANON', 'Judith', '2002-02-18', 'Abomey', 'F', 5),
-- (44, 353, 'MATH-L2-004', 'CHABI', 'Moussa', '2002-10-30', 'Parakou', 'M', 5),
-- (45, 354, 'MATH-L2-005', 'DEDJINOU', 'Célestin', '2002-01-05', 'Ouidah', 'M', 5),
-- (46, 355, 'MATH-L2-006', 'GBEDO', 'Prisca', '2002-06-14', 'Bohicon', 'F', 5),
-- (47, 356, 'MATH-L2-007', 'HOUENOU', 'Léon', '2002-08-22', 'Natitingou', 'M', 5),
-- (48, 357, 'MATH-L2-008', 'KPATCHAVI', 'Edwige', '2002-12-09', 'Lokossa', 'F', 5),
-- (49, 358, 'MATH-L2-009', 'LAOUROU', 'Afissatou', '2002-04-17', 'Calavi', 'F', 5),
-- (50, 359, 'MATH-L2-010', 'MAMA', 'Soumaila', '2002-11-26', 'Cotonou', 'M', 5),
-- (51, 360, 'MATH-L3-001', 'NIKIEMA', 'Aminata', '2001-07-19', 'Cotonou', 'F', 6),
-- (52, 361, 'MATH-L3-002', 'OGOUDJOU', 'Maurice', '2001-12-03', 'Porto-Novo', 'M', 6),
-- (53, 362, 'MATH-L3-003', 'OSSE', 'Gisèle', '2001-05-28', 'Abomey', 'F', 6),
-- (54, 363, 'MATH-L3-004', 'SALAMI', 'Rachidatou', '2001-09-11', 'Parakou', 'F', 6),
-- (55, 364, 'MATH-L3-005', 'SOMASSOU', 'Matthieu', '2001-02-14', 'Ouidah', 'M', 6),
-- (56, 365, 'MATH-L3-006', 'TCHEDJI', 'Lucien', '2001-08-27', 'Bohicon', 'M', 6),
-- (57, NULL, 'MATH-L3-007', 'VIHO', 'Sarah', '2001-01-09', 'Natitingou', 'F', 6),
-- (58, 367, 'MATH-L3-008', 'YAKOUBOU', 'Abdou', '2001-06-23', 'Lokossa', 'M', 6),
-- (59, 368, 'MATH-L3-009', 'ZAKARI', 'Ramatou', '2001-10-16', 'Calavi', 'F', 6),
-- (60, 369, 'MATH-L3-010', 'AKOBI', 'Isidore', '2001-04-04', 'Cotonou', 'M', 6);

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `matieres`
-- --

-- CREATE TABLE `matieres` (
--   `id` int(11) NOT NULL,
--   `code` varchar(20) NOT NULL,
--   `nom` varchar(100) NOT NULL,
--   `coefficient` int(11) DEFAULT 1,
--   `credits` int(11) DEFAULT 3,
--   `semestre` enum('S1','S2','S3','S4','S5','S6') NOT NULL,
--   `heures_cours` int(11) DEFAULT 0,
--   `heures_td` int(11) DEFAULT 0,
--   `heures_tp` int(11) DEFAULT 0,
--   `enseignant_id` int(11) DEFAULT NULL,
--   `classe_id` int(11) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `matieres`
-- --

-- INSERT INTO `matieres` (`id`, `code`, `nom`, `coefficient`, `credits`, `semestre`, `heures_cours`, `heures_td`, `heures_tp`, `enseignant_id`, `classe_id`) VALUES
-- (1, 'INF-S1-101', 'Algorithmique', 4, 6, 'S1', 0, 0, 0, 2, 1),
-- (2, 'INF-S1-102', 'Programmation C/Python', 4, 6, 'S1', 0, 0, 0, 2, 1),
-- (3, 'INF-S1-103', 'Architecture Ordinateurs', 3, 5, 'S1', 0, 0, 0, 4, 1),
-- (4, 'INF-S1-104', 'Mathématiques', 3, 5, 'S1', 0, 0, 0, 1, 1),
-- (5, 'INF-S1-105', 'Bases de Données', 3, 4, 'S1', 0, 0, 0, 3, 1),
-- (6, 'INF-S2-201', 'Programmation Orientée Objet', 4, 6, 'S2', 0, 0, 0, 2, 1),
-- (7, 'INF-S2-202', 'Développement Web', 3, 5, 'S2', 0, 0, 0, 7, 1),
-- (8, 'INF-S2-203', 'SQL Avancé', 3, 5, 'S2', 0, 0, 0, 3, 1),
-- (9, 'INF-S2-204', 'Réseaux', 3, 4, 'S2', 0, 0, 0, 4, 1),
-- (10, 'INF-S3-301', 'Algorithmique Avancée', 4, 6, 'S3', 0, 0, 0, 2, 2),
-- (11, 'INF-S3-302', 'PHP/MySQL', 4, 6, 'S3', 0, 0, 0, 7, 2),
-- (12, 'INF-S3-303', 'Programmation Système', 3, 5, 'S3', 0, 0, 0, 4, 2),
-- (13, 'INF-S3-304', 'Génie Logiciel', 3, 4, 'S3', 0, 0, 0, 2, 2),
-- (14, 'INF-S4-401', 'Programmation Mobile', 4, 6, 'S4', 0, 0, 0, 7, 2),
-- (15, 'INF-S4-402', 'NoSQL', 3, 5, 'S4', 0, 0, 0, 3, 2),
-- (16, 'INF-S4-403', 'Sécurité Informatique', 3, 5, 'S4', 0, 0, 0, 4, 2),
-- (17, 'INF-S5-501', 'Intelligence Artificielle', 4, 6, 'S5', 0, 0, 0, 5, 3),
-- (18, 'INF-S5-502', 'Machine Learning', 4, 6, 'S5', 0, 0, 0, 5, 3),
-- (19, 'INF-S5-503', 'Cloud Computing', 3, 5, 'S5', 0, 0, 0, 4, 3),
-- (20, 'INF-S5-504', 'Full-Stack', 4, 6, 'S5', 0, 0, 0, 7, 3),
-- (21, 'INF-S6-601', 'Projet Fin d\'Études', 6, 10, 'S6', 0, 0, 0, 2, 3),
-- (22, 'INF-S6-602', 'Stage', 8, 15, 'S6', 0, 0, 0, 3, 3),
-- (23, 'MATH-S1-101', 'Analyse 1', 5, 8, 'S1', 0, 0, 0, 6, 4),
-- (24, 'MATH-S1-102', 'Algèbre 1', 5, 8, 'S1', 0, 0, 0, 1, 4),
-- (25, 'MATH-S1-103', 'Géométrie', 3, 5, 'S1', 0, 0, 0, 1, 4),
-- (26, 'MATH-S1-104', 'Probabilités', 3, 4, 'S1', 0, 0, 0, 6, 4),
-- (27, 'MATH-S2-201', 'Analyse 2', 5, 8, 'S2', 0, 0, 0, 6, 4),
-- (28, 'MATH-S2-202', 'Algèbre 2', 5, 8, 'S2', 0, 0, 0, 1, 4),
-- (29, 'MATH-S2-203', 'Statistiques', 3, 5, 'S2', 0, 0, 0, 6, 4),
-- (30, 'MATH-S3-301', 'Analyse 3', 5, 8, 'S3', 0, 0, 0, 6, 5),
-- (31, 'MATH-S3-302', 'Algèbre 3', 5, 8, 'S3', 0, 0, 0, 1, 5),
-- (32, 'MATH-S3-303', 'Équations Différentielles', 4, 6, 'S3', 0, 0, 0, 6, 5),
-- (33, 'MATH-S4-401', 'Analyse 4', 5, 8, 'S4', 0, 0, 0, 6, 5),
-- (34, 'MATH-S4-402', 'Algèbre 4', 4, 6, 'S4', 0, 0, 0, 1, 5),
-- (35, 'MATH-S4-403', 'Optimisation', 4, 6, 'S4', 0, 0, 0, 1, 5),
-- (36, 'MATH-S5-501', 'Analyse Fonctionnelle', 5, 8, 'S5', 0, 0, 0, 6, 6),
-- (37, 'MATH-S5-502', 'Algèbre 5', 4, 6, 'S5', 0, 0, 0, 1, 6),
-- (38, 'MATH-S5-503', 'Statistique Mathématique', 4, 6, 'S5', 0, 0, 0, 6, 6),
-- (39, 'MATH-S6-601', 'Mémoire', 6, 12, 'S6', 0, 0, 0, 6, 6),
-- (40, 'MATH-S6-602', 'Stage', 6, 12, 'S6', 0, 0, 0, 3, 6);

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `notes`
-- --

-- CREATE TABLE `notes` (
--   `id` int(11) NOT NULL,
--   `etudiant_id` int(11) NOT NULL,
--   `matiere_id` int(11) NOT NULL,
--   `note_interro` int(11) DEFAULT 0,
--   `note_devoir` int(11) DEFAULT 0,
--   `note_examen` int(11) DEFAULT 0,
--   `moyenne` decimal(5,2) GENERATED ALWAYS AS (`note_interro` * 0.3 + `note_devoir` * 0.7) STORED,
--   `semestre` enum('S1','S2','S3','S4','S5','S6') NOT NULL,
--   `annee_scolaire` varchar(20) DEFAULT '2024-2025',
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
--   `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
--   `validee` tinyint(1) NOT NULL DEFAULT 0,
--   `validated_at` timestamp NULL DEFAULT NULL,
--   `validated_by` int(11) DEFAULT NULL
-- ) ;

-- --
-- -- Déchargement des données de la table `notes`
-- --

-- INSERT INTO `notes` (`id`, `etudiant_id`, `matiere_id`, `note_interro`, `note_devoir`, `note_examen`, `semestre`, `annee_scolaire`, `created_at`, `updated_at`, `validee`, `validated_at`, `validated_by`) VALUES
-- (2, 39, 24, 10, 11, 0, 'S1', '2025-2026', '2026-01-15 09:00:00', '2026-05-19 11:00:00', 1, '2026-05-19 10:02:40', 2),
-- (3, 39, 28, 12, 15, 0, 'S1', '2025-2026', '2026-01-15 09:00:00', '2026-05-19 11:00:00', 1, '2026-05-19 10:02:40', 2),
-- (4, 39, 25, 15, 19, 0, 'S1', '2025-2026', '2026-01-15 09:00:00', '2026-05-19 11:00:00', 1, '2026-05-19 10:02:40', 2),
-- (5, 39, 23, 12, 16, 0, 'S1', '2024-2025', '2026-05-19 11:02:41', '2026-05-19 11:04:28', 1, '2026-05-19 11:04:28', 2),
-- (6, 39, 27, 20, 11, 0, 'S1', '2024-2025', '2026-05-19 11:02:54', '2026-05-19 11:04:28', 1, '2026-05-19 11:04:28', 2),
-- (7, 39, 26, 11, 5, 0, 'S1', '2024-2025', '2026-05-19 11:03:11', '2026-05-19 11:04:28', 1, '2026-05-19 11:04:28', 2),
-- (8, 39, 29, 12, 16, 0, 'S1', '2024-2025', '2026-05-19 11:03:24', '2026-05-19 11:04:28', 1, '2026-05-19 11:04:28', 2),
-- (9, 40, 24, 12, 11, 0, 'S1', '2024-2025', '2026-05-19 11:42:12', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (10, 40, 28, 10, 15, 0, 'S1', '2024-2025', '2026-05-19 11:42:25', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (11, 40, 25, 16, 19, 0, 'S1', '2024-2025', '2026-05-19 11:42:40', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (12, 40, 23, 15, 19, 0, 'S1', '2024-2025', '2026-05-19 11:43:27', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (13, 40, 27, 14, 16, 0, 'S1', '2024-2025', '2026-05-19 11:43:38', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (14, 40, 26, 12, 14, 0, 'S1', '2024-2025', '2026-05-19 11:43:50', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2),
-- (15, 40, 29, 12, 15, 0, 'S1', '2024-2025', '2026-05-19 11:43:59', '2026-05-19 11:45:01', 1, '2026-05-19 11:45:01', 2);

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `notes_historique`
-- --

-- CREATE TABLE `notes_historique` (
--   `id` int(11) NOT NULL,
--   `note_id` int(11) DEFAULT NULL,
--   `etudiant_id` int(11) NOT NULL,
--   `matiere_id` int(11) NOT NULL,
--   `note_interro` int(11) DEFAULT 0,
--   `note_devoir` int(11) DEFAULT 0,
--   `note_examen` int(11) DEFAULT 0,
--   `moyenne` int(11) DEFAULT 0,
--   `semestre` varchar(5) NOT NULL,
--   `annee_scolaire` varchar(20) DEFAULT '2024-2025',
--   `action` enum('validation','modification','suppression') NOT NULL DEFAULT 'validation',
--   `validated_by` int(11) DEFAULT NULL,
--   `validated_at` timestamp NOT NULL DEFAULT current_timestamp(),
--   `commentaire` varchar(255) DEFAULT NULL
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `notes_historique`
-- --

-- INSERT INTO `notes_historique` (`id`, `note_id`, `etudiant_id`, `matiere_id`, `note_interro`, `note_devoir`, `note_examen`, `moyenne`, `semestre`, `annee_scolaire`, `action`, `validated_by`, `validated_at`, `commentaire`) VALUES
-- (1, 2, 39, 24, 10, 11, 0, 5, 'S1', '2025-2026', 'validation', 2, '2026-05-19 11:00:00', NULL),
-- (2, 3, 39, 28, 12, 15, 0, 7, 'S1', '2025-2026', 'validation', 2, '2026-05-19 11:00:00', NULL),
-- (3, 4, 39, 25, 15, 19, 0, 9, 'S1', '2025-2026', 'validation', 2, '2026-05-19 11:00:00', NULL),
-- (4, 5, 39, 23, 12, 16, 0, 15, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:04:28', NULL),
-- (5, 6, 39, 27, 20, 11, 0, 14, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:04:28', NULL),
-- (6, 7, 39, 26, 11, 5, 0, 7, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:04:28', NULL),
-- (7, 8, 39, 29, 12, 16, 0, 15, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:04:28', NULL),
-- (8, 9, 40, 24, 12, 11, 0, 11, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (9, 10, 40, 28, 10, 15, 0, 14, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (10, 11, 40, 25, 16, 19, 0, 18, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (11, 12, 40, 23, 15, 19, 0, 18, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (12, 13, 40, 27, 14, 16, 0, 15, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (13, 14, 40, 26, 12, 14, 0, 13, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL),
-- (14, 15, 40, 29, 12, 15, 0, 14, 'S1', '2024-2025', 'validation', 2, '2026-05-19 11:45:01', NULL);

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `password_resets`
-- --

-- CREATE TABLE `password_resets` (
--   `id` int(11) NOT NULL,
--   `email` varchar(150) NOT NULL,
--   `token` varchar(100) NOT NULL,
--   `expire_at` datetime NOT NULL,
--   `used` tinyint(1) DEFAULT 0,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `password_resets`
-- --

-- INSERT INTO `password_resets` (`id`, `email`, `token`, `expire_at`, `used`, `created_at`) VALUES
-- (1, 'kevin.adjovi@institut.bj', '676f78736c0478d04a0286e87eb703f89dc6cade78fe75a0e9dd4b7ba49ad376', '2026-05-19 13:19:35', 1, '2026-05-19 09:19:35');

-- -- --------------------------------------------------------

-- --
-- -- Structure de la table `utilisateurs`
-- --

-- CREATE TABLE `utilisateurs` (
--   `id` int(11) NOT NULL,
--   `nom` varchar(100) NOT NULL,
--   `prenom` varchar(100) NOT NULL,
--   `email` varchar(150) NOT NULL,
--   `mot_de_passe` varchar(255) NOT NULL,
--   `role` enum('admin','enseignant','etudiant','responsable') NOT NULL,
--   `actif` tinyint(1) DEFAULT 1,
--   `created_at` timestamp NOT NULL DEFAULT current_timestamp()
-- ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --
-- -- Déchargement des données de la table `utilisateurs`
-- --

-- INSERT INTO `utilisateurs` (`id`, `nom`, `prenom`, `email`, `mot_de_passe`, `role`, `actif`, `created_at`) VALUES
-- (1, 'SYSTEME', 'Admin', 'admin@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'admin', 1, '2026-01-01 08:00:00'),
-- (2, 'DOSSOU', 'Cyrille', 'c.dossou@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'responsable', 1, '2026-01-01 08:00:00'),
-- (3, 'HOUNKPATIN', 'Marc', 'marc.hounkpatin@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (4, 'AGOSSOU', 'Bernadette', 'b.agossou@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (5, 'HOUNKPE', 'Alain', 'a.hounkpe@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (6, 'SALIFOU', 'Rachid', 'r.salifou@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (7, 'ADJOVI', 'Jocelyn', 'j.adjovi@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (8, 'BOKO', 'Séraphine', 's.boko@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (9, 'TOGNIDE', 'Franck', 'f.tognide@institut.bj', '$2y$10$wH8QOyEKJv3gXl1TJQ4z3eYxGVvK5gO9aD0rZQ0nYhE3lXFm6sT8O', 'enseignant', 1, '2026-01-01 08:00:00'),
-- (310, 'ADJOVI', 'Kevin', 'kevin.adjovi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (311, 'AHOLOU', 'Mariette', 'mariette.aholou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (312, 'GBAGUIDI', 'Rodrigue', 'rodrigue.gbaguidi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (313, 'ZINSOU', 'Léontine', 'leontine.zinsou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (314, 'TOSSOU', 'Émilien', 'emilien.tossou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (315, 'KPADONOU', 'Sandrine', 'sandrine.kpadonou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (316, 'HOUEDANOU', 'Olivier', 'olivier.houedanou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (317, 'AGBODJAN', 'Florine', 'florine.agbodjan@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (318, 'SOSSOU', 'Wilfried', 'wilfried.sossou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (319, 'AKPOVI', 'Carmen', 'carmen.akpovi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (320, 'ATTIOGBE', 'Komlan', 'komlan.attiogbe@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (321, 'ADJANOHOUN', 'Jules', 'jules.adjanohoun@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (322, 'HOUNDJAGBA', 'Eloi', 'eloi.houndjagba@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (323, 'HOUNTONDJI', 'Marcel', 'marcel.hountondji@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (324, 'KPOSSOU', 'Gildas', 'gildas.kpossou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (325, 'AGBOTON', 'Sébastien', 'sebastien.agboton@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (326, 'AKAKPO', 'Anatole', 'anatole.akakpo@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (327, 'DEGBEY', 'Mireille', 'mireille.degbey@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (328, 'FAGLA', 'David', 'david.fagla@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (329, 'GLELE', 'Ruth', 'ruth.glele@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (330, 'HOUNSOUNOU', 'Gabin', 'gabin.hounsounou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (331, 'IDOHOU', 'Ange', 'ange.idohou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (332, 'JANVIER', 'Bertin', 'bertin.janvier@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (333, 'KOUAGOU', 'Nadia', 'nadia.kouagou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (334, 'LOKO', 'Blaise', 'blaise.loko@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (335, 'METOGO', 'Israël', 'israel.metogo@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (336, 'NOUDJIHOUDJI', 'Fabrice', 'fabrice.noudjihoudji@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (337, 'OGOUBIYI', 'Sonia', 'sonia.ogoubiyi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (338, 'OSSAH', 'Géraud', 'geraud.ossah@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (339, 'POUSSOU', 'Alphonse', 'alphonse.poussou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (340, 'SEGBEDJI', 'Romain', 'romain.segbedji@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (341, 'TCHEDRE', 'Victoire', 'victoire.tchedre@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (342, 'TOGNIDE', 'Franck', 'franck.tognide@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (343, 'TONATO', 'Muriel', 'muriel.tonato@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (344, 'TOUGOUMA', 'Jules', 'jules.tougouma@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (345, 'VIGNON', 'Alain', 'alain.vignon@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (346, 'YABI', 'Bertrand', 'bertrand.yabi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (347, 'ZOHOUN', 'Aimé', 'aime.zohoun@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (348, 'AVLESSI', 'Régina', 'regina.avlessi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (349, 'BIAOU', 'Gabin', 'gabin.biaou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (350, 'ADAM', 'Fatima', 'fatima.adam@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (351, 'AMOUSSOU', 'Eric', 'eric.amoussou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (352, 'BANON', 'Judith', 'judith.banon@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (353, 'CHABI', 'Moussa', 'moussa.chabi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (354, 'DEDJINOU', 'Célestin', 'celestin.dedjinou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (355, 'GBEDO', 'Prisca', 'prisca.gbedo@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (356, 'HOUENOU', 'Léon', 'leon.houenou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (357, 'KPATCHAVI', 'Edwige', 'edwige.kpatchavi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (358, 'LAOUROU', 'Afissatou', 'afissatou.laourou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (359, 'MAMA', 'Soumaila', 'soumaila.mama@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (360, 'NIKIEMA', 'Aminata', 'aminata.nikiema@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (361, 'OGOUDJOU', 'Maurice', 'maurice.ogoudjou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (362, 'OSSE', 'Gisèle', 'gisele.osse@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (363, 'SALAMI', 'Rachidatou', 'rachidatou.salami@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (364, 'SOMASSOU', 'Matthieu', 'matthieu.somassou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (365, 'TCHEDJI', 'Lucien', 'lucien.tchedji@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (366, 'VIHO', 'Sarah', 'sarah.vihou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (367, 'YAKOUBOU', 'Abdou', 'abdou.yakoubou@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (368, 'ZAKARI', 'Ramatou', 'ramatou.zakari@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00'),
-- (369, 'AKOBI', 'Isidore', 'isidore.akobi@institut.bj', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'etudiant', 1, '2026-01-15 09:00:00');

-- --
-- -- Index pour les tables déchargées
-- --

-- --
-- -- Index pour la table `classes`
-- --
-- ALTER TABLE `classes`
--   ADD PRIMARY KEY (`id`);

-- --
-- -- Index pour la table `enseignants`
-- --
-- ALTER TABLE `enseignants`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `matricule` (`matricule`),
--   ADD UNIQUE KEY `utilisateur_id` (`utilisateur_id`);

-- --
-- -- Index pour la table `etudiants`
-- --
-- ALTER TABLE `etudiants`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `matricule` (`matricule`),
--   ADD UNIQUE KEY `utilisateur_id` (`utilisateur_id`),
--   ADD KEY `idx_etudiants_classe` (`classe_id`);

-- --
-- -- Index pour la table `matieres`
-- --
-- ALTER TABLE `matieres`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `code` (`code`),
--   ADD KEY `enseignant_id` (`enseignant_id`),
--   ADD KEY `idx_matieres_classe` (`classe_id`);

-- --
-- -- Index pour la table `notes`
-- --
-- ALTER TABLE `notes`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `uk_note` (`etudiant_id`,`matiere_id`,`semestre`,`annee_scolaire`),
--   ADD KEY `idx_notes_etudiant` (`etudiant_id`),
--   ADD KEY `idx_notes_matiere` (`matiere_id`);

-- --
-- -- Index pour la table `notes_historique`
-- --
-- ALTER TABLE `notes_historique`
--   ADD PRIMARY KEY (`id`),
--   ADD KEY `idx_etudiant` (`etudiant_id`),
--   ADD KEY `idx_matiere` (`matiere_id`),
--   ADD KEY `idx_date` (`validated_at`);

-- --
-- -- Index pour la table `password_resets`
-- --
-- ALTER TABLE `password_resets`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `token` (`token`),
--   ADD KEY `idx_token` (`token`),
--   ADD KEY `idx_email` (`email`);

-- --
-- -- Index pour la table `utilisateurs`
-- --
-- ALTER TABLE `utilisateurs`
--   ADD PRIMARY KEY (`id`),
--   ADD UNIQUE KEY `email` (`email`);

-- --
-- -- AUTO_INCREMENT pour les tables déchargées
-- --

-- --
-- -- AUTO_INCREMENT pour la table `classes`
-- --
-- ALTER TABLE `classes`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

-- --
-- -- AUTO_INCREMENT pour la table `enseignants`
-- --
-- ALTER TABLE `enseignants`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

-- --
-- -- AUTO_INCREMENT pour la table `etudiants`
-- --
-- ALTER TABLE `etudiants`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=61;

-- --
-- -- AUTO_INCREMENT pour la table `matieres`
-- --
-- ALTER TABLE `matieres`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

-- --
-- -- AUTO_INCREMENT pour la table `notes`
-- --
-- ALTER TABLE `notes`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

-- --
-- -- AUTO_INCREMENT pour la table `notes_historique`
-- --
-- ALTER TABLE `notes_historique`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

-- --
-- -- AUTO_INCREMENT pour la table `password_resets`
-- --
-- ALTER TABLE `password_resets`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

-- --
-- -- AUTO_INCREMENT pour la table `utilisateurs`
-- --
-- ALTER TABLE `utilisateurs`
--   MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=370;

-- --
-- -- Contraintes pour les tables déchargées
-- --

-- --
-- -- Contraintes pour la table `enseignants`
-- --
-- ALTER TABLE `enseignants`
--   ADD CONSTRAINT `enseignants_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL;

-- --
-- -- Contraintes pour la table `etudiants`
-- --
-- ALTER TABLE `etudiants`
--   ADD CONSTRAINT `etudiants_ibfk_1` FOREIGN KEY (`utilisateur_id`) REFERENCES `utilisateurs` (`id`) ON DELETE SET NULL,
--   ADD CONSTRAINT `etudiants_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE SET NULL;

-- --
-- -- Contraintes pour la table `matieres`
-- --
-- ALTER TABLE `matieres`
--   ADD CONSTRAINT `matieres_ibfk_1` FOREIGN KEY (`enseignant_id`) REFERENCES `enseignants` (`id`) ON DELETE SET NULL,
--   ADD CONSTRAINT `matieres_ibfk_2` FOREIGN KEY (`classe_id`) REFERENCES `classes` (`id`) ON DELETE CASCADE;

-- --
-- -- Contraintes pour la table `notes`
-- --
-- ALTER TABLE `notes`
--   ADD CONSTRAINT `notes_ibfk_1` FOREIGN KEY (`etudiant_id`) REFERENCES `etudiants` (`id`) ON DELETE CASCADE,
--   ADD CONSTRAINT `notes_ibfk_2` FOREIGN KEY (`matiere_id`) REFERENCES `matieres` (`id`) ON DELETE CASCADE;
-- COMMIT;

-- /*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
-- /*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
-- /*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;