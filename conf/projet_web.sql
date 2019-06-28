-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  mar. 27 mars 2018 à 15:05
-- Version du serveur :  5.7.19
-- Version de PHP :  5.6.31

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `projet_web`
--

-- --------------------------------------------------------

--
-- Structure de la table `annual_contributions`
--

DROP TABLE IF EXISTS `annual_contributions`;
CREATE TABLE IF NOT EXISTS `annual_contributions` (
  `year` int(4) NOT NULL,
  `member_id` int(11) NOT NULL,
  `amount_paid` double NOT NULL,
  PRIMARY KEY (`year`,`member_id`),
  KEY `member_id` (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `contributions`
--

DROP TABLE IF EXISTS `contributions`;
CREATE TABLE IF NOT EXISTS `contributions` (
  `year` int(4) NOT NULL,
  `price` double NOT NULL,
  PRIMARY KEY (`year`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `daily_plans`
--

DROP TABLE IF EXISTS `daily_plans`;
CREATE TABLE IF NOT EXISTS `daily_plans` (
  `date` date NOT NULL,
  `plan_id` int(11) NOT NULL,
  `training_description` varchar(255) NOT NULL,
  PRIMARY KEY (`date`,`plan_id`),
  KEY `plan_id` (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `events`
--

DROP TABLE IF EXISTS `events`;
CREATE TABLE IF NOT EXISTS `events` (
  `event_id` int(11) NOT NULL AUTO_INCREMENT,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  `event_name` varchar(50) NOT NULL,
  `description` varchar(255) NOT NULL,
  `location` varchar(50) NOT NULL,
  `url` varchar(255) DEFAULT NULL,
  `cost` double NOT NULL,
  `lattitude` double DEFAULT NULL,
  `longitude` double DEFAULT NULL,
  `url_picture` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `interrested_members`
--

DROP TABLE IF EXISTS `interrested_members`;
CREATE TABLE IF NOT EXISTS `interrested_members` (
  `member_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  PRIMARY KEY (`member_id`,`event_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `members`
--

DROP TABLE IF EXISTS `members`;
CREATE TABLE IF NOT EXISTS `members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `last_name` varchar(50) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `password` varchar(60) NOT NULL,
  `adress` varchar(255) NOT NULL,
  `phone_number` varchar(10) NOT NULL,
  `account_number` varchar(20) NOT NULL,
  `responsablity` varchar(50) NOT NULL,
  `accepted` tinyint(1) NOT NULL,
  `contributed` tinyint(1) NOT NULL,
  PRIMARY KEY (`member_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `participating_members`
--

DROP TABLE IF EXISTS `participating_members`;
CREATE TABLE IF NOT EXISTS `participating_members` (
  `member_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `has_paid` tinyint(1) NOT NULL,
  PRIMARY KEY (`member_id`,`event_id`),
  KEY `event_id` (`event_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `trainings`
--

DROP TABLE IF EXISTS `trainings`;
CREATE TABLE IF NOT EXISTS `trainings` (
  `training_id` int(11) NOT NULL AUTO_INCREMENT,
  `member_id` int(11) NOT NULL,
  `plan_id` int(11) NOT NULL,
  `training_start_date` date NOT NULL,
  `training_end_date` date DEFAULT NULL,
  PRIMARY KEY (`training_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `training_plans`
--

DROP TABLE IF EXISTS `training_plans`;
CREATE TABLE IF NOT EXISTS `training_plans` (
  `plan_id` int(11) NOT NULL AUTO_INCREMENT,
  `goal` varchar(255) NOT NULL,
  `plan_name` varchar(50) NOT NULL,
  `date_start` date NOT NULL,
  `date_end` date NOT NULL,
  PRIMARY KEY (`plan_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `annual_contributions`
--
ALTER TABLE `annual_contributions`
  ADD CONSTRAINT `annual_contributions_ibfk_1` FOREIGN KEY (`year`) REFERENCES `contributions` (`year`),
  ADD CONSTRAINT `annual_contributions_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Contraintes pour la table `daily_plans`
--
ALTER TABLE `daily_plans`
  ADD CONSTRAINT `daily_plans_ibfk_1` FOREIGN KEY (`plan_id`) REFERENCES `training_plans` (`plan_id`);

--
-- Contraintes pour la table `interrested_members`
--
ALTER TABLE `interrested_members`
  ADD CONSTRAINT `interrested_members_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`),
  ADD CONSTRAINT `interrested_members_ibfk_2` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`);

--
-- Contraintes pour la table `participating_members`
--
ALTER TABLE `participating_members`
  ADD CONSTRAINT `participating_members_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `participating_members_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`event_id`);

--
-- Contraintes pour la table `trainings`
--
ALTER TABLE `trainings`
  ADD CONSTRAINT `trainings_ibfk_1` FOREIGN KEY (`member_id`) REFERENCES `members` (`member_id`),
  ADD CONSTRAINT `trainings_ibfk_2` FOREIGN KEY (`plan_id`) REFERENCES `training_plans` (`plan_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
