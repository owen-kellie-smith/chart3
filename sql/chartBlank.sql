-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Apr 11, 2018 at 06:20 PM
-- Server version: 10.1.31-MariaDB
-- PHP Version: 7.0.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Create a database and use it
CREATE DATABASE chart2;
USE chart2;

--
--  Create a user to whom the views (later on) can be attributed
GRANT ALL PRIVILEGES ON *.* TO 'chartUser'@'localhost' IDENTIFIED BY 'SomeSuitablePassword999!';

--  Create a user for travis and mysql-cred.php
GRANT ALL PRIVILEGES ON *.* TO 'makeUpAUserName'@'localhost' IDENTIFIED BY 'makeUpASatisfactoryPassword';
-- --------------------------------------------------------

--
-- Table structure for table `arrangement`
-- Table structure for table `arrangement`
--

DROP TABLE IF EXISTS `arrangement`;
CREATE TABLE IF NOT EXISTS `arrangement` (
  `arrangementID` int(11) NOT NULL AUTO_INCREMENT,
  `songID` int(11) NOT NULL,
  `arrangerPersonID` int(11) NOT NULL,
  `isInPads` tinyint(1) NOT NULL,
  `isBackedUp` tinyint(1) NOT NULL,
  PRIMARY KEY (`arrangementID`),
  UNIQUE KEY `songID` (`songID`,`arrangerPersonID`),
  KEY `arrangerPersonID` (`arrangerPersonID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=374 ;

-- --------------------------------------------------------

--
-- Table structure for table `arrangementCategory`
--

DROP TABLE IF EXISTS `arrangementCategory`;
CREATE TABLE IF NOT EXISTS `arrangementCategory` (
  `arrangementID` int(11) NOT NULL,
  `categoryID` int(11) NOT NULL,
  PRIMARY KEY (`arrangementID`,`categoryID`),
  KEY `categoryID` (`categoryID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `band`
--

DROP TABLE IF EXISTS `band`;
CREATE TABLE IF NOT EXISTS `band` (
  `subSectionID` int(11) NOT NULL,
  `superSectionID` int(11) NOT NULL,
  PRIMARY KEY (`subSectionID`,`superSectionID`),
  KEY `superSectionID` (`superSectionID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

DROP TABLE IF EXISTS `category`;
CREATE TABLE IF NOT EXISTS `category` (
  `categoryID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`categoryID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=6 ;

-- --------------------------------------------------------

--
-- Table structure for table `confirmation`
--

DROP TABLE IF EXISTS `confirmation`;
CREATE TABLE IF NOT EXISTS `confirmation` (
  `confirmationID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) DEFAULT NULL,
  `confirmationCode` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `tsbcode` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `IP` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`confirmationID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=342 ;

-- --------------------------------------------------------

--
-- Table structure for table `efile`
--

DROP TABLE IF EXISTS `efile`;
CREATE TABLE IF NOT EXISTS `efile` (
  `efileID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `efileTypeID` int(11) NOT NULL,
  `publicationID` int(11) NOT NULL,
  `formatID` int(11) NOT NULL,
  `arrangementID` int(11) DEFAULT NULL,
  `gigID` int(11) DEFAULT NULL,
  PRIMARY KEY (`efileID`),
  KEY `efileTypeID` (`efileTypeID`),
  KEY `publicationID` (`publicationID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=453 ;

-- --------------------------------------------------------

--
-- Table structure for table `efileLocation`
--

DROP TABLE IF EXISTS `efileLocation`;
CREATE TABLE IF NOT EXISTS `efileLocation` (
  `efileID` int(11) NOT NULL,
  `locationTypeID` int(11) NOT NULL,
  `location` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  UNIQUE KEY `location` (`location`),
  KEY `efileID` (`efileID`),
  KEY `locationTypeID` (`locationTypeID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `efilePart`
--

DROP TABLE IF EXISTS `efilePart`;
CREATE TABLE IF NOT EXISTS `efilePart` (
  `efilePartID` int(11) NOT NULL AUTO_INCREMENT,
  `efileID` int(11) NOT NULL,
  `partID` int(11) NOT NULL,
  `startPage` int(11) NOT NULL,
  `endPage` int(11) NOT NULL,
  PRIMARY KEY (`efilePartID`),
  KEY `efileID` (`efileID`),
  KEY `partID` (`partID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2230 ;

-- --------------------------------------------------------

--
-- Table structure for table `efileType`
--

DROP TABLE IF EXISTS `efileType`;
CREATE TABLE IF NOT EXISTS `efileType` (
  `efileTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`efileTypeID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=3 ;

-- --------------------------------------------------------

--
-- Table structure for table `gig`
--

DROP TABLE IF EXISTS `gig`;
CREATE TABLE IF NOT EXISTS `gig` (
  `gigID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `location` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `notes` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `gigDate` date NOT NULL,
  `sound` time DEFAULT NULL,
  `isGig` tinyint(1) DEFAULT NULL,
  `includesAll` tinyint(4) DEFAULT NULL,
  `isStyle` tinyint(4) NOT NULL,
  `updateTime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `hasWhere` tinyint(4) DEFAULT NULL,
  `whereText` varchar(300) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`gigID`),
  UNIQUE KEY `name` (`name`,`gigDate`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=181 ;

-- --------------------------------------------------------

--
-- Table structure for table `gigJoin`
--

DROP TABLE IF EXISTS `gigJoin`;
CREATE TABLE IF NOT EXISTS `gigJoin` (
  `gigJoinOperator` text NOT NULL,
  `gigJoinID` int(11) NOT NULL AUTO_INCREMENT,
  PRIMARY KEY (`gigJoinID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=4 ;

-- --------------------------------------------------------

--
-- Table structure for table `image`
--

DROP TABLE IF EXISTS `image`;
CREATE TABLE IF NOT EXISTS `image` (
  `imageID` int(11) NOT NULL AUTO_INCREMENT,
  `path` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `filename` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `filetype` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `description` text COLLATE utf8_unicode_ci,
  `creatorPersonID` int(11) DEFAULT NULL,
  PRIMARY KEY (`imageID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=16 ;

-- --------------------------------------------------------

--
-- Table structure for table `locationType`
--

DROP TABLE IF EXISTS `locationType`;
CREATE TABLE IF NOT EXISTS `locationType` (
  `locationTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`locationTypeID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `note`
--

DROP TABLE IF EXISTS `note`;
CREATE TABLE IF NOT EXISTS `note` (
  `noteID` int(11) NOT NULL AUTO_INCREMENT,
  `publicationID` int(11) NOT NULL,
  `noteDate` datetime DEFAULT NULL,
  `noteText` text COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`noteID`),
  KEY `publicationID` (`publicationID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=76 ;

-- --------------------------------------------------------

--
-- Table structure for table `part`
--

DROP TABLE IF EXISTS `part`;
CREATE TABLE IF NOT EXISTS `part` (
  `partID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `minSectionID` int(11) DEFAULT '8',
  `shortName` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`partID`),
  UNIQUE KEY `name` (`name`),
  KEY `minSectionID` (`minSectionID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=30 ;

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

DROP TABLE IF EXISTS `person`;
CREATE TABLE IF NOT EXISTS `person` (
  `personID` int(11) NOT NULL AUTO_INCREMENT,
  `firstName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `lastName` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `nickName` varchar(30) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`personID`),
  UNIQUE KEY `firstName` (`firstName`,`lastName`,`nickName`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=990 ;

-- --------------------------------------------------------

--
-- Table structure for table `printList`
--

DROP TABLE IF EXISTS `printList`;
CREATE TABLE IF NOT EXISTS `printList` (
  `printListID` int(11) NOT NULL AUTO_INCREMENT,
  `userID` int(11) NOT NULL,
  PRIMARY KEY (`printListID`),
  KEY `userID` (`userID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=1 ;

-- --------------------------------------------------------

--
-- Table structure for table `publication`
--

DROP TABLE IF EXISTS `publication`;
CREATE TABLE IF NOT EXISTS `publication` (
  `publicationID` int(11) NOT NULL AUTO_INCREMENT,
  `arrangementID` int(11) NOT NULL,
  `description` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`publicationID`),
  UNIQUE KEY `description` (`description`,`arrangementID`),
  KEY `arrangementID` (`arrangementID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=149 ;

-- --------------------------------------------------------

--
-- Table structure for table `request`
--

DROP TABLE IF EXISTS `request`;
CREATE TABLE IF NOT EXISTS `request` (
  `requestID` int(11) NOT NULL AUTO_INCREMENT,
  `requestIP` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `requestWhen` date DEFAULT NULL,
  `requestGet` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`requestID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2985 ;

-- --------------------------------------------------------

--
-- Table structure for table `section`
--

DROP TABLE IF EXISTS `section`;
CREATE TABLE IF NOT EXISTS `section` (
  `sectionID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `printOrder` int(11) NOT NULL,
  `shortName` varchar(5) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`sectionID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=11 ;

-- --------------------------------------------------------

--
-- Table structure for table `setList2`
--

DROP TABLE IF EXISTS `setList2`;
CREATE TABLE IF NOT EXISTS `setList2` (
  `setListID` int(10) NOT NULL AUTO_INCREMENT,
  `arrangementID` int(11) NOT NULL,
  `gigID` int(11) NOT NULL,
  `setListOrder` decimal(11,3) NOT NULL,
  PRIMARY KEY (`setListID`),
  UNIQUE KEY `gigOrder` (`gigID`,`setListOrder`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=2327 ;

-- --------------------------------------------------------

--
-- Table structure for table `song`
--

DROP TABLE IF EXISTS `song`;
CREATE TABLE IF NOT EXISTS `song` (
  `songID` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`songID`),
  UNIQUE KEY `name` (`name`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=466 ;

-- --------------------------------------------------------

--
-- Table structure for table `songComposer`
--

DROP TABLE IF EXISTS `songComposer`;
CREATE TABLE IF NOT EXISTS `songComposer` (
  `songID` int(11) NOT NULL,
  `composerPersonID` int(11) NOT NULL,
  PRIMARY KEY (`songID`,`composerPersonID`),
  KEY `composerPersonID` (`composerPersonID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `url`
--

DROP TABLE IF EXISTS `url`;
CREATE TABLE IF NOT EXISTS `url` (
  `urlID` int(11) NOT NULL AUTO_INCREMENT,
  `urlurl` varchar(200) NOT NULL,
  `urlArrangementID` int(11) NOT NULL,
  `urlTSB` tinyint(4) NOT NULL,
  `urlTypeID` int(11) NOT NULL,
  `urlTitle` text,
  `urlYouTubeID` text,
  PRIMARY KEY (`urlID`),
  UNIQUE KEY `urlurl` (`urlurl`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=41 ;

-- --------------------------------------------------------

--
-- Table structure for table `urlType`
--

DROP TABLE IF EXISTS `urlType`;
CREATE TABLE IF NOT EXISTS `urlType` (
  `urlTypeID` int(11) NOT NULL AUTO_INCREMENT,
  `urlTypeName` varchar(100) NOT NULL,
  PRIMARY KEY (`urlTypeID`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 AUTO_INCREMENT=5 ;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
CREATE TABLE IF NOT EXISTS `user` (
  `userID` int(11) NOT NULL AUTO_INCREMENT,
  `md5email` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `nickName` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `aesEmail` blob,
  PRIMARY KEY (`userID`),
  UNIQUE KEY `md5email` (`md5email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci AUTO_INCREMENT=56 ;

-- --------------------------------------------------------

--
-- Stand-in structure for view `view_arrangement`
--
DROP VIEW IF EXISTS `view_arrangement`;
CREATE TABLE IF NOT EXISTS `view_arrangement` (
`arrangementID` int(11)
,`arrangerFirstName` varchar(30)
,`arrangerLastName` varchar(30)
,`name` varchar(100)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_arrangementCategory`
--
DROP VIEW IF EXISTS `view_arrangementCategory`;
CREATE TABLE IF NOT EXISTS `view_arrangementCategory` (
`arrangerLastName` varchar(30)
,`songName` varchar(100)
,`categoryName` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_band`
--
DROP VIEW IF EXISTS `view_band`;
CREATE TABLE IF NOT EXISTS `view_band` (
`Sub` varchar(30)
,`Super` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_countPartPage`
--
DROP VIEW IF EXISTS `view_countPartPage`;
CREATE TABLE IF NOT EXISTS `view_countPartPage` (
`countPage` decimal(34,0)
,`arrangementID` int(11)
,`partID` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_efile`
--
DROP VIEW IF EXISTS `view_efile`;
CREATE TABLE IF NOT EXISTS `view_efile` (
`formatID` int(11)
,`efileID` int(11)
,`typeName` varchar(30)
,`fileName` varchar(255)
,`arrangementID` int(11)
,`publicationID` int(11)
,`arrangerFirstName` varchar(30)
,`arrangerLastName` varchar(30)
,`name` varchar(100)
,`description` varchar(255)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_efilePageCount`
--
DROP VIEW IF EXISTS `view_efilePageCount`;
CREATE TABLE IF NOT EXISTS `view_efilePageCount` (
`countPages` decimal(34,0)
,`efileID` int(11)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_efilePages`
--
DROP VIEW IF EXISTS `view_efilePages`;
CREATE TABLE IF NOT EXISTS `view_efilePages` (
`efileID` int(11)
,`countPages` decimal(34,0)
,`name` varchar(255)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_efilePart`
--
DROP VIEW IF EXISTS `view_efilePart`;
CREATE TABLE IF NOT EXISTS `view_efilePart` (
`arrangementid` int(11)
,`formatID` int(11)
,`efileID` int(11)
,`partID` int(11)
,`songName` varchar(100)
,`partName` varchar(30)
,`startPage` int(11)
,`endPage` int(11)
,`fileName` varchar(255)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_efilePartSetList2`
--
DROP VIEW IF EXISTS `view_efilePartSetList2`;
CREATE TABLE IF NOT EXISTS `view_efilePartSetList2` (
`fileName` varchar(255)
,`startPage` int(11)
,`endPage` int(11)
,`formatID` int(11)
,`partName` varchar(30)
,`arrangementid` int(11)
,`gigID` int(11)
,`setListOrder` decimal(11,3)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_note`
--
DROP VIEW IF EXISTS `view_note`;
CREATE TABLE IF NOT EXISTS `view_note` (
`noteID` int(11)
,`arrangerLastName` varchar(30)
,`name` varchar(100)
,`description` varchar(255)
,`noteText` text
,`noteDate` datetime
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_part`
--
DROP VIEW IF EXISTS `view_part`;
CREATE TABLE IF NOT EXISTS `view_part` (
`Sub` varchar(30)
,`Super` varchar(30)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_popular`
--
DROP VIEW IF EXISTS `view_popular`;
CREATE TABLE IF NOT EXISTS `view_popular` (
`arrangementID` int(11)
,`name` varchar(100)
,`countPlays` bigint(21)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_publication`
--
DROP VIEW IF EXISTS `view_publication`;
CREATE TABLE IF NOT EXISTS `view_publication` (
`arrangementID` int(11)
,`publicationID` int(11)
,`arrangerFirstName` varchar(30)
,`arrangerLastName` varchar(30)
,`name` varchar(100)
,`description` varchar(255)
);
-- --------------------------------------------------------

--
-- Stand-in structure for view `view_songComposer`
--
DROP VIEW IF EXISTS `view_songComposer`;
CREATE TABLE IF NOT EXISTS `view_songComposer` (
`personID` int(11)
,`firstName` varchar(30)
,`lastName` varchar(30)
,`nickName` varchar(30)
,`songID` int(11)
,`name` varchar(100)
);
-- --------------------------------------------------------

--
-- Structure for view `view_arrangement`
--
DROP TABLE IF EXISTS `view_arrangement`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_arrangement` AS select `a`.`arrangementID` AS `arrangementID`,`b1`.`firstName` AS `arrangerFirstName`,`b1`.`lastName` AS `arrangerLastName`,`b2`.`name` AS `name` from ((`person` `b1` join `song` `b2`) join `arrangement` `a`) where ((`b1`.`personID` = `a`.`arrangerPersonID`) and (`b2`.`songID` = `a`.`songID`));

-- --------------------------------------------------------

--
-- Structure for view `view_arrangementCategory`
--
DROP TABLE IF EXISTS `view_arrangementCategory`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_arrangementCategory` AS select `b1`.`lastName` AS `arrangerLastName`,`b2`.`name` AS `songName`,`c`.`name` AS `categoryName` from ((((`person` `b1` join `song` `b2`) join `arrangement` `a`) join `category` `c`) join `arrangementCategory` `ac`) where ((`b1`.`personID` = `a`.`arrangerPersonID`) and (`b2`.`songID` = `a`.`songID`) and (`a`.`arrangementID` = `ac`.`arrangementID`) and (`c`.`categoryID` = `ac`.`categoryID`));

-- --------------------------------------------------------

--
-- Structure for view `view_band`
--
DROP TABLE IF EXISTS `view_band`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_band` AS select `b1`.`name` AS `Sub`,`b2`.`name` AS `Super` from ((`section` `b1` join `section` `b2`) join `band` `b`) where ((`b`.`subSectionID` = `b1`.`sectionID`) and (`b`.`superSectionID` = `b2`.`sectionID`));

-- --------------------------------------------------------

--
-- Structure for view `view_countPartPage`
--
DROP TABLE IF EXISTS `view_countPartPage`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_countPartPage` AS select sum(((`view_efilePart`.`endPage` - `view_efilePart`.`startPage`) + 1)) AS `countPage`,`view_efilePart`.`arrangementid` AS `arrangementID`,`view_efilePart`.`partID` AS `partID` from `view_efilePart` group by `view_efilePart`.`arrangementid`,`view_efilePart`.`partID`;

-- --------------------------------------------------------

--
-- Structure for view `view_efile`
--
DROP TABLE IF EXISTS `view_efile`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_efile` AS select `b2`.`formatID` AS `formatID`,`b2`.`efileID` AS `efileID`,`b1`.`name` AS `typeName`,`b2`.`name` AS `fileName`,`p`.`arrangementID` AS `arrangementID`,`p`.`publicationID` AS `publicationID`,`p`.`arrangerFirstName` AS `arrangerFirstName`,`p`.`arrangerLastName` AS `arrangerLastName`,`p`.`name` AS `name`,`p`.`description` AS `description` from ((`efileType` `b1` join `efile` `b2`) join `view_publication` `p`) where ((`b1`.`efileTypeID` = `b2`.`efileTypeID`) and (`b2`.`publicationID` = `p`.`publicationID`));

-- --------------------------------------------------------

--
-- Structure for view `view_efilePageCount`
--
DROP TABLE IF EXISTS `view_efilePageCount`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_efilePageCount` AS select sum(((`P`.`endPage` - `P`.`startPage`) + 1)) AS `countPages`,`P`.`efileID` AS `efileID` from `efilePart` `P` group by `P`.`efileID`;

-- --------------------------------------------------------

--
-- Structure for view `view_efilePages`
--
DROP TABLE IF EXISTS `view_efilePages`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_efilePages` AS select `E`.`efileID` AS `efileID`,`C`.`countPages` AS `countPages`,`E`.`name` AS `name` from (`efile` `E` left join `view_efilePageCount` `C` on((`E`.`efileID` = `C`.`efileID`)));

-- --------------------------------------------------------

--
-- Structure for view `view_efilePart`
--
DROP TABLE IF EXISTS `view_efilePart`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_efilePart` AS select `v`.`arrangementID` AS `arrangementid`,`v`.`formatID` AS `formatID`,`v`.`efileID` AS `efileID`,`t`.`partID` AS `partID`,`v`.`name` AS `songName`,`t`.`name` AS `partName`,`i`.`startPage` AS `startPage`,`i`.`endPage` AS `endPage`,`v`.`fileName` AS `fileName` from ((`efilePart` `i` join `view_efile` `v`) join `part` `t`) where ((`v`.`efileID` = `i`.`efileID`) and (`i`.`partID` = `t`.`partID`));

-- --------------------------------------------------------

--
-- Structure for view `view_efilePartSetList2`
--
DROP TABLE IF EXISTS `view_efilePartSetList2`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_efilePartSetList2` AS select `V`.`fileName` AS `fileName`,`V`.`startPage` AS `startPage`,`V`.`endPage` AS `endPage`,`V`.`formatID` AS `formatID`,`V`.`partName` AS `partName`,`S`.`arrangementID` AS `arrangementid`,`S`.`gigID` AS `gigID`,`S`.`setListOrder` AS `setListOrder` from (`view_efilePart` `V` join `setList2` `S`) where (`V`.`arrangementid` = `S`.`arrangementID`) order by `S`.`gigID`,`S`.`setListOrder`,`V`.`partName`;

-- --------------------------------------------------------

--
-- Structure for view `view_note`
--
DROP TABLE IF EXISTS `view_note`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_note` AS select `n`.`noteID` AS `noteID`,`b1`.`lastName` AS `arrangerLastName`,`b2`.`name` AS `name`,`p`.`description` AS `description`,`n`.`noteText` AS `noteText`,`n`.`noteDate` AS `noteDate` from ((((`person` `b1` join `song` `b2`) join `publication` `p`) join `arrangement` `a`) join `note` `n`) where ((`b1`.`personID` = `a`.`arrangerPersonID`) and (`b2`.`songID` = `a`.`songID`) and (`a`.`arrangementID` = `p`.`arrangementID`) and (`n`.`publicationID` = `p`.`publicationID`));

-- --------------------------------------------------------

--
-- Structure for view `view_part`
--
DROP TABLE IF EXISTS `view_part`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_part` AS select `b1`.`name` AS `Sub`,`b2`.`name` AS `Super` from (`part` `b1` join `section` `b2`) where (`b1`.`minSectionID` = `b2`.`sectionID`);

-- --------------------------------------------------------

--
-- Structure for view `view_popular`
--
DROP TABLE IF EXISTS `view_popular`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_popular` AS select `A`.`arrangementID` AS `arrangementID`,`A`.`name` AS `name`,count(0) AS `countPlays` from ((`view_arrangement` `A` join `setList2` `S` on((`A`.`arrangementID` = `S`.`arrangementID`))) join `gig` `G` on((`G`.`gigID` = `S`.`gigID`))) where (`G`.`isGig` <> 0) group by `A`.`arrangementID` order by count(0) desc,`A`.`name`;

-- --------------------------------------------------------

--
-- Structure for view `view_publication`
--
DROP TABLE IF EXISTS `view_publication`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_publication` AS select `a`.`arrangementID` AS `arrangementID`,`p`.`publicationID` AS `publicationID`,`b1`.`firstName` AS `arrangerFirstName`,`b1`.`lastName` AS `arrangerLastName`,`b2`.`name` AS `name`,`p`.`description` AS `description` from (((`person` `b1` join `song` `b2`) join `publication` `p`) join `arrangement` `a`) where ((`b1`.`personID` = `a`.`arrangerPersonID`) and (`b2`.`songID` = `a`.`songID`) and (`a`.`arrangementID` = `p`.`arrangementID`));

-- --------------------------------------------------------

--
-- Structure for view `view_songComposer`
--
DROP TABLE IF EXISTS `view_songComposer`;

CREATE ALGORITHM=UNDEFINED DEFINER=`makeUpAUserName`@`localhost` SQL SECURITY DEFINER VIEW `view_songComposer` AS select `b1`.`personID` AS `personID`,`b1`.`firstName` AS `firstName`,`b1`.`lastName` AS `lastName`,`b1`.`nickName` AS `nickName`,`b2`.`songID` AS `songID`,`b2`.`name` AS `name` from ((`person` `b1` join `song` `b2`) join `songComposer` `SC`) where ((`b1`.`personID` = `SC`.`composerPersonID`) and (`b2`.`songID` = `SC`.`songID`));

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
