-- phpMyAdmin SQL Dump
-- version 4.0.10.20
-- https://www.phpmyadmin.net
--
-- Host: localhost
-- Generation Time: Sep 27, 2018 at 10:04 PM
-- Server version: 5.6.40
-- PHP Version: 5.3.29

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Database: `xdolej08`
--

-- --------------------------------------------------------

--
-- Table structure for table `kis__hostitele`
--

CREATE TABLE IF NOT EXISTS `kis__hostitele` (
  `hostitel_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Hostitele',
  `jmeno` varchar(30) NOT NULL,
  `pohlavi` enum('M','Z') NOT NULL,
  `datum_narozeni` datetime NOT NULL,
  `datum_umrti` datetime DEFAULT NULL,
  PRIMARY KEY (`hostitel_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje nalezené hostitele' AUTO_INCREMENT=9 ;

--
-- Dumping data for table `kis__hostitele`
--

INSERT INTO `kis__hostitele` (`hostitel_id`, `jmeno`, `pohlavi`, `datum_narozeni`, `datum_umrti`) VALUES
(1, 'Kordell Moss', 'M', '1976-09-23 12:05:00', '2010-03-30 02:25:59'),
(2, 'Dina Fuller', 'M', '1982-03-06 18:21:34', NULL),
(3, 'Prevan Green', 'M', '1991-06-08 09:14:54', NULL),
(4, 'Tasheka Kelley', 'M', '1983-11-16 21:39:14', NULL),
(5, 'Lucinda Nelson', 'M', '1946-08-09 16:11:12', '2016-03-10 07:12:20'),
(6, 'Deveron Dawson', 'M', '2000-07-30 01:54:29', NULL),
(7, 'Marqués Copeland', 'M', '1991-01-01 11:39:00', NULL),
(8, 'Denton Myers', 'M', '1987-11-26 14:41:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kis__hostitele_preference`
--

CREATE TABLE IF NOT EXISTS `kis__hostitele_preference` (
  `FK_hostitel_id` int(10) unsigned NOT NULL COMMENT 'ID Hostitele',
  `FK_rasa_id` int(10) unsigned NOT NULL COMMENT 'ID Rasy',
  UNIQUE KEY `FK_hostitel_id_FK_rasa_id` (`FK_hostitel_id`,`FK_rasa_id`),
  KEY `FK_rasa_id` (`FK_rasa_id`),
  KEY `FK_hostitel_id` (`FK_hostitel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Obsahuje preference ras pro dané hostitele';

--
-- Dumping data for table `kis__hostitele_preference`
--

INSERT INTO `kis__hostitele_preference` (`FK_hostitel_id`, `FK_rasa_id`) VALUES
(1, 1),
(2, 1),
(5, 1),
(1, 2),
(3, 2),
(4, 2),
(5, 2),
(6, 2),
(3, 3),
(5, 3),
(6, 3);

-- --------------------------------------------------------

--
-- Table structure for table `kis__kocky`
--

CREATE TABLE IF NOT EXISTS `kis__kocky` (
  `kocka_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Kočky',
  `FK_rasa_id` int(10) unsigned NOT NULL COMMENT 'Rasa ID',
  `jmeno` varchar(30) NOT NULL,
  `login` varchar(20) NOT NULL,
  `heslo` varchar(60) NOT NULL,
  `role` varchar(20) NOT NULL DEFAULT 'uzivatel',
  PRIMARY KEY (`kocka_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje hlavní profily koček' AUTO_INCREMENT=7 ;

--
-- Dumping data for table `kis__kocky`
--

INSERT INTO `kis__kocky` (`kocka_id`, `FK_rasa_id`, `jmeno`, `login`, `heslo`, `role`) VALUES
(1, 1, 'Kuborc', '', '', 'uzivatel'),
(2, 2, 'Greidrirc', '', '', 'uzivatel'),
(3, 3, 'Knices', '', '', 'uzivatel'),
(4, 3, 'Druaq''aullu', '', '', 'uzivatel'),
(5, 2, 'Straxxog', '', '', 'uzivatel'),
(6, 3, 'Kneikkird', '', '', 'uzivatel');

-- --------------------------------------------------------

--
-- Table structure for table `kis__kocky_hostitele`
--

CREATE TABLE IF NOT EXISTS `kis__kocky_hostitele` (
  `FK_zivot_id` int(10) unsigned NOT NULL COMMENT 'ID Života',
  `FK_hostitel_id` int(10) unsigned NOT NULL COMMENT 'ID Hostitele',
  `kryci_jmeno` varchar(30) NOT NULL,
  `datum_od` datetime NOT NULL,
  `datum_do` datetime DEFAULT NULL,
  UNIQUE KEY `FK_zivot_id_FK_hostitel_id` (`FK_zivot_id`,`FK_hostitel_id`),
  KEY `FK_zivot_id` (`FK_zivot_id`),
  KEY `FK_hostitel_id` (`FK_hostitel_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Obsahuje hostitele ovládané kočkami';

--
-- Dumping data for table `kis__kocky_hostitele`
--

INSERT INTO `kis__kocky_hostitele` (`FK_zivot_id`, `FK_hostitel_id`, `kryci_jmeno`, `datum_od`, `datum_do`) VALUES
(1, 2, 'Molly', '0000-00-00 00:00:00', NULL),
(2, 1, 'Ashes', '0000-00-00 00:00:00', NULL),
(3, 3, 'Felix', '0000-00-00 00:00:00', NULL),
(4, 5, 'Smudge', '0000-00-00 00:00:00', NULL),
(5, 4, 'Sooty', '0000-00-00 00:00:00', NULL),
(6, 5, 'Tigger', '0000-00-00 00:00:00', NULL),
(7, 5, 'Charlie', '0000-00-00 00:00:00', NULL),
(8, 2, 'Alfie', '0000-00-00 00:00:00', NULL),
(9, 6, 'Oscar', '0000-00-00 00:00:00', NULL),
(9, 7, 'Millie', '0000-00-00 00:00:00', NULL),
(10, 7, 'Misty', '0000-00-00 00:00:00', NULL),
(11, 7, 'Sausage', '0000-00-00 00:00:00', NULL),
(12, 8, 'Max', '0000-00-00 00:00:00', NULL),
(13, 8, 'Bella', '0000-00-00 00:00:00', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `kis__kocky_pohyb`
--

CREATE TABLE IF NOT EXISTS `kis__kocky_pohyb` (
  `FK_zivot_id` int(10) unsigned NOT NULL COMMENT 'ID Života',
  `FK_teritorium_id` int(10) unsigned NOT NULL COMMENT 'ID Teritoria',
  `cas_pohybu_od` datetime NOT NULL,
  `cas_pohybu_do` datetime DEFAULT NULL,
  KEY `FK_kocka_id` (`FK_zivot_id`),
  KEY `FK_teritorium_id` (`FK_teritorium_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Obsahuje historii pohybu koček';

--
-- Dumping data for table `kis__kocky_pohyb`
--

INSERT INTO `kis__kocky_pohyb` (`FK_zivot_id`, `FK_teritorium_id`, `cas_pohybu_od`, `cas_pohybu_do`) VALUES
(1, 1, '1981-07-10 00:00:00', '1981-07-11 00:00:00'),
(1, 2, '1981-10-27 00:00:00', '1981-10-28 00:00:00'),
(1, 5, '1981-09-09 00:00:00', '1981-09-10 00:00:00'),
(2, 3, '1986-11-21 00:00:00', '1986-11-23 00:00:00'),
(2, 7, '1988-03-26 00:00:00', '1988-03-30 00:00:00'),
(1, 2, '1989-11-02 00:00:00', '1989-11-04 00:00:00'),
(2, 3, '1984-05-08 00:00:00', '1984-05-10 00:00:00'),
(3, 1, '1984-08-27 00:00:00', '1984-09-29 00:00:00'),
(3, 2, '1985-03-05 00:00:00', '1985-04-07 00:00:00'),
(4, 4, '1988-06-23 00:00:00', '1988-06-24 00:00:00'),
(2, 3, '1985-05-11 00:00:00', '1985-05-12 00:00:00'),
(1, 1, '1988-09-26 00:00:00', '1988-11-28 00:00:00'),
(3, 1, '1989-06-13 00:00:00', '1989-08-07 00:00:00'),
(4, 6, '1991-12-17 00:00:00', '1992-06-18 00:00:00'),
(5, 3, '1995-12-30 00:00:00', '1995-12-31 00:00:00'),
(4, 4, '1993-04-04 00:00:00', '1993-05-08 00:00:00'),
(2, 7, '1996-12-11 00:00:00', '1997-01-07 00:00:00'),
(3, 5, '1985-11-15 00:00:00', '1985-12-18 00:00:00'),
(1, 5, '1985-07-04 00:00:00', '1985-07-17 00:00:00'),
(6, 2, '2002-01-06 00:00:00', '2002-01-15 00:00:00'),
(4, 4, '1997-07-31 00:00:00', '1997-11-10 00:00:00'),
(2, 3, '2001-03-31 00:00:00', '2001-04-20 00:00:00'),
(3, 9, '2001-07-30 00:00:00', '2001-08-19 00:00:00'),
(1, 2, '1989-07-04 00:00:00', '1989-07-21 00:00:00'),
(5, 3, '1992-01-17 00:00:00', '1992-01-25 00:00:00'),
(7, 4, '2004-03-02 00:00:00', '2004-03-23 00:00:00'),
(6, 2, '2002-08-03 00:00:00', '2002-08-11 00:00:00'),
(5, 3, '1996-11-13 00:00:00', '1996-11-18 00:00:00'),
(4, 4, '2006-09-29 00:00:00', '2006-10-08 00:00:00'),
(3, 1, '2000-12-13 00:00:00', '2000-12-22 00:00:00'),
(1, 9, '1992-06-18 00:00:00', '1992-07-24 00:00:00'),
(2, 3, '2003-10-19 00:00:00', '2003-11-08 00:00:00'),
(8, 6, '2013-08-15 00:00:00', '2013-08-23 00:00:00'),
(9, 4, '2017-02-11 00:00:00', '2017-02-19 00:00:00'),
(5, 10, '1997-10-03 00:00:00', '1997-12-11 00:00:00'),
(6, 5, '2005-06-05 00:00:00', '2005-06-11 00:00:00'),
(4, 6, '2006-10-21 00:00:00', '2006-10-31 00:00:00'),
(1, 5, '1999-03-24 00:00:00', '1999-04-14 00:00:00'),
(10, 9, '2013-10-20 00:00:00', '2013-10-25 00:00:00'),
(8, 6, '2008-01-14 00:00:00', '2008-03-25 00:00:00'),
(5, 7, '1998-05-05 00:00:00', '1998-05-10 00:00:00'),
(2, 7, '2003-02-24 00:00:00', '2003-03-06 00:00:00'),
(7, 6, '2007-01-18 00:00:00', '2007-01-22 00:00:00'),
(3, 5, '2001-06-14 00:00:00', '2001-06-21 00:00:00'),
(11, 5, '2017-01-08 00:00:00', '2017-02-13 00:00:00'),
(12, 9, '2013-12-09 00:00:00', '2013-12-14 00:00:00'),
(8, 6, '2012-05-26 00:00:00', '2012-06-29 00:00:00'),
(6, 1, '2016-05-05 00:00:00', '2016-05-24 00:00:00'),
(7, 4, '2009-08-16 00:00:00', '2009-08-20 00:00:00'),
(13, 1, '2012-02-17 00:00:00', '2012-02-20 00:00:00'),
(3, 2, '2000-02-15 00:00:00', '2000-02-19 00:00:00'),
(7, 4, '2002-08-03 00:00:00', '2002-09-15 00:00:00'),
(7, 6, '2001-06-12 00:00:00', '2002-01-01 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `kis__kocky_vlastnictvi`
--

CREATE TABLE IF NOT EXISTS `kis__kocky_vlastnictvi` (
  `FK_zivot_id` int(10) unsigned NOT NULL COMMENT 'ID Života',
  `FK_vec_id` int(10) unsigned NOT NULL COMMENT 'ID Věci',
  `cas_od` datetime NOT NULL,
  `cas_do` datetime DEFAULT NULL,
  `pocet` smallint(5) unsigned NOT NULL,
  KEY `FK_kocka_id` (`FK_zivot_id`),
  KEY `FK_vec_id` (`FK_vec_id`),
  KEY `FK_zivot_id` (`FK_zivot_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Obsahuje aktuální i předešlá vlastnictví koček';

--
-- Dumping data for table `kis__kocky_vlastnictvi`
--

INSERT INTO `kis__kocky_vlastnictvi` (`FK_zivot_id`, `FK_vec_id`, `cas_od`, `cas_do`, `pocet`) VALUES
(1, 1, '1981-09-09 00:00:00', NULL, 20),
(2, 1, '1988-03-26 00:00:00', NULL, 60),
(3, 2, '1984-08-27 00:00:00', NULL, 6),
(4, 3, '1988-06-23 00:00:00', NULL, 2),
(5, 4, '1995-12-30 00:00:00', NULL, 2),
(6, 5, '1995-12-30 00:00:00', NULL, 8),
(7, 5, '1984-08-27 00:00:00', NULL, 8),
(8, 6, '2002-01-06 00:00:00', NULL, 14),
(9, 7, '2004-03-02 00:00:00', NULL, 34),
(10, 7, '2013-10-20 00:00:00', NULL, 29),
(11, 7, '2012-02-17 00:00:00', NULL, 17),
(12, 8, '2017-01-08 00:00:00', NULL, 134),
(13, 8, '2002-01-06 00:00:00', NULL, 218),
(3, 9, '1995-12-30 00:00:00', NULL, 120),
(4, 9, '1985-03-05 00:00:00', NULL, 3),
(5, 10, '2017-01-08 00:00:00', NULL, 40),
(8, 10, '2013-12-09 00:00:00', NULL, 14),
(9, 11, '2002-01-06 00:00:00', NULL, 20),
(1, 12, '1989-06-13 00:00:00', NULL, 13),
(11, 13, '1995-12-30 00:00:00', NULL, 2),
(13, 14, '2012-02-17 00:00:00', NULL, 138),
(11, 15, '2013-10-20 00:00:00', NULL, 3),
(10, 15, '1985-11-15 00:00:00', NULL, 5),
(1, 16, '2004-03-02 00:00:00', NULL, 4),
(3, 16, '1985-07-04 00:00:00', NULL, 3),
(8, 17, '1997-07-31 00:00:00', NULL, 6),
(9, 18, '1997-07-31 00:00:00', NULL, 3),
(7, 19, '2001-07-30 00:00:00', NULL, 12),
(1, 19, '1985-11-15 00:00:00', NULL, 25),
(4, 20, '1992-01-17 00:00:00', NULL, 11);

-- --------------------------------------------------------

--
-- Table structure for table `kis__rasy`
--

CREATE TABLE IF NOT EXISTS `kis__rasy` (
  `rasa_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Rasy',
  `jmeno` varchar(30) NOT NULL,
  `misto_puvodu` varchar(60) NOT NULL,
  `delka_tesaku_max` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  `delka_drapu_max` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  `delka_srsti_max` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  PRIMARY KEY (`rasa_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje rasy koček' AUTO_INCREMENT=4 ;

--
-- Dumping data for table `kis__rasy`
--

INSERT INTO `kis__rasy` (`rasa_id`, `jmeno`, `misto_puvodu`, `delka_tesaku_max`, `delka_drapu_max`, `delka_srsti_max`) VALUES
(1, 'Kenta', 'Whyria ZE31', 22, 18, 8),
(2, 'Mamoru', 'Thonoe 28QX', 25, 30, 14),
(3, 'Kiyoshi', 'Trara 74Y', 38, 34, 20);

-- --------------------------------------------------------

--
-- Table structure for table `kis__teritoria`
--

CREATE TABLE IF NOT EXISTS `kis__teritoria` (
  `teritorium_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Teritoria',
  `nazev` varchar(30) NOT NULL,
  `typ` varchar(20) NOT NULL,
  `misto` varchar(40) NOT NULL,
  `latitude` float NOT NULL,
  `longitude` float NOT NULL,
  PRIMARY KEY (`teritorium_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje nalezená teritoria' AUTO_INCREMENT=11 ;

--
-- Dumping data for table `kis__teritoria`
--

INSERT INTO `kis__teritoria` (`teritorium_id`, `nazev`, `typ`, `misto`, `latitude`, `longitude`) VALUES
(1, 'Orchard Meadows', 'PARK', 'Thonoe 22', 44.3108, 166.979),
(2, 'Vineyard Plaza', 'PLAZA', 'Thonoe 22', 9.81134, -104.315),
(3, 'Windy Oaks Farm', 'FARM', 'Eploria', 24.8069, 120.881),
(4, 'Pine Valley Vineyard', 'FARM', 'Zoazuno', 70.6829, 50.5478),
(5, 'Dark Thicket', 'FOREST', 'Thonoe 22', -26.7605, -37.219),
(6, 'Frish Woods', 'FOREST', 'Zoazuno', -82.2492, 97.9883),
(7, 'Sand Cove Meadows', 'PARK', 'Eploria', 31.921, -58.3686),
(8, 'Wolf Point Garden', 'PARK', 'Zoazuno', -6.52207, 49.0228),
(9, 'Southern Kingfisher Wood', 'FOREST', 'Thonoe 22', -36.0858, -84.1188),
(10, 'Cherry Blossom Square', 'PLAZA', 'Eploria', 37.9083, 73.5336);

-- --------------------------------------------------------

--
-- Table structure for table `kis__veci`
--

CREATE TABLE IF NOT EXISTS `kis__veci` (
  `vec_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Věci',
  `FK_teritorium_id` int(10) unsigned NOT NULL COMMENT 'ID Teritoria',
  `nazev` varchar(30) NOT NULL,
  `pocet` smallint(5) unsigned NOT NULL,
  PRIMARY KEY (`vec_id`),
  UNIQUE KEY `FK_teritorium_id_nazev` (`FK_teritorium_id`,`nazev`),
  KEY `FK_teritorium_id` (`FK_teritorium_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje věci v rámci daných teritorií' AUTO_INCREMENT=21 ;

--
-- Dumping data for table `kis__veci`
--

INSERT INTO `kis__veci` (`vec_id`, `FK_teritorium_id`, `nazev`, `pocet`) VALUES
(1, 1, 'A leaf', 154),
(2, 4, 'A chair', 12),
(3, 3, 'A bench', 5),
(4, 2, 'A laptop', 2),
(5, 3, 'A stone', 16),
(6, 4, 'A stone', 14),
(7, 5, 'A tree', 1389),
(8, 6, 'A tree', 974),
(9, 6, 'A leaf', 488),
(10, 7, 'A stone', 84),
(11, 8, 'A stone', 43),
(12, 9, 'A stone', 76),
(13, 10, 'A laptop', 3),
(14, 9, 'A tree', 138),
(15, 8, 'A blanket', 3),
(16, 1, 'A bench', 7),
(17, 10, 'A chair', 18),
(18, 10, 'A table', 6),
(19, 2, 'A chair', 48),
(20, 2, 'A table', 12);

-- --------------------------------------------------------

--
-- Table structure for table `kis__zivoty`
--

CREATE TABLE IF NOT EXISTS `kis__zivoty` (
  `zivot_id` int(10) unsigned NOT NULL AUTO_INCREMENT COMMENT 'ID Života',
  `FK_kocka_id` int(10) unsigned NOT NULL COMMENT 'ID Kočky',
  `barva_oci` varchar(20) NOT NULL,
  `barva_srsti` varchar(20) NOT NULL,
  `delka_tesaku` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  `delka_drapu` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  `delka_srsti` smallint(5) unsigned NOT NULL COMMENT 'v mm',
  `datum_narozeni` datetime NOT NULL,
  `misto_narozeni` varchar(60) NOT NULL,
  `datum_umrti` datetime DEFAULT NULL,
  `misto_umrti` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`zivot_id`),
  KEY `FK_kocka_id` (`FK_kocka_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COMMENT='Obsahuje probíhající a již proběhlé životy koček' AUTO_INCREMENT=14 ;

--
-- Dumping data for table `kis__zivoty`
--

INSERT INTO `kis__zivoty` (`zivot_id`, `FK_kocka_id`, `barva_oci`, `barva_srsti`, `delka_tesaku`, `delka_drapu`, `delka_srsti`, `datum_narozeni`, `misto_narozeni`, `datum_umrti`, `misto_umrti`) VALUES
(1, 1, '134,54,68,1', '190,190,190,1', 18, 16, 6, '1981-02-19 01:16:00', 'Whyria ZE31', '2000-11-08 07:07:59', 'Thonoe 22'),
(2, 2, '215,154,36,1', '168,168,186,1', 22, 23, 12, '1983-02-03 02:16:00', 'Thonoe 28QX', '2008-07-21 16:27:59', 'Eploria'),
(3, 3, '15,19,250,1', '210,128,128,1', 32, 27, 19, '1983-02-13 17:18:00', 'Trara 74Y', '2002-11-18 05:19:59', 'Thonoe 22'),
(4, 4, '34,97,168,1', '128,231,127,1', 34, 25, 18, '1987-11-08 23:30:00', 'Thonoe 28QX', '2013-10-17 17:39:59', 'Zoazuno'),
(5, 5, '197,67,22,1', '255,255,255,1', 22, 28, 10, '1994-10-21 04:36:00', 'Trara 74Y', '2001-02-07 12:57:59', 'Eploria'),
(6, 6, '48,123,97,1', '134,9,134,1', 32, 32, 7, '1999-11-15 15:56:00', 'Trara 74Y', NULL, NULL),
(7, 1, '79,46,210,1', '43,43,210,1', 13, 13, 7, '2000-11-08 07:08:00', 'Whyria ZE31', '2010-03-30 02:25:59', 'Zoazuno'),
(8, 5, '180,58,76,1', '192,83,83,1', 20, 28, 10, '2001-02-07 12:58:00', 'Trara 74Y', NULL, NULL),
(9, 3, '81,43,158,1', '14,175,14,1', 20, 23, 12, '2002-11-18 05:20:00', 'Trara 74Y', NULL, NULL),
(10, 2, '21,46,98,1', '37,85,37,1', 18, 16, 13, '2008-07-21 16:28:00', 'Thonoe 28QX', '2017-01-27 02:59:59', 'Thonoe 22'),
(11, 1, '176,113,134,1', '93,136,192,1', 18, 8, 5, '2010-03-30 02:26:00', 'Whyria ZE31', NULL, NULL),
(12, 4, '8,107,238,1', '73,78,186,1', 17, 30, 16, '2013-10-17 17:40:00', 'Thonoe 28QX', NULL, NULL),
(13, 2, '137,14,79,1', '127,127,127,1', 31, 23, 8, '2017-01-27 03:00:00', 'Thonoe 28QX', NULL, NULL);

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kis__hostitele_preference`
--
ALTER TABLE `kis__hostitele_preference`
  ADD CONSTRAINT `kis__hostitele_preference_ibfk_3` FOREIGN KEY (`FK_hostitel_id`) REFERENCES `kis__hostitele` (`hostitel_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kis__hostitele_preference_ibfk_4` FOREIGN KEY (`FK_rasa_id`) REFERENCES `kis__rasy` (`rasa_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kis__kocky_hostitele`
--
ALTER TABLE `kis__kocky_hostitele`
  ADD CONSTRAINT `kis__kocky_hostitele_ibfk_3` FOREIGN KEY (`FK_zivot_id`) REFERENCES `kis__zivoty` (`zivot_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kis__kocky_hostitele_ibfk_4` FOREIGN KEY (`FK_hostitel_id`) REFERENCES `kis__hostitele` (`hostitel_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kis__kocky_pohyb`
--
ALTER TABLE `kis__kocky_pohyb`
  ADD CONSTRAINT `kis__kocky_pohyb_ibfk_3` FOREIGN KEY (`FK_zivot_id`) REFERENCES `kis__zivoty` (`zivot_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kis__kocky_pohyb_ibfk_4` FOREIGN KEY (`FK_teritorium_id`) REFERENCES `kis__teritoria` (`teritorium_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kis__kocky_vlastnictvi`
--
ALTER TABLE `kis__kocky_vlastnictvi`
  ADD CONSTRAINT `kis__kocky_vlastnictvi_ibfk_3` FOREIGN KEY (`FK_zivot_id`) REFERENCES `kis__zivoty` (`zivot_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `kis__kocky_vlastnictvi_ibfk_4` FOREIGN KEY (`FK_vec_id`) REFERENCES `kis__veci` (`vec_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kis__veci`
--
ALTER TABLE `kis__veci`
  ADD CONSTRAINT `kis__veci_ibfk_2` FOREIGN KEY (`FK_teritorium_id`) REFERENCES `kis__teritoria` (`teritorium_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `kis__zivoty`
--
ALTER TABLE `kis__zivoty`
  ADD CONSTRAINT `kis__zivoty_ibfk_2` FOREIGN KEY (`FK_kocka_id`) REFERENCES `kis__kocky` (`kocka_id`) ON DELETE CASCADE ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
