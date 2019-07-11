-- Adminer 4.7.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

DROP TABLE IF EXISTS `description`;
CREATE TABLE `description` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `subject` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hidden` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `description_body` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `user_id` int(10) DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `description` (`id`, `subject`, `hidden`, `description_body`, `created_at`, `user_id`) VALUES
(1,	'Demo',	NULL,	'<p>Lorem ipsum dolor sit amet, vix scaevola instructior no, id probo quidam deserunt usu. Ceteros concludaturque sit te. Ea pri odio facete, quis alterum ocurreret ad mei, vel in tale illud ignota. In cum alia definitiones, at mel iisque disputando, eu ancillae electram pri. Pro eu populo accusam, at mei choro delicata pertinacia, id eam audire propriae consulatu.</p>',	'2019-06-02 14:50:26',	1);

DROP TABLE IF EXISTS `roles`;
CREATE TABLE `roles` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `title` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `roles` (`id`, `title`, `created_at`) VALUES
(1,	'Normal',	'2019-01-12 19:28:48'),
(2,	'Admin',	'2019-01-12 19:29:10');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(10) NOT NULL AUTO_INCREMENT,
  `role_id` int(11) DEFAULT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `users` (`id`, `role_id`, `username`, `email`, `password`, `created_at`) VALUES
(1,	2,	'admin',	'demo@demo.admin',	'$2y$10$h17gneP/PKRi6cjowxDqCOrt/cMd53lIliRVv/SiTlDUXYGqy8Mxa',	'2019-01-13 01:35:04');

-- 2019-07-10 23:14:26
