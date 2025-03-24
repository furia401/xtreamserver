-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Tempo de geração: 23/03/2025 às 22:46
-- Versão do servidor: 8.3.0
-- Versão do PHP: 8.3.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Banco de dados: `xtserveropensource`
--

-- --------------------------------------------------------

--
-- Estrutura para tabela `admin`
--

DROP TABLE IF EXISTS `admin`;
CREATE TABLE IF NOT EXISTS `admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `pass` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `admin` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `creditos` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `creditos_usados` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `criado_por` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '0',
  `servidores` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT '0',
  `importacao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'nao',
  `plano` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `email` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `telegram` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `whatsapp` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo_link` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'padrao',
  `saldo_devedor` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data_criado` date DEFAULT NULL,
  `Vencimento` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `user` (`user`)
) ENGINE=InnoDB AUTO_INCREMENT=71 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `admin`
--

INSERT INTO `admin` (`id`, `user`, `pass`, `admin`, `creditos`, `creditos_usados`, `criado_por`, `servidores`, `importacao`, `plano`, `email`, `telegram`, `whatsapp`, `tipo_link`, `saldo_devedor`, `token`, `data_criado`, `Vencimento`) VALUES
(11, 'admin', 'admin', '1', '0', '0', '0', '0', 'nao', '4', 'joseflavio401@gmail.com', 'efr', '43543534', 'padrao', '0', '252fdb78f933307c614fce1b03219f69b5f84c3a7b81e1ab3cae441386e39bd5', NULL, '2025-08-31');

-- --------------------------------------------------------

--
-- Estrutura para tabela `categoria`
--

DROP TABLE IF EXISTS `categoria`;
CREATE TABLE IF NOT EXISTS `categoria` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `parent_id` int DEFAULT '0',
  `type` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `is_adult` int DEFAULT '0',
  `bg` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `admin_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `clientes`
--

DROP TABLE IF EXISTS `clientes`;
CREATE TABLE IF NOT EXISTS `clientes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `info_conexoes` text COLLATE utf8mb4_general_ci,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senha` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Criado_em` datetime DEFAULT NULL,
  `Ultimo_pagamento` datetime DEFAULT NULL,
  `Vencimento` timestamp NULL DEFAULT NULL,
  `is_trial` int NOT NULL DEFAULT '0',
  `adulto` int NOT NULL DEFAULT '0',
  `conexoes` int NOT NULL DEFAULT '1',
  `bloqueio_conexao` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'sim',
  `admin_id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '0',
  `ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `ultimo_acesso` datetime DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `ultimo_ip` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Dispositivo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Deconhecido',
  `App` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Deconhecido',
  `Forma_de_pagamento` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `nome_do_pagador` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `Whatsapp` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `plano` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL,
  `V_total` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20',
  `c_ocultar_fonte` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'nao',
  `msg` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `indicado_por` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_mac` varchar(17) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `device_key` char(6) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `email_app` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `senha_app` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `validade_app` date DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `usuario` (`usuario`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `devices_apps`
--

DROP TABLE IF EXISTS `devices_apps`;
CREATE TABLE IF NOT EXISTS `devices_apps` (
  `id` int NOT NULL AUTO_INCREMENT,
  `device_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `app_name` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `devices_apps`
--

INSERT INTO `devices_apps` (`id`, `device_name`, `app_name`) VALUES
(1, 'TV SMART', 'SS-IPTV'),
(2, 'TV SMART', 'STB'),
(3, 'TV SMART', 'SMART ONE'),
(4, 'TV SMART', 'SETIPTV'),
(5, 'TV SMART', 'WOWTV'),
(6, 'TV SMART', 'ClouddY'),
(7, 'TV SMART AOC', 'SS-IPTV'),
(8, 'TV SMART AOC', 'ClouddY'),
(9, 'TV SMART AOC', 'SmartUP'),
(10, 'TV PHILIPS', 'SS-IPTV'),
(11, 'TV BOX', 'XCIPTV'),
(12, 'TV BOX', 'SMARTERS PLAYER LITE'),
(13, 'TV BOX', 'SMARTERS PLAYER PRO'),
(14, 'TV BOX', 'EASYPLAY LITE'),
(15, 'TV BOX', 'Stream Player'),
(16, 'TV BOX', 'Smart IPTV Xtream'),
(17, 'TV ANDROID', 'XCIPTV'),
(18, 'TV ANDROID', 'SMARTERS PLAYER LITE'),
(19, 'TV ANDROID', 'SMARTERS PLAYER PRO'),
(20, 'TV ANDROID', 'EASYPLAY LITE'),
(21, 'TV ANDROID', 'Stream Player'),
(22, 'TV ANDROID', 'Smart IPTV Xtream'),
(23, 'TV LG', 'IPTV SMARTERES PRO'),
(24, 'TV LG', 'SS-IPTV'),
(25, 'TV LG', 'STB'),
(26, 'TV LG', 'SMART ONE'),
(27, 'TV LG', 'SETIPTV'),
(28, 'TV LG', 'ClouddY'),
(29, 'TV LG', 'SmartUP'),
(30, 'TV SANSUNG', 'IPTV SMARTERES PRO'),
(31, 'TV SANSUNG', 'SS-IPTV'),
(32, 'TV SANSUNG', 'STB'),
(33, 'TV SANSUNG', 'SMART ONE'),
(34, 'TV SANSUNG', 'SETIPTV'),
(35, 'TV SANSUNG', 'ClouddY'),
(36, 'TV SANSUNG', 'SmartUP'),
(37, 'PC/COMPUTADOR', 'SMARTERS PLAYER PRO'),
(40, 'Roku TV', 'SIMPLE TV'),
(41, 'TV ANDROID', 'Sky Glass+'),
(42, 'TV ANDROID', 'FURIA PLAY SM V3'),
(43, 'TV ANDROID', 'FURIA PLAY SM V4'),
(44, 'Roku TV', 'Quick Player'),
(45, 'Roku TV', 'Meta Player'),
(46, 'TV BOX', 'FURIA PLAY SM V3'),
(47, 'TV BOX', 'FURIA PLAY SM V4'),
(49, 'Roku TV', 'IBO PLAYER PRO'),
(50, 'Roku TV', 'IBO PRO'),
(51, 'TV SANSUNG', 'IBO PLAYER PRO'),
(52, 'TV SANSUNG', 'IBO PRO'),
(53, 'TV LG', 'IBO PLAYER PRO'),
(54, 'TV LG', 'IBO PRO'),
(55, 'TV SMART', 'IBO PLAYER PRO'),
(56, 'TV SMART', 'IBO PRO');

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos`
--

DROP TABLE IF EXISTS `planos`;
CREATE TABLE IF NOT EXISTS `planos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` text COLLATE utf8mb4_general_ci NOT NULL,
  `valor` varchar(255) COLLATE utf8mb4_general_ci NOT NULL DEFAULT '20',
  `valor_primeiro_mes` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `custo_por_credito` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `admin_id` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planos`
--

INSERT INTO `planos` (`id`, `nome`, `valor`, `valor_primeiro_mes`, `custo_por_credito`, `admin_id`) VALUES
(1, 'Completo', '35', '20', '0', 11);

-- --------------------------------------------------------

--
-- Estrutura para tabela `planos_admin`
--

DROP TABLE IF EXISTS `planos_admin`;
CREATE TABLE IF NOT EXISTS `planos_admin` (
  `id` int NOT NULL AUTO_INCREMENT,
  `nome` text COLLATE utf8mb4_general_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Despejando dados para a tabela `planos_admin`
--

INSERT INTO `planos_admin` (`id`, `nome`) VALUES
(1, 'Nivel 1: Sub-Revenda'),
(2, 'Nivel 2: Revenda'),
(3, 'Nivel 3: Master'),
(4, 'Nivel 4: Master-Pro');

-- --------------------------------------------------------

--
-- Estrutura para tabela `series`
--

DROP TABLE IF EXISTS `series`;
CREATE TABLE IF NOT EXISTS `series` (
  `id` int NOT NULL AUTO_INCREMENT,
  `is_adult` int DEFAULT '0',
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `category_id` int DEFAULT NULL,
  `year` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `stream_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'series',
  `cover` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `plot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cast` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `director` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `genre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `release_date` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `releaseDate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `last_modified` int DEFAULT NULL,
  `rating` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating_5based` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `backdrop_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `youtube_trailer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `episode_run_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tmdb_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `series_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `series_episodes`
--

DROP TABLE IF EXISTS `series_episodes`;
CREATE TABLE IF NOT EXISTS `series_episodes` (
  `id` int NOT NULL AUTO_INCREMENT,
  `situacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo_link` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'padrao',
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `series_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `episode_num` int DEFAULT NULL,
  `title` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `container_extension` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'mp4',
  `duration_secs` int DEFAULT NULL,
  `duration` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `bitrate` int DEFAULT NULL,
  `cover_big` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `plot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `movie_image` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `subtitles` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `custom_sid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `added` int DEFAULT NULL,
  `season` int DEFAULT NULL,
  `tmdb_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_series_episodes_series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `series_seasons`
--

DROP TABLE IF EXISTS `series_seasons`;
CREATE TABLE IF NOT EXISTS `series_seasons` (
  `id` int NOT NULL AUTO_INCREMENT,
  `series_id` int DEFAULT NULL,
  `category_id` int DEFAULT NULL,
  `air_date` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `episode_count` int DEFAULT NULL,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `overview` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `season_number` int DEFAULT NULL,
  `cover` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cover_big` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tmdb_id` int DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_series_seasons_series` (`series_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `streams`
--

DROP TABLE IF EXISTS `streams`;
CREATE TABLE IF NOT EXISTS `streams` (
  `id` int NOT NULL AUTO_INCREMENT,
  `situacao` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tipo_link` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'padrao',
  `link` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `name` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `year` int DEFAULT NULL,
  `stream_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT 'movie',
  `stream_icon` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating_5based` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `added` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `category_id` int DEFAULT NULL,
  `epg_channel_id` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `container_extension` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'ts',
  `custom_sid` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `direct_source` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `kinopoisk_url` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `tmdb_id` int DEFAULT NULL,
  `cover_big` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `release_date` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `episode_run_time` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `youtube_trailer` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `director` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `actors` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `cast` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `description` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `plot` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `age` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `rating_count_kinopoisk` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `country` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `genre` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `backdrop_path` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `duration_secs` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `duration` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `bitrate` int DEFAULT NULL,
  `releasedate` text CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci,
  `subtitles` int DEFAULT NULL,
  `is_adult` int DEFAULT '0',
  PRIMARY KEY (`id`),
  UNIQUE KEY `stream_id` (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Estrutura para tabela `ultimos_acessos`
--

DROP TABLE IF EXISTS `ultimos_acessos`;
CREATE TABLE IF NOT EXISTS `ultimos_acessos` (
  `id` int NOT NULL AUTO_INCREMENT,
  `id_user` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `usuario` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `tipo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `nome` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `logo` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `status` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  `data` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
