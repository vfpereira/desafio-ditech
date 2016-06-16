-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2016 at 05:51 AM
-- Server version: 5.7.9
-- PHP Version: 5.6.16

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `symfony`
--

-- --------------------------------------------------------

--
-- Table structure for table `horario`
--

DROP TABLE IF EXISTS `horario`;
CREATE TABLE IF NOT EXISTS `horario` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `sala_Id` int(11) NOT NULL,
  `usuario_Id` int(11) NOT NULL,
  `Horario` varchar(2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `salaId` (`sala_Id`),
  KEY `usuarioId` (`usuario_Id`),
  KEY `id` (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=35 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `horario`
--

INSERT INTO `horario` (`id`, `sala_Id`, `usuario_Id`, `Horario`) VALUES
(34, 6, 25, '0');

-- --------------------------------------------------------

--
-- Table structure for table `sala`
--

DROP TABLE IF EXISTS `sala`;
CREATE TABLE IF NOT EXISTS `sala` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(256) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sala`
--

INSERT INTO `sala` (`ID`, `NAME`) VALUES
(6, 'Sala2'),
(7, 'Sala3');

-- --------------------------------------------------------

--
-- Table structure for table `usuario`
--

DROP TABLE IF EXISTS `usuario`;
CREATE TABLE IF NOT EXISTS `usuario` (
  `ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(256) NOT NULL,
  `LASTNAME` varchar(256) NOT NULL,
  `PASSWORD` varchar(256) NOT NULL,
  `EMAIL` varchar(256) NOT NULL,
  PRIMARY KEY (`ID`),
  UNIQUE KEY `ID` (`ID`),
  UNIQUE KEY `EMAIL` (`EMAIL`),
  KEY `ID_2` (`ID`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=latin1;

--
-- Dumping data for table `usuario`
--

INSERT INTO `usuario` (`ID`, `NAME`, `LASTNAME`, `PASSWORD`, `EMAIL`) VALUES
(25, 'Vagner', 'Franco', '1234', 'vagner@gmail.com');

--
-- Constraints for dumped tables
--

--
-- Constraints for table `horario`
--
ALTER TABLE `horario`
  ADD CONSTRAINT `FKSALAID` FOREIGN KEY (`sala_Id`) REFERENCES `sala` (`ID`),
  ADD CONSTRAINT `FKUSERID` FOREIGN KEY (`usuario_Id`) REFERENCES `usuario` (`ID`);

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
