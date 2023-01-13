-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Erstellungszeit: 09. Dez 2022 um 14:34
-- Server-Version: 8.0.21
-- PHP-Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `fungi_data`
--
CREATE DATABASE IF NOT EXISTS `fungi_data` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci;
USE `fungi_data`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fungi`
--

CREATE TABLE `fungi` (
  `id` int UNSIGNED NOT NULL,
  `uuid` varchar(32) DEFAULT NULL,
  `floor` int DEFAULT NULL,
  `created_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `fungi`
--

INSERT INTO `fungi` (`id`, `uuid`, `floor`, `created_at`, `updated_at`) VALUES
(1, '1a3645d1d9d345bba3b0298ad6d32a27', 1, '2022-12-09 14:18:25', NULL),
(2, '1a3645d1d9d345bba3b0298ad6d32a11', 1, '2022-12-09 14:18:25', NULL),
(3, '1a3645d1d9d345bba3b0298ad6d32a22', 0, '2022-12-09 14:19:02', NULL),
(4, '1a3645d1d9d345bba3b0298ad6d32a33', 0, '2022-12-09 14:19:02', NULL),
(5, '1a3645d1d9d345bba3b0298ad6d32a55', 1, '2022-12-09 14:19:20', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `fungi_seeker`
--

CREATE TABLE `fungi_seeker` (
  `id` int UNSIGNED NOT NULL,
  `fungi_id` int UNSIGNED NOT NULL,
  `seeker_id` int UNSIGNED NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `fungi_seeker`
--

INSERT INTO `fungi_seeker` (`id`, `fungi_id`, `seeker_id`, `created_at`, `updated_at`) VALUES
(1, 2, 1, '2022-12-09 14:20:56', NULL),
(2, 3, 1, '2022-12-09 14:24:24', NULL),
(3, 3, 2, '2022-12-09 14:24:48', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `seeker`
--

CREATE TABLE `seeker` (
  `id` int UNSIGNED NOT NULL,
  `uuid` varchar(32) DEFAULT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` datetime DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Daten für Tabelle `seeker`
--

INSERT INTO `seeker` (`id`, `uuid`, `created_at`, `updated_at`) VALUES
(1, '1a3645d1d9d345bba3b0298ad6d32aaa', '2022-12-09 14:19:59', NULL),
(2, '1a3645d1d9d345bba3b0298ad6d32bbb', '2022-12-09 14:19:59', NULL),
(3, '1a3645d1d9d345bba3b0298ad6d32ccc', '2022-12-09 14:20:11', NULL);

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `fungi`
--
ALTER TABLE `fungi`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- Indizes für die Tabelle `fungi_seeker`
--
ALTER TABLE `fungi_seeker`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fungi_id-seeker_id-unique` (`fungi_id`,`seeker_id`),
  ADD KEY `seeker_id` (`seeker_id`);

--
-- Indizes für die Tabelle `seeker`
--
ALTER TABLE `seeker`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid` (`uuid`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `fungi`
--
ALTER TABLE `fungi`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT für Tabelle `fungi_seeker`
--
ALTER TABLE `fungi_seeker`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `seeker`
--
ALTER TABLE `seeker`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `fungi_seeker`
--
ALTER TABLE `fungi_seeker`
  ADD CONSTRAINT `fungi_seeker_ibfk_1` FOREIGN KEY (`fungi_id`) REFERENCES `fungi` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fungi_seeker_ibfk_2` FOREIGN KEY (`seeker_id`) REFERENCES `seeker` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
