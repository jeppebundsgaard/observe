
CREATE TABLE `organizations` (
  `lang` enum('da_DK','en_US') NOT NULL,
  `org_id` int UNSIGNED NOT NULL,
  `orgname` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `orgslogan` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `orgurl` varchar(32) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `settings` json NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Struktur-dump for tabellen `users`
--

CREATE TABLE `users` (
  `user_id` int UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT '',
  `username` varchar(30) NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `org_id` int UNSIGNED NOT NULL DEFAULT '0',
  `permissions` set('baseuser','wordedit','affix','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL DEFAULT 'baseuser',
  `lastchange` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Begrænsninger for dumpede tabeller
--

--
-- Indeks for tabel `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`org_id`),
  ADD UNIQUE KEY `orgurl` (`orgurl`),
  ADD UNIQUE KEY `lang` (`lang`);

--
-- Indeks for tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Tilføj AUTO_INCREMENT i tabel `organizations`
--
ALTER TABLE `organizations`
  MODIFY `org_id` int UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Tilføj AUTO_INCREMENT i tabel `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int UNSIGNED NOT NULL AUTO_INCREMENT;
COMMIT;


INSERT INTO `organizations` (`org_id`, `orgname`, `orgslogan`, `orgurl`, `settings`) VALUES ('1', 'Main Orgainzation', 'Site administration', 'admin', '{}'); 
INSERT INTO `users` (`user_id`, `username`, `email`, `password`, `org_id`, `permissions`, `lastchange`) VALUES ('1', 'admin', 'admin@admin.com', '21232f297a57a5a743894a0e4a801fc3', '1', 'admin', '0000-00-00 00:00:00.000000');
-- User admin@admin.com, Password: admin
