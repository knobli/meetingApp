-- phpMyAdmin SQL Dump
-- version 4.2.3
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 03. Jun 2015 um 20:48
-- Server Version: 5.5.40
-- PHP-Version: 5.3.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `meetingApp`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_Accounts`
--

CREATE TABLE IF NOT EXISTS `tbl_Accounts` (
`Account_ID` int(11) NOT NULL,
  `Username` varchar(30) CHARACTER SET latin1 NOT NULL,
  `SaltedPassword` char(60) COLLATE utf8_unicode_ci DEFAULT NULL,
  `FK_Mitglied` int(11) DEFAULT NULL,
  `Erfasst` datetime NOT NULL,
  `Last_Login` datetime DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=216 ;

--
-- Daten für Tabelle `tbl_Accounts`
--

INSERT INTO `tbl_Accounts` (`Account_ID`, `Username`, `SaltedPassword`, `FK_Mitglied`, `Erfasst`, `Last_Login`) VALUES
(1, 'dummy', NULL, 0, '2013-05-08 00:00:00', NULL),
(6, 'knobli', '$2y$10$08dXNvrQ5gVjVzG8/eXUmezNFS1BptyIelFlElgLdCQT5NcHvjWHi', 201, '2011-08-13 06:27:28', '2015-04-06 11:55:42'),
(120, 'testUser', '$2y$10$08dXNvrQ5gVjVzG8/eXUmezNFS1BptyIelFlElgLdCQT5NcHvjWHi', 502, '2012-06-05 01:34:19', NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_Mitglieder`
--

CREATE TABLE IF NOT EXISTS `tbl_Mitglieder` (
`Mitglied_ID` int(11) NOT NULL,
  `Anrede` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Nachname` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Vorname` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Strasse` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Postfach` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `PLZ` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Ort` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Land` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Tel_P` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Tel_G` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Tel_N` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `e-mail1` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `e-mail2` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Geburtsdatum` datetime DEFAULT NULL,
  `Beruf` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Todesdatum` datetime DEFAULT NULL,
  `Besonderes` longtext COLLATE utf8_unicode_ci
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=875 ;

--
-- Daten für Tabelle `tbl_Mitglieder`
--

INSERT INTO `tbl_Mitglieder` (`Mitglied_ID`, `Anrede`, `Nachname`, `Vorname`, `Strasse`, `Postfach`, `PLZ`, `Ort`, `Land`, `Tel_P`, `Tel_G`, `Tel_N`, `e-mail1`, `e-mail2`, `Geburtsdatum`, `Beruf`, `Todesdatum`, `Besonderes`) VALUES
(0, NULL, '', NULL, NULL, NULL, '', '', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(201, 'Herr', 'Santschi', 'Raffael', 'Rietstrasse 5', '', '8317', 'Tagelswangen', 'Schweiz', '052-343 90 90', '', '079-269 06 70', 'raffael@santschi.ch', '', '1990-03-24 00:00:00', 'Informatiker', NULL, ''),
(502, 'Herr', 'Test', 'Muster', 'Asd', NULL, '8317', 'Tagelswangen', NULL, NULL, NULL, NULL, 'dummy@huhu.ch', NULL, NULL, NULL, NULL, NULL),
(873, 'Herr', 'Max', 'Muster', 'Asd', NULL, '8317', 'Tagelswangen', NULL, NULL, NULL, NULL, 'dummy@huhu.ch', NULL, NULL, NULL, NULL, NULL),
(874, 'Herr', 'Max', 'Müller', 'Asd', NULL, '8317', 'Tagelswangen', NULL, NULL, NULL, NULL, 'dummy@huhu.ch', NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_Orte`
--

CREATE TABLE IF NOT EXISTS `tbl_Orte` (
`ID_Ort` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=latin1 AUTO_INCREMENT=265 ;

--
-- Daten für Tabelle `tbl_Orte`
--

INSERT INTO `tbl_Orte` (`ID_Ort`, `Name`) VALUES
(130, 'Turnhalle Eselriet, Effretikon'),
(131, 'Sportanlagen Deutweg, Winterthur'),
(132, 'Restaurant Frieden, Grafstal'),
(133, 'Sportanlagen, Ossingen'),
(134, 'Basel'),
(135, 'Sportanlagen Allmend, Frauenfeld'),
(136, 'Gemeinde Lindau'),
(137, 'Engadin (Maloja-Zuoz)'),
(138, 'Turnhalle Heiget, Fehraltorf'),
(139, 'Sportanlagen, Dinhard'),
(140, 'Klosters-Weissfluhjoch-Davos'),
(141, 'Schulhaus Buck, Tagelswangen'),
(142, 'Festgelände, Rheinau'),
(143, 'Sportanlagen, Elgg'),
(144, 'Schiessstand, Oberembrach'),
(145, 'Sportanlagen, Stammheim'),
(146, 'Sportplatz, Grafstal'),
(147, 'Sportanlagen, Wiesendangen'),
(148, 'Armbrutschiessstand Tagelswangen'),
(149, 'Sportanlagen Heiget, Brütten'),
(150, 'Sportanlagen, Solothurn'),
(151, 'Schiessstand, Lindau'),
(152, 'Sportanlagen, Bauma'),
(153, 'Allmend, Frauenfeld'),
(154, 'Skigebiet, Davos'),
(155, 'Sportanlagen, Weisslingen'),
(156, 'Sportanlagen, Studen'),
(157, 'Skigebiet Montafon'),
(158, 'Sportanlagen, Pfungen'),
(159, 'Sportanlagen, Andelfingen'),
(160, 'Sportanlagen, Fribourg'),
(161, 'Sportanlagen, Dägerlen'),
(162, 'Sportanlagen, Wädenswil'),
(163, 'Schwimmbad Grafstal'),
(164, 'Singsaal, Grafstal'),
(165, 'Sportanlagen, Russikon'),
(166, 'Pfadiheim Effretikon'),
(167, 'Mollis'),
(168, 'Glarus Süd'),
(169, 'Loipe Lendikon/Weisslingen'),
(170, 'Loipe First/Kyburg'),
(171, 'Marthalen'),
(172, 'Ossingen'),
(173, 'Biel'),
(174, 'Strickhof'),
(175, 'Gemeindehaus Lindau'),
(176, 'Grüsch-Danusa'),
(177, 'Bergün'),
(178, 'Sportanlagen Dürrbach, Dübendorf'),
(179, 'Rheinau'),
(180, 'Melchsee-Frutt'),
(181, 'Turnhallen Mettlen, Pfäffikon ZH'),
(182, 'Sportanlagen Hittnau'),
(183, 'Schulhaus Feld, Winterthur-Veltheim'),
(184, 'Schulhaus Watt, Effretikon'),
(185, 'Schulhaus Schlimpberg, Effretikon'),
(186, 'Schulhausanlagen Rosenau, Winterthur-Töss'),
(187, 'Appenzell'),
(188, 'Murgsee'),
(189, 'Schulhaus Rychenberg, Oberwinterthur'),
(190, 'Schulhaus Schönengrund, Winterthur-Töss'),
(191, 'Skigebiet Braunwald'),
(192, 'Schulanlagen Walenbach, Wetzikon'),
(193, 'Schulanlagen Schochen, Wila'),
(194, 'Schulhaus Grafstal'),
(195, 'Schulhaus Bachwis, Winterberg'),
(196, 'Engelberg'),
(197, 'Turnhalle Steinacker, Pfäffikon ZH'),
(198, 'Sportanlagen Ebni, Neftenbach'),
(199, 'Sportanlagen Seehalde, Niederhasli'),
(200, 'Sportanlagen, Buchs ZH'),
(201, 'Sportanlagen, Oberwinterthur'),
(202, 'Hohstuckli, Sattel'),
(203, 'Sportanlagen, Otelfingen'),
(204, 'Elm und Umgebung'),
(205, 'Kronberg, Jakobsbad'),
(206, 'Sportanlage Rennweg, Winterthur'),
(207, 'Hasliberg, BE'),
(208, 'Chilbiplatz Lindau'),
(209, 'FC Häussli'),
(210, 'Waldhüte Cheiberiet Illnau'),
(211, 'Mit Velo and die Töss'),
(212, 'Braunwald'),
(213, 'Hause Zimmermann'),
(214, 'Schweiz'),
(215, 'unbekannt'),
(216, 'Weisslingen'),
(217, 'Schulhaus Berg, Gossau ZH'),
(218, 'Effretikon Eishalle'),
(219, 'Restaurant Frieden Grafstal'),
(220, 'Sportzentrum Eselriet'),
(221, 'Brütten'),
(222, 'Grafstal'),
(223, 'Turnhalle Hüenerweid, Dietlikon'),
(224, 'Greifensee'),
(225, 'Schulhaus Rooswis, Gossau ZH'),
(226, 'Wetzikon (Halle 2)'),
(227, 'Bäretswil'),
(228, 'Wetzikon'),
(229, 'Eisbahn Dolder Zürich'),
(230, 'Effretikon'),
(231, 'Schulhaus Bachwis'),
(232, 'Schulhaus Buck'),
(233, 'Sportplatz Grafstal'),
(234, 'Russikon'),
(235, 'Armbrustschuetzenstand Tagelswangen'),
(236, 'Ralph Ernst AG // Tagelswangen'),
(237, 'Finnenbahn, Winterberg'),
(238, 'Kleinikon'),
(239, 'Deutweg Winterthur'),
(240, '???'),
(241, 'Sportzentrum Grindel'),
(242, 'Volleyballplatz Eschikon'),
(243, 'Bachwiis'),
(244, 'Lindau Gemeindeplatz'),
(245, 'Winterthur'),
(246, 'Effretikon (Hockey)'),
(247, 'Bachs'),
(248, 'Illnau '),
(249, 'Steinmaur'),
(250, 'Schulhaus Pfaffberg, Pfäffikon ZH'),
(251, 'Heuried Zürich'),
(252, 'Effretikon Aussen'),
(253, 'Effretikon Halle'),
(254, 'Rietstrasse 5, Tagelswangen'),
(255, 'Restaurant Thalegg'),
(256, 'Restaurant Riet, Tagelswangen'),
(258, 'Tagelswangenerstrasse 22, Lindau'),
(259, 'Brüllbier, Tagelswangen'),
(260, 'Kantonsschule Rychenberg'),
(261, 'Vereinsarchiv'),
(262, 'Im Ifang 9, Effretikon'),
(263, 'Fam. Zimmermann, Haldenstrasse, Lindau');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_Rechte`
--

CREATE TABLE IF NOT EXISTS `tbl_Rechte` (
`ID_Recht` int(11) NOT NULL,
  `Recht` varchar(30) CHARACTER SET latin1 NOT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=38 ;

--
-- Daten für Tabelle `tbl_Rechte`
--

INSERT INTO `tbl_Rechte` (`ID_Recht`, `Recht`) VALUES
(1, 'Login'),
(2, 'Admin'),
(3, 'Sitzung_mod');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_Sitzungen`
--

CREATE TABLE IF NOT EXISTS `tbl_Sitzungen` (
`ID` int(11) NOT NULL,
  `Anhang` varchar(100) CHARACTER SET utf8 DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2024 ;

--
-- Daten für Tabelle `tbl_Sitzungen`
--

INSERT INTO `tbl_Sitzungen` (`ID`, `Anhang`) VALUES
(2004, ''),
(2005, ''),
(2006, ''),
(2007, ''),
(2008, ''),
(2009, ''),
(2010, ''),
(2011, ''),
(2012, ''),
(2013, ''),
(2014, ''),
(2015, ''),
(2016, ''),
(2017, ''),
(2018, ''),
(2019, ''),
(2020, ''),
(2021, ''),
(2022, ''),
(2023, '');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `tbl_TerminObjekte`
--

CREATE TABLE IF NOT EXISTS `tbl_TerminObjekte` (
`ID` int(11) NOT NULL,
  `Typ` varchar(20) CHARACTER SET latin1 NOT NULL,
  `Name` varchar(100) CHARACTER SET latin1 NOT NULL,
  `Beschreibung` text CHARACTER SET latin1 NOT NULL,
  `Start` datetime NOT NULL,
  `Ende` datetime NOT NULL,
  `FK_Verantwortlicher` int(11) DEFAULT NULL,
  `FK_Ort` int(11) DEFAULT NULL
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2024 ;

--
-- Daten für Tabelle `tbl_TerminObjekte`
--

INSERT INTO `tbl_TerminObjekte` (`ID`, `Typ`, `Name`, `Beschreibung`, `Start`, `Ende`, `FK_Verantwortlicher`, `FK_Ort`) VALUES
(2004, 'Meeting', 'Test', 'asdfasdf', '2015-04-13 12:38:00', '2015-04-14 12:38:00', 201, 132),
(2005, 'Meeting', 'Test', 'asdfasdf', '2015-04-13 12:38:00', '2015-04-14 12:38:00', 201, 132),
(2006, 'Meeting', 'Test', '', '2015-04-14 18:00:00', '2015-04-14 21:40:00', 201, 141),
(2007, 'Meeting', 'Test', '', '2015-04-14 18:00:00', '2015-04-14 21:40:00', 201, 141),
(2008, 'Meeting', 'Test', '', '2015-04-14 18:00:00', '2015-04-14 21:40:00', 201, 141),
(2009, 'Meeting', 'Test it', '', '2015-04-14 21:00:00', '2015-04-14 22:50:00', 201, 141),
(2010, 'Meeting', 'Test Meeting', '', '2016-05-26 16:00:00', '2016-05-26 18:00:00', 201, 141),
(2011, 'Meeting', 'The final Meeting', '', '2016-05-26 19:00:00', '2016-05-26 23:00:00', 201, 132),
(2012, 'Meeting', 'Test Meeting', '', '2015-06-04 21:30:30', '2015-06-14 13:30:30', 201, 141),
(2013, 'Meeting', 'Test Meeting', '', '2015-06-04 21:30:30', '2015-06-14 13:30:30', 201, 141),
(2014, 'Meeting', 'Test', '', '2015-06-05 20:20:21', '2015-06-06 09:20:21', 201, 141),
(2015, 'Meeting', 'Test', '', '2015-06-03 19:20:59', '2015-06-03 21:20:59', 201, 141),
(2016, 'Meeting', 'Test', '', '2015-06-11 18:30:07', '2015-06-11 17:30:00', 201, 141),
(2017, 'Meeting', 'Test', '', '2015-06-11 18:30:07', '2015-06-11 17:30:00', 201, 141),
(2018, 'Meeting', 'Test', '', '2015-06-11 18:30:07', '2015-06-11 17:30:00', 201, 141),
(2019, 'Meeting', 'Test', '', '2015-06-11 18:30:07', '2015-06-11 17:30:00', 201, 141),
(2020, 'Meeting', 'Test Meeting', '', '2015-06-07 21:50:49', '2015-06-07 22:50:49', 502, 141),
(2021, 'Meeting', 'Test Meeting', '', '2014-10-02 09:10:49', '2014-09-01 01:50:50', 502, 141),
(2022, 'Meeting', 'Besprechnung', '', '2015-06-09 19:50:49', '2015-06-09 20:50:49', 502, 146),
(2023, 'Meeting', 'OK-Sitzung', '', '2015-07-07 19:00:00', '2015-07-07 21:30:00', 502, 142);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vtbl_Account_Recht`
--

CREATE TABLE IF NOT EXISTS `vtbl_Account_Recht` (
`ID` int(11) NOT NULL,
  `FK_Account` int(11) NOT NULL,
  `FK_Recht` int(11) NOT NULL DEFAULT '0'
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=972 ;

--
-- Daten für Tabelle `vtbl_Account_Recht`
--

INSERT INTO `vtbl_Account_Recht` (`ID`, `FK_Account`, `FK_Recht`) VALUES
(1, 6, 1),
(2, 6, 2),
(3, 6, 3),
(4, 120, 1);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `vtbl_Mitglied_Termin`
--

CREATE TABLE IF NOT EXISTS `vtbl_Mitglied_Termin` (
  `FK_Mitglied` int(11) NOT NULL,
  `FK_Termin` int(11) NOT NULL,
  `Bemerkung` text NOT NULL,
  `Status` tinyint(2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Daten für Tabelle `vtbl_Mitglied_Termin`
--

INSERT INTO `vtbl_Mitglied_Termin` (`FK_Mitglied`, `FK_Termin`, `Bemerkung`, `Status`) VALUES
(201, 2021, '', 3),
(502, 2020, 'Sign in from demo app', 1),
(502, 2021, '', 3),
(502, 2022, '', 3),
(502, 2023, '', 3);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `tbl_Accounts`
--
ALTER TABLE `tbl_Accounts`
 ADD PRIMARY KEY (`Account_ID`), ADD UNIQUE KEY `Username` (`Username`), ADD KEY `acc_fkmitglied` (`FK_Mitglied`);

--
-- Indexes for table `tbl_Mitglieder`
--
ALTER TABLE `tbl_Mitglieder`
 ADD PRIMARY KEY (`Mitglied_ID`);

--
-- Indexes for table `tbl_Orte`
--
ALTER TABLE `tbl_Orte`
 ADD PRIMARY KEY (`ID_Ort`);

--
-- Indexes for table `tbl_Rechte`
--
ALTER TABLE `tbl_Rechte`
 ADD PRIMARY KEY (`ID_Recht`);

--
-- Indexes for table `tbl_Sitzungen`
--
ALTER TABLE `tbl_Sitzungen`
 ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `tbl_TerminObjekte`
--
ALTER TABLE `tbl_TerminObjekte`
 ADD PRIMARY KEY (`ID`), ADD KEY `FK_Verantwortlicher` (`FK_Verantwortlicher`), ADD KEY `FK_Ort` (`FK_Ort`);

--
-- Indexes for table `vtbl_Account_Recht`
--
ALTER TABLE `vtbl_Account_Recht`
 ADD PRIMARY KEY (`ID`), ADD UNIQUE KEY `FK_Account_2` (`FK_Account`,`FK_Recht`), ADD KEY `FK_Account` (`FK_Account`), ADD KEY `vtbl_Account_Recht_ibfk_2` (`FK_Recht`);

--
-- Indexes for table `vtbl_Mitglied_Termin`
--
ALTER TABLE `vtbl_Mitglied_Termin`
 ADD UNIQUE KEY `FK_Mitglied` (`FK_Mitglied`,`FK_Termin`), ADD KEY `vtbl_mitglied_termin_ibfk_2` (`FK_Termin`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `tbl_Accounts`
--
ALTER TABLE `tbl_Accounts`
MODIFY `Account_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=216;
--
-- AUTO_INCREMENT for table `tbl_Mitglieder`
--
ALTER TABLE `tbl_Mitglieder`
MODIFY `Mitglied_ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=875;
--
-- AUTO_INCREMENT for table `tbl_Orte`
--
ALTER TABLE `tbl_Orte`
MODIFY `ID_Ort` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=265;
--
-- AUTO_INCREMENT for table `tbl_Rechte`
--
ALTER TABLE `tbl_Rechte`
MODIFY `ID_Recht` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=38;
--
-- AUTO_INCREMENT for table `tbl_Sitzungen`
--
ALTER TABLE `tbl_Sitzungen`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2024;
--
-- AUTO_INCREMENT for table `tbl_TerminObjekte`
--
ALTER TABLE `tbl_TerminObjekte`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=2024;
--
-- AUTO_INCREMENT for table `vtbl_Account_Recht`
--
ALTER TABLE `vtbl_Account_Recht`
MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=972;
--
-- Constraints der exportierten Tabellen
--

--
-- Constraints der Tabelle `tbl_Accounts`
--
ALTER TABLE `tbl_Accounts`
ADD CONSTRAINT `fk_memberId` FOREIGN KEY (`FK_Mitglied`) REFERENCES `tbl_Mitglieder` (`Mitglied_ID`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `tbl_Sitzungen`
--
ALTER TABLE `tbl_Sitzungen`
ADD CONSTRAINT `tbl_Sitzungen_ibfk_1` FOREIGN KEY (`ID`) REFERENCES `tbl_TerminObjekte` (`ID`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints der Tabelle `tbl_TerminObjekte`
--
ALTER TABLE `tbl_TerminObjekte`
ADD CONSTRAINT `tbl_TerminObjekte_ibfk_1` FOREIGN KEY (`FK_Verantwortlicher`) REFERENCES `tbl_Mitglieder` (`Mitglied_ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `tbl_TerminObjekte_ibfk_2` FOREIGN KEY (`FK_Ort`) REFERENCES `tbl_Orte` (`ID_Ort`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `vtbl_Account_Recht`
--
ALTER TABLE `vtbl_Account_Recht`
ADD CONSTRAINT `vtbl_Account_Recht_ibfk_1` FOREIGN KEY (`FK_Account`) REFERENCES `tbl_Accounts` (`Account_ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `vtbl_Account_Recht_ibfk_2` FOREIGN KEY (`FK_Recht`) REFERENCES `tbl_Rechte` (`ID_Recht`) ON UPDATE CASCADE;

--
-- Constraints der Tabelle `vtbl_Mitglied_Termin`
--
ALTER TABLE `vtbl_Mitglied_Termin`
ADD CONSTRAINT `vtbl_Mitglied_Termin_ibfk_1` FOREIGN KEY (`FK_Mitglied`) REFERENCES `tbl_Mitglieder` (`Mitglied_ID`) ON UPDATE CASCADE,
ADD CONSTRAINT `vtbl_Mitglied_Termin_ibfk_2` FOREIGN KEY (`FK_Termin`) REFERENCES `tbl_TerminObjekte` (`ID`) ON UPDATE CASCADE;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
