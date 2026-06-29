-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le : lun. 29 juin 2026 à 10:02
-- Version du serveur :  5.7.31
-- Version de PHP : 7.3.21

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données : `immobilier_db`
--

-- --------------------------------------------------------

--
-- Structure de la table `annonces`
--

DROP TABLE IF EXISTS `annonces`;
CREATE TABLE IF NOT EXISTS `annonces` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `bailleur_id` int(11) NOT NULL,
  `titre` varchar(255) NOT NULL,
  `description` text,
  `type_bien` enum('location','vente','bureau','espace_vide') NOT NULL,
  `zone_geographique` varchar(200) NOT NULL,
  `prix` decimal(15,2) NOT NULL,
  `surface` int(11) DEFAULT NULL,
  `statut` enum('attente','publie','retire') DEFAULT 'attente',
  `nb_vues` int(11) DEFAULT '0',
  `date_publication` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `bailleur_id` (`bailleur_id`),
  KEY `idx_statut` (`statut`),
  KEY `idx_type_bien` (`type_bien`),
  KEY `idx_zone` (`zone_geographique`),
  KEY `idx_date_publication` (`date_publication`)
) ENGINE=MyISAM AUTO_INCREMENT=15 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `annonces`
--

INSERT INTO `annonces` (`id`, `bailleur_id`, `titre`, `description`, `type_bien`, `zone_geographique`, `prix`, `surface`, `statut`, `nb_vues`, `date_publication`, `created_at`, `updated_at`) VALUES
(13, 23, 'maison meubler', 'maison R+2', 'location', 'cissin', '3000000.00', 340, 'retire', 1, NULL, '2026-06-28 22:04:51', '2026-06-29 09:34:11'),
(12, 25, 'maison meubler', 'avec de bonnes meubles', 'vente', 'marcoussis', '1200000.00', NULL, 'publie', 3, '2026-06-28 21:29:54', '2026-06-28 21:28:10', '2026-06-29 09:46:31'),
(11, 25, 'Maison', 'Villa', 'vente', 'Ouagadougou', '12000000.00', 240, 'publie', 6, '2026-06-28 20:10:14', '2026-06-28 19:31:47', '2026-06-29 09:46:59'),
(10, 25, 'Chambre Salon Cours Unique', 'Cours unique de chambre salon', 'location', 'Ouagadougou', '35000.00', 120, 'publie', 7, '2026-06-28 20:10:19', '2026-06-28 18:45:56', '2026-06-29 09:30:20');

-- --------------------------------------------------------

--
-- Structure de la table `demandes_visite`
--

DROP TABLE IF EXISTS `demandes_visite`;
CREATE TABLE IF NOT EXISTS `demandes_visite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `date_visite` date DEFAULT NULL,
  `heure_visite` time DEFAULT NULL,
  `message` text,
  `statut` enum('attente','validee','refusee') DEFAULT 'attente',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_client` (`client_id`),
  KEY `idx_annonce` (`annonce_id`),
  KEY `idx_statut` (`statut`)
) ENGINE=MyISAM AUTO_INCREMENT=2 DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `favoris`
--

DROP TABLE IF EXISTS `favoris`;
CREATE TABLE IF NOT EXISTS `favoris` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `client_id` int(11) NOT NULL,
  `annonce_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_favori` (`client_id`,`annonce_id`),
  KEY `annonce_id` (`annonce_id`),
  KEY `idx_client` (`client_id`)
) ENGINE=MyISAM AUTO_INCREMENT=7 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `favoris`
--

INSERT INTO `favoris` (`id`, `client_id`, `annonce_id`, `created_at`) VALUES
(6, 24, 11, '2026-06-28 21:35:22'),
(5, 24, 12, '2026-06-28 21:32:41');

-- --------------------------------------------------------

--
-- Structure de la table `logs_activite`
--

DROP TABLE IF EXISTS `logs_activite`;
CREATE TABLE IF NOT EXISTS `logs_activite` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_action` (`action`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `notifications`
--

DROP TABLE IF EXISTS `notifications`;
CREATE TABLE IF NOT EXISTS `notifications` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(50) DEFAULT NULL,
  `est_lu` tinyint(1) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_user` (`user_id`),
  KEY `idx_lu` (`est_lu`)
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Structure de la table `photos_annonce`
--

DROP TABLE IF EXISTS `photos_annonce`;
CREATE TABLE IF NOT EXISTS `photos_annonce` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `annonce_id` int(11) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `is_principal` tinyint(1) DEFAULT '0',
  `ordre` int(11) DEFAULT '0',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_annonce` (`annonce_id`),
  KEY `idx_principal` (`is_principal`)
) ENGINE=MyISAM AUTO_INCREMENT=17 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `photos_annonce`
--

INSERT INTO `photos_annonce` (`id`, `annonce_id`, `photo_path`, `is_principal`, `ordre`, `created_at`) VALUES
(15, 13, '6a419a8301675_1782684291.jpg', 1, 0, '2026-06-28 22:04:51'),
(14, 12, '6a4191ea6f8b7_1782682090.jpg', 1, 0, '2026-06-28 21:28:10'),
(13, 11, '6a4176a3d04f9_1782675107.jpg', 0, 2, '2026-06-28 19:31:47'),
(12, 11, '6a4176a3cfbcd_1782675107.jpg', 0, 1, '2026-06-28 19:31:47'),
(11, 11, '6a4176a3cf2dd_1782675107.jpg', 1, 0, '2026-06-28 19:31:47'),
(10, 10, '6a416be4a3c87_1782672356.jpg', 1, 0, '2026-06-28 18:45:56');

-- --------------------------------------------------------

--
-- Structure de la table `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nom` varchar(100) NOT NULL,
  `prenom` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `telephone` varchar(20) DEFAULT NULL,
  `adresse` text,
  `password` varchar(255) NOT NULL,
  `type_utilisateur` enum('client','bailleur','agent','manager') NOT NULL,
  `agent_id` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`),
  KEY `agent_id` (`agent_id`),
  KEY `idx_email` (`email`),
  KEY `idx_type` (`type_utilisateur`)
) ENGINE=MyISAM AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Déchargement des données de la table `users`
--

INSERT INTO `users` (`id`, `nom`, `prenom`, `email`, `telephone`, `adresse`, `password`, `type_utilisateur`, `agent_id`, `created_at`, `updated_at`) VALUES
(25, 'OUEDRAOGO', 'innocent', 'innocent@gmail.com', '66748182', 'NIOKO2', '$2y$12$CKLzZ1aeGsnJGTSRkjTUGe2s0oqlSTsnI0/ySils1GFekvePlSJvu', 'bailleur', NULL, '2026-06-28 19:02:28', '2026-06-28 19:02:28'),
(22, 'Manager', 'Test', 'manager1@immobilier.com', '70123456', 'Ouagadougou', '$2y$12$3dsdMrx2AccfgxluJgDIJu2ds/mHDm7S8RfCxUFSGJkrM3Bq4211C', 'manager', NULL, '2026-06-28 19:02:28', '2026-06-28 19:02:28'),
(24, 'OUEDRAOGO', 'nemata', 'nemata@gmail.com', '76920189', 'KILWIN', '$2y$12$MEu5djcNLXzybEOKk7JEuOwUxwtqGJlaCng/dmSLv7HUSG2bNT4IC', 'client', NULL, '2026-06-28 19:02:28', '2026-06-28 19:02:28'),
(23, 'Agent', 'Test', 'agent@immobilier.com', '70123457', 'Ouagadougou', '$2y$12$tYXbJBuMhZExwMp4T8UvkevKdu/wfMh22r/7joAmVwKwmZnYz2R5K', 'agent', NULL, '2026-06-28 19:02:28', '2026-06-28 19:02:28');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
