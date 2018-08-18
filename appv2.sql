-- phpMyAdmin SQL Dump
-- version 4.8.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 01, 2001 at 12:25 PM
-- Server version: 10.1.34-MariaDB
-- PHP Version: 7.2.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `appv2`
--

-- --------------------------------------------------------

--
-- Table structure for table `APP_banned`
--

CREATE TABLE `APP_banned` (
  `userid` int(11) NOT NULL,
  `until` int(11) NOT NULL,
  `by` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `APP_groups`
--

CREATE TABLE `APP_groups` (
  `groupid` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `type` int(11) NOT NULL,
  `priority` int(11) NOT NULL,
  `color` varchar(50) NOT NULL,
  `canban` int(11) NOT NULL,
  `canhideavt` int(11) NOT NULL,
  `canedit` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `APP_groups`
--

INSERT INTO `APP_groups` (`groupid`, `name`, `type`, `priority`, `color`, `canban`, `canhideavt`, `canedit`) VALUES
(1, 'Guests', 0, 0, 'gray', 0, 0, 0),
(2, 'Student', 1, 3, 'blue', 0, 0, 1),
(3, 'Teacher', 2, 1, 'brown', 0, 0, 0),
(4, 'Administrator', 3, 2, 'red', 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `APP_privacy`
--

CREATE TABLE `APP_privacy` (
  `userid` int(11) NOT NULL,
  `email` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `APP_privacy`
--

INSERT INTO `APP_privacy` (`userid`, `email`) VALUES
(1, 0),
(2, 1),
(3, 0);

-- --------------------------------------------------------

--
-- Table structure for table `APP_settings`
--

CREATE TABLE `APP_settings` (
  `site_name` varchar(255) NOT NULL DEFAULT 'Teachers Evaluation System',
  `url` varchar(300) NOT NULL DEFAULT 'http://localhost/appv2',
  `admin_email` varchar(255) NOT NULL DEFAULT 'sitsitboy@gmail.com',
  `max_ban_period` int(11) NOT NULL DEFAULT '10',
  `register` int(11) NOT NULL DEFAULT '1',
  `email_validation` int(11) NOT NULL DEFAULT '0',
  `captcha` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `APP_settings`
--

INSERT INTO `APP_settings` (`site_name`, `url`, `admin_email`, `max_ban_period`, `register`, `email_validation`, `captcha`) VALUES
('', '', '', 0, 1, 1, 0);

-- --------------------------------------------------------

--
-- Table structure for table `APP_users`
--

CREATE TABLE `APP_users` (
  `userid` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `last_name` varchar(100) NOT NULL,
  `first_name` varchar(100) NOT NULL,
  `middle_name` varchar(100) NOT NULL,
  `mobile` varchar(25) NOT NULL,
  `age` varchar(50) NOT NULL,
  `gender` enum('Male','Female') NOT NULL,
  `password` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `key` varchar(50) NOT NULL,
  `validated` varchar(100) NOT NULL,
  `groupid` int(11) NOT NULL DEFAULT '2',
  `lastactive` int(11) NOT NULL,
  `showavt` int(11) NOT NULL DEFAULT '1',
  `banned` int(11) NOT NULL,
  `regtime` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `APP_users`
--

INSERT INTO `APP_users` (`userid`, `username`, `last_name`, `first_name`, `middle_name`, `mobile`, `age`, `gender`, `password`, `email`, `key`, `validated`, `groupid`, `lastactive`, `showavt`, `banned`, `regtime`) VALUES
(1, 'admin', 'Administrator', 'Manny', 'Pacquiao', '09266557656', '28', 'Male', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'sitsitboy@gmail.com', '8e5f09b0be6aacfdedf3db35c6136c557d70174f', '1', 4, 978320710, 1, 0, 1533924761),
(2, 'student', 'Dagohoy', 'Alfred', 'Barueso', '09266557656', '28', 'Male', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'alfred.dagohoy@gmail.com', '147980d6b8550aa6a0c8f33d9089964b8b466ded', '1', 2, 1534491844, 1, 0, 1533964814),
(3, 'teacher', 'Cabahug', 'Jan Devin', 'Paul', '09212345678', '', 'Male', '7110eda4d09e062aa5e4a390b0a572ac0d2c0220', 'cjandevin@gmail.com', '', '1', 3, 1534481176, 1, 0, 1534481166);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `APP_banned`
--
ALTER TABLE `APP_banned`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `APP_groups`
--
ALTER TABLE `APP_groups`
  ADD PRIMARY KEY (`groupid`);

--
-- Indexes for table `APP_privacy`
--
ALTER TABLE `APP_privacy`
  ADD UNIQUE KEY `userid` (`userid`);

--
-- Indexes for table `APP_users`
--
ALTER TABLE `APP_users`
  ADD PRIMARY KEY (`userid`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `APP_groups`
--
ALTER TABLE `APP_groups`
  MODIFY `groupid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `APP_users`
--
ALTER TABLE `APP_users`
  MODIFY `userid` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
