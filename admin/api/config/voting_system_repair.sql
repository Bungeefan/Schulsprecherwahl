-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 18. Nov 2019 um 17:31
-- Server-Version: 10.4.8-MariaDB
-- PHP-Version: 7.3.11

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
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
    `FirstName`      varchar(50) NOT NULL,
    `LastName`       varchar(50) NOT NULL,
    `AdditionalText` text         DEFAULT NULL,
    `ImagePath`      varchar(260) DEFAULT NULL,
    PRIMARY KEY (`ID`),
    KEY `candidates_idx_id` (`ID`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

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
    `Blacklisted` tinyint(1)  NOT NULL DEFAULT 0,
    `Used`        datetime             DEFAULT NULL,
    PRIMARY KEY (`VoteKey`),
    KEY `voting_keys_idx_blacklisted` (`Blacklisted`)
) ENGINE = InnoDB
  DEFAULT CHARSET = utf8mb4;

--
-- Constraints der exportierten Tabellen
--

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT = @OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS = @OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION = @OLD_COLLATION_CONNECTION */;
