-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Vært: localhost
-- Genereringstid: 11. 11 2022 kl. 11:56:21
-- Serverversion: 8.0.31-0ubuntu0.22.04.1
-- PHP-version: 8.1.2-1ubuntu2.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `observe`
--

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `invited_users`
--

CREATE TABLE `invited_users` (
  `email` varchar(255) NOT NULL,
  `registrationcode` varchar(30) NOT NULL,
  `study_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `ip2location_db1`
--

CREATE TABLE `ip2location_db1` (
  `ip_from` int UNSIGNED DEFAULT NULL,
  `ip_to` int UNSIGNED DEFAULT NULL,
  `country_code` char(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL,
  `country_name` varchar(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_bin DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_bin;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `languages`
--

CREATE TABLE `languages` (
  `language_family` varchar(19) DEFAULT NULL,
  `code` varchar(2) DEFAULT NULL,
  `language_name` varchar(80) DEFAULT NULL,
  `native_name` varchar(51) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COMMENT='Imported from https://en.wikipedia.org/wiki/List_of_ISO_639-1_codes';

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `observations`
--

CREATE TABLE `observations` (
  `id` int UNSIGNED NOT NULL,
  `study_id` int UNSIGNED NOT NULL,
  `observations` json NOT NULL,
  `date` date NOT NULL,
  `starttime` time NOT NULL,
  `endtime` time NOT NULL,
  `institution_id` int UNSIGNED NOT NULL,
  `groups` json NOT NULL,
  `participants` json NOT NULL,
  `subject` varchar(50) NOT NULL,
  `round` varchar(50) NOT NULL DEFAULT '',
  `observer_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `observers`
--

CREATE TABLE `observers` (
  `user_id` int UNSIGNED NOT NULL,
  `study_id` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `obsschemes`
--

CREATE TABLE `obsschemes` (
  `id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `reference` varchar(255) NOT NULL DEFAULT '',
  `obsscheme` json NOT NULL,
  `owner` int UNSIGNED NOT NULL,
  `public` tinyint(1) NOT NULL DEFAULT '0',
  `language` varchar(2) NOT NULL DEFAULT 'en'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `organizations`
--

CREATE TABLE `organizations` (
  `lang` enum('da_DK','en_US') NOT NULL,
  `org_id` int UNSIGNED NOT NULL,
  `orgname` varchar(255) NOT NULL,
  `orgslogan` varchar(255) NOT NULL DEFAULT '',
  `orgurl` varchar(32) NOT NULL DEFAULT '',
  `settings` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `studies`
--

CREATE TABLE `studies` (
  `id` int UNSIGNED NOT NULL,
  `owner` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `observation_scheme` int UNSIGNED NOT NULL,
  `translation` int UNSIGNED NOT NULL,
  `institutions` json NOT NULL,
  `subjects` json NOT NULL,
  `rounds` json NOT NULL,
  `settings` json NOT NULL,
  `status` enum('under_construction','active','inactive','finished') NOT NULL DEFAULT 'under_construction'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `translations`
--

CREATE TABLE `translations` (
  `id` int UNSIGNED NOT NULL,
  `obsscheme_id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `reference` varchar(255) NOT NULL DEFAULT '',
  `translation` json NOT NULL,
  `translator` int UNSIGNED NOT NULL,
  `language` varchar(2) NOT NULL DEFAULT 'da'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `org_id` int UNSIGNED NOT NULL DEFAULT '0',
  `permissions` set('observer','baseuser','admin') NOT NULL DEFAULT 'baseuser',
  `lastchange` timestamp NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Data dump for tabellen `users`
--
INSERT INTO `users` (`user_id`, `name`, `username`, `email`, `password`, `org_id`, `permissions`, `lastchange`) VALUES
(1, 'Admin User', 'observe', 'admin@observe.education.example', '26723393df0594b606034d3bc4827d3f', 1, 'baseuser', '2022-11-14 22:03:59');

--
-- Indeks for tabel `invited_users`
--
ALTER TABLE `invited_users`
  ADD PRIMARY KEY (`email`,`study_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD KEY `study_id` (`study_id`);

--
-- Indeks for tabel `ip2location_db1`
--
ALTER TABLE `ip2location_db1`
  ADD KEY `idx_ip_from` (`ip_from`),
  ADD KEY `idx_ip_to` (`ip_to`),
  ADD KEY `idx_ip_from_to` (`ip_from`,`ip_to`);

--
-- Indeks for tabel `languages`
--
ALTER TABLE `languages`
  ADD UNIQUE KEY `code` (`code`);

--
-- Indeks for tabel `observations`
--
ALTER TABLE `observations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `study` (`study_id`),
  ADD KEY `starttime` (`starttime`);

--
-- Indeks for tabel `observers`
--
ALTER TABLE `observers`
  ADD PRIMARY KEY (`user_id`,`study_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `study_id` (`study_id`);

--
-- Indeks for tabel `obsschemes`
--
ALTER TABLE `obsschemes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner` (`owner`);

--
-- Indeks for tabel `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `orgurl` (`orgurl`),
  ADD UNIQUE KEY `lang` (`lang`);

--
-- Indeks for tabel `studies`
--
ALTER TABLE `studies`
  ADD PRIMARY KEY (`id`);

--
-- Indeks for tabel `translations`
--
ALTER TABLE `translations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `owner` (`translator`),
  ADD KEY `id` (`obsscheme_id`) USING BTREE;

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Brug ikke AUTO_INCREMENT for slettede tabeller
--

--
-- Tilføj AUTO_INCREMENT i tabel `observations`
--
ALTER TABLE `observations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `obsschemes`
--
ALTER TABLE `obsschemes`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `organizations`
--
ALTER TABLE `organizations`
  MODIFY `org_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `studies`
--
ALTER TABLE `studies`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `translations`
--
ALTER TABLE `translations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Begrænsninger for tabel `invited_users`
--
ALTER TABLE `invited_users`
  ADD CONSTRAINT `invited_users_ibfk_1` FOREIGN KEY (`study_id`) REFERENCES `studies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `observations`
--
ALTER TABLE `observations`
  ADD CONSTRAINT `observations_ibfk_1` FOREIGN KEY (`study_id`) REFERENCES `studies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `observers`
--
ALTER TABLE `observers`
  ADD CONSTRAINT `observers_ibfk_1` FOREIGN KEY (`study_id`) REFERENCES `studies` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `observers_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Begrænsninger for tabel `translations`
--
ALTER TABLE `translations`
  ADD CONSTRAINT `Id` FOREIGN KEY (`obsscheme_id`) REFERENCES `obsschemes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
