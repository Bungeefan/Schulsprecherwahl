-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Erstellungszeit: 17. Sep 2020 um 18:55
-- Server-Version: 10.4.11-MariaDB
-- PHP-Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT = @@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS = @@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION = @@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `voting_system`
--
CREATE DATABASE IF NOT EXISTS `voting_system` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `voting_system`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `candidates`
--

CREATE TABLE IF NOT EXISTS `candidates`
(
    `ID`             int(11)     NOT NULL AUTO_INCREMENT,
    `CandidateType`  int(11)     NOT NULL,
    `Class`          varchar(50) NOT NULL,
    `FirstName`      varchar(50) NOT NULL,
    `LastName`       varchar(50) NOT NULL,
    `AdditionalText` text         DEFAULT NULL,
    `ImagePath`      varchar(260) DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `candidates_idx_id` (`ID`),
    KEY `FK_CandidateType` (`CandidateType`),
    KEY `FK_Class` (`Class`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 12
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `candidates_types`
--

CREATE TABLE IF NOT EXISTS `candidates_types`
(
    `ID`               int(11)     NOT NULL AUTO_INCREMENT,
    `Type`             varchar(50) NOT NULL,
    `DependingOnClass` tinyint(1)  NOT NULL DEFAULT 0,
    PRIMARY KEY (`ID`)
) ENGINE = InnoDB
  AUTO_INCREMENT = 3
  DEFAULT CHARSET = utf8mb4;

--
-- Daten für Tabelle `candidates_types`
--

INSERT INTO `candidates_types` (`ID`, `Type`, `DependingOnClass`)
VALUES (1, 'Schulsprecher', 0),
       (2, 'Abteilungssprecher', 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `classes`
--

CREATE TABLE IF NOT EXISTS `classes`
(
    `Name`        varchar(50)          NOT NULL,
    `SubjectArea` enum ('HIF','AHBGM') NOT NULL,
    PRIMARY KEY (`Name`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

--
-- Daten für Tabelle `classes`
--

INSERT INTO `classes` (`Name`, `SubjectArea`)
VALUES ('1', 'AHBGM'),
       ('1A', 'HIF'),
       ('1B', 'HIF'),
       ('1C', 'HIF'),
       ('2', 'AHBGM'),
       ('2A', 'HIF'),
       ('2B', 'HIF'),
       ('2C', 'HIF'),
       ('3', 'AHBGM'),
       ('3A', 'HIF'),
       ('3B', 'HIF'),
       ('3C', 'HIF'),
       ('4', 'AHBGM'),
       ('4A', 'HIF'),
       ('4B', 'HIF'),
       ('4C', 'HIF'),
       ('5', 'AHBGM'),
       ('5A', 'HIF'),
       ('5B', 'HIF'),
       ('5C', 'HIF');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `votes`
--

CREATE TABLE IF NOT EXISTS `votes`
(
    `VoteKey`     varchar(50) NOT NULL,
    `CandidateID` int(11)     NOT NULL,
    `VoteCount`   int(11)     NOT NULL,
    PRIMARY KEY (`VoteKey`, `CandidateID`),
    KEY `VoteKey` (`VoteKey`),
    KEY `votes_idx_votekey_votecount` (`VoteKey`, `VoteCount`),
    KEY `FK_votes_runoff_candidates_CandidateID` (`CandidateID`) USING BTREE
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `votes_runoff`
--

CREATE TABLE IF NOT EXISTS `votes_runoff`
(
    `VoteKey`     varchar(50) NOT NULL,
    `CandidateID` int(11)     NOT NULL,
    PRIMARY KEY (`VoteKey`, `CandidateID`),
    KEY `VoteKey` (`VoteKey`),
    KEY `votes_runoff_idx_votekey_candidateid` (`VoteKey`, `CandidateID`),
    KEY `FK_votes_candidates_CandidateID` (`CandidateID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `voting_keys`
--

CREATE TABLE IF NOT EXISTS `voting_keys`
(
    `VoteKey`     varchar(50) NOT NULL,
    `Class`       varchar(50) NOT NULL,
    `Blacklisted` tinyint(1)  NOT NULL DEFAULT 0,
    `Used`        datetime             DEFAULT NULL,
    PRIMARY KEY (`VoteKey`),
    KEY `voting_keys_idx_blacklisted` (`Blacklisted`),
    KEY `FK_voting_keys_Class` (`Class`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `candidates`
--
ALTER TABLE `candidates`
    ADD CONSTRAINT `FK_CandidateType` FOREIGN KEY (`CandidateType`) REFERENCES `candidates_types` (`ID`) ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_Class` FOREIGN KEY (`Class`) REFERENCES `classes` (`Name`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `votes`
--
ALTER TABLE `votes`
    ADD CONSTRAINT `FK_votes_candidates_CandidateID` FOREIGN KEY (`CandidateID`) REFERENCES `candidates` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_votes_voting_keys_VoteKey` FOREIGN KEY (`VoteKey`) REFERENCES `voting_keys` (`VoteKey`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `votes_runoff`
--
ALTER TABLE `votes_runoff`
    ADD CONSTRAINT `FK_votes_runoff_candidates_CandidateID` FOREIGN KEY (`CandidateID`) REFERENCES `candidates` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE,
    ADD CONSTRAINT `FK_votes_runoff_voting_keys_VoteKey` FOREIGN KEY (`VoteKey`) REFERENCES `voting_keys` (`VoteKey`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `voting_keys`
--
ALTER TABLE `voting_keys`
    ADD CONSTRAINT `FK_voting_keys_Class` FOREIGN KEY (`Class`) REFERENCES `classes` (`Name`) ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
