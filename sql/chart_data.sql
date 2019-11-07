-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 10, 2018 at 09:49 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.0.26


--
-- Dumping data for table `song`
--
DELETE FROM setList2;

DELETE FROM gig;
DELETE FROM efilePart;

DELETE FROM efile;


DELETE FROM efileType;
DELETE FROM note;
DELETE FROM publication;

DELETE FROM category;
DELETE FROM band;
DELETE FROM arrangement;


DELETE FROM song;
INSERT INTO `song` (`songID`, `name`) VALUES
(1, 'Work Song'),
(2, 'Zoot Suit Riot');

-- --------------------------------------------------------

DELETE FROM person;

INSERT INTO `person` (`personID`, `firstName`, `lastName`, `nickName`) VALUES
(1, 'First1', 'Last1', NULL),
(2, 'First2', 'Last2', NULL);

--
-- Dumping data for table `part`
--

DELETE FROM part;

INSERT INTO `part` (`partID`, `name`, `minSectionID`) VALUES
(1, 'Trumpet 1', 1),
(2, 'Trumpet 2', 1),
(3, 'Trumpet 3', 1),
(4, 'Trumpet 4', 1),
(5, 'Trombone 1', 2),
(6, 'Trombone 2', 2),
(7, 'Trombone 3', 2),
(8, 'Trombone 4', 2),
(9, 'Drums', 5),
(10, 'Guitar', 5),
(11, 'Bass', 5),
(12, 'Piano', 5),
(13, 'Alto Sax 1', 4),
(14, 'Alto Sax 2', 4),
(15, 'Tenor Sax 1', 4),
(16, 'Tenor Sax 2', 4),
(17, 'Baritone Sax', 4),
(18, 'Vocal Solo', 6),
(19, 'Vocal Soprano', 6),
(20, 'Vocal Alto', 6),
(21, 'Vocal Tenor', 6),
(22, 'Vocal Bass', 6),
(23, 'Score', 8),
(24, 'Vocal Group', 6),
(26, 'Flute', 4),
(27, 'Clarinet', 4),
(28, 'Tuba', 3),
(29, 'Synth', 5);

-- --------------------------------------------------------


--
-- Dumping data for table `arrangement`
--

INSERT INTO `arrangement` (`arrangementID`, `songID`, `arrangerPersonID`) VALUES
(1, 1, 1),
(2, 2, 2);



INSERT INTO `band` (`subSectionID`, `superSectionID`) VALUES
(1, 3),
(2, 3),
(3, 8),
(4, 8),
(5, 8),
(6, 7),
(8, 7);


INSERT INTO `category` (`categoryID`, `name`) VALUES
(1, '1940s'),
(2, 'Glenn Miller'),
(4, 'Quick Step'),
(3, 'Swing'),
(5, 'Waltz');

-- --------------------------------------------------------

--
-- Dumping data for table `publication`
--

INSERT INTO `publication` (`publicationID`, `arrangementID`, `description`) VALUES
(1, 1, 'Hand-written'),
(2, 1, 'Typed');

INSERT INTO `note` (`noteID`, `publicationID`, `noteDate`, `noteText`) VALUES
(1, 2, '2018-03-17 20:44:06', 'Tango 124');

INSERT INTO `efileType` (`efileTypeID`, `name`) VALUES
(1, 'pdf');

-- --------------------------------------------------------


--
-- Dumping data for table `efile`
--

INSERT INTO `efile` (`efileID`, `name`, `efileTypeID`, `publicationID`, `formatID`) VALUES
(13, 'ABlankFile.pdf', 1, 1, 0),
(14, 'AnotherBlankFile.pdf', 1, 1, 0);


--
-- Dumping data for table `efilePart`
--

INSERT INTO `efilePart` (`efilePartID`, `efileID`, `partID`, `startPage`, `endPage`) VALUES
(2, 14, 13, 2, 3),
(3, 14, 14, 4, 5),
(4, 14, 15, 6, 7),
(5, 14, 16, 8, 9),
(6, 14, 17, 10, 11),
(7, 14, 1, 12, 13),
(8, 14, 2, 14, 15),
(9, 14, 3, 16, 17),
(10, 14, 4, 18, 19),
(11, 14, 5, 20, 21),
(12, 14, 6, 22, 23),
(13, 14, 7, 24, 25),
(14, 14, 8, 26, 27),
(15, 14, 10, 28, 29),
(16, 14, 12, 30, 33),
(17, 14, 11, 34, 35);



--
-- Dumping data for table `gig`
--
INSERT INTO `gig` (`gigID`, `name`, `gigDate`, `isGig`) VALUES
(1, 'Location1', '2017-10-14', 1),
(2, 'Place2', '2017-06-30', 1);

-- --------------------------------------------------------

--
-- Table structure for table `locationType`
--


-- --------------------------------------------------------

-- --------------------------------------------------------

-- --------------------------------------------------------

-- Dumping data for table `setList2`
--

INSERT INTO `setList2` (`setListID`, `arrangementID`, `gigID`, `setListOrder`) VALUES
(1, 1, 1, 13.000),
(2, 2, 1, 150.000);

-- --------------------------------------------------------


