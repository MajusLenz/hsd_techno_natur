-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Erstellungszeit: 18. Jan 2023 um 16:41
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
(1, 'b9969ddc974a11ed921077026793e341', 0, '2023-01-18 16:27:28', NULL),
(2, 'b9969ecc974a11ed92117bf9c8afc0c0', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:45'),
(3, 'b9969ef4974a11ed92122bf6d413e8d6', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:48'),
(4, 'b9969f12974a11ed9213ff6b3c3203d0', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:51'),
(5, 'b9969f30974a11ed9214afcb49c1eca6', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:53'),
(6, 'b9969f4e974a11ed9215ef2faca246c9', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:54'),
(7, 'b9969f6c974a11ed9216a3c355e301d9', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:56'),
(8, 'b9969f8a974a11ed9217c345958a6b1a', 0, '2023-01-18 16:29:28', '2023-01-18 16:29:59'),
(9, 'b9969fa8974a11ed921823a85801f904', 0, '2023-01-18 16:30:34', NULL),
(10, 'b9969fc6974a11ed9219df18e3c69141', 0, '2023-01-18 16:30:34', NULL),
(11, 'b9969fe4974a11ed921a6b3f21e05be4', 1, '2023-01-18 16:36:32', NULL),
(12, 'b996a002974a11ed921ba3f1f754681e', 1, '2023-01-18 16:36:32', NULL),
(13, 'b996a016974a11ed921cf7dc0f96d48a', 1, '2023-01-18 16:36:32', NULL),
(14, 'b996a034974a11ed921d63234446a195', 1, '2023-01-18 16:36:32', NULL),
(15, 'b996a052974a11ed921ea32236f1533a', 1, '2023-01-18 16:36:32', NULL),
(16, 'b996a070974a11ed921ff361c83396aa', 1, '2023-01-18 16:36:32', NULL),
(17, 'b996a08e974a11ed9220c300dde4b3e5', 1, '2023-01-18 16:36:32', NULL),
(18, 'b996a0ac974a11ed9221ff56ed481f90', 1, '2023-01-18 16:36:32', NULL),
(19, 'b996a0ca974a11ed92229b63725e78f8', 1, '2023-01-18 16:36:49', NULL),
(20, 'b996a0e8974a11ed9223ff4a9d16129e', 1, '2023-01-18 16:36:49', NULL);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT für Tabelle `fungi_seeker`
--
ALTER TABLE `fungi_seeker`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT für Tabelle `seeker`
--
ALTER TABLE `seeker`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

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
