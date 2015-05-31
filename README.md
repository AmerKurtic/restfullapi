# restfullapi


//get all domains
curl 127.168.0.1/restfullapi/domains/?apiKey=55
//get beeseweb.com info
curl 127.168.0.1/restfullapi/domains/beeseweb.com/?apiKey=55
//create new domain
curl -X POST -d @d:\domain.txt 127.168.0.1/restfullapi/domains/create/?apiKey=55 --header "Content-Type:application/json"
//update domain
curl -X PUT -d @d:\domainupdate.txt 127.168.0.1/restfullapi/domains/help.com/?apiKey=55 --header "Content-Type:application/json"



-- Database

-- phpMyAdmin SQL Dump
-- version 4.3.11
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Gegenereerd op: 31 mei 2015 om 21:17
-- Serverversie: 5.6.24
-- PHP-versie: 5.6.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";

--
-- Database: `api`
--

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `apikeys`
--

CREATE TABLE IF NOT EXISTS `apikeys` (
  `APIKEY` int(255) NOT NULL,
  `ORIGIN` varchar(255) COLLATE utf8_bin NOT NULL,
  `uID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `apikeys`
--

INSERT INTO `apikeys` (`APIKEY`, `ORIGIN`, `uID`) VALUES
(55, '127.168.0.1', 1);

-- --------------------------------------------------------

--
-- Tabelstructuur voor tabel `domain`
--

CREATE TABLE IF NOT EXISTS `domain` (
  `dID` int(22) NOT NULL,
  `domain` varchar(255) COLLATE utf8_bin NOT NULL,
  `uID` int(11) NOT NULL,
  `startDate` varchar(255) COLLATE utf8_bin NOT NULL,
  `contractTerm` varchar(255) COLLATE utf8_bin NOT NULL,
  `montlyPrice` int(11) NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_bin;

--
-- Gegevens worden geëxporteerd voor tabel `domain`
--

INSERT INTO `domain` (`dID`, `domain`, `uID`, `startDate`, `contractTerm`, `montlyPrice`) VALUES
(1, 'beeseweb.com', 1, 'APR 1, 2015', '2 year', 35),
(2, 'deeseweb.com', 1, 'APR 5, 2015', '1 year', 25),
(4, 'test.com', 1, 'APR 8, 2015', '3 years', 77),
(5, 'test.com', 1, 'APR 8, 2015', '3 years', 77),
(6, 'help.com', 1, 'APR 7, 2015', '5 years', 22);

--
-- Indexen voor geëxporteerde tabellen
--

--
-- Indexen voor tabel `apikeys`
--
ALTER TABLE `apikeys`
  ADD PRIMARY KEY (`APIKEY`);

--
-- Indexen voor tabel `domain`
--
ALTER TABLE `domain`
  ADD PRIMARY KEY (`dID`);

--
-- AUTO_INCREMENT voor geëxporteerde tabellen
--

--
-- AUTO_INCREMENT voor een tabel `domain`
--
ALTER TABLE `domain`
  MODIFY `dID` int(22) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=7;