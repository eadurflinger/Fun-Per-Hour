ADMIN

CREATE TABLE `admin` (
  `AID` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) NOT NULL,
  `password` varchar(60) NOT NULL,
  `adminname` varchar(45) DEFAULT NULL,
  `adminemail` varchar(45) DEFAULT NULL,
  `salt` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`AID`,`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

REGISTRATION

CREATE TABLE `registration` (
  `attendeepno` varchar(15) NOT NULL,
  `EID` int(11) NOT NULL,
  `submittime` time(6) NOT NULL,
  PRIMARY KEY (`attendeepno`,`EID`),
  KEY `EID_idx` (`EID`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8

ATTENDEE

CREATE TABLE `attendee` (
  `EID` int(11) DEFAULT NULL,
  `entreeId` int(11) NOT NULL AUTO_INCREMENT,
  `attendeepno` varchar(15) NOT NULL,
  `updateTime` int(11) DEFAULT NULL,
  PRIMARY KEY (`entreeId`),
  KEY `EID_idx` (`EID`),
  KEY `attendeeono_idx` (`attendeepno`),
  CONSTRAINT `EID` FOREIGN KEY (`EID`) REFERENCES `event` (`EID`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `attendeepno` FOREIGN KEY (`attendeepno`) REFERENCES `registration` (`attendeepno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8


EVENT

CREATE TABLE `event` (
  `AID` int(11) DEFAULT NULL,
  `EID` int(11) NOT NULL AUTO_INCREMENT,
  `EventName` varchar(45) NOT NULL,
  `EventTime` varchar(45) DEFAULT NULL,
  `EventDate` date DEFAULT NULL,
  PRIMARY KEY (`EID`,`EventName`),
  KEY `AID_idx` (`AID`),
  CONSTRAINT `AID` FOREIGN KEY (`AID`) REFERENCES `admin` (`AID`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8

FUNLEVEL

CREATE TABLE `funlevel` (
  `attendeepno` varchar(15) NOT NULL,
  `funlevel` int(11) NOT NULL,
  `time` time(6) DEFAULT NULL,
  PRIMARY KEY (`attendeepno`,`funlevel`),
  CONSTRAINT `atendeepno` FOREIGN KEY (`attendeepno`) REFERENCES `attendee` (`attendeepno`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8
