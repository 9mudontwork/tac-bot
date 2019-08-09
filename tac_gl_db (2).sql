-- phpMyAdmin SQL Dump
-- version 4.7.4
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 22, 2018 at 08:42 PM
-- Server version: 10.1.28-MariaDB
-- PHP Version: 7.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `tac_gl_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `account`
--

CREATE TABLE `account` (
  `id` int(11) NOT NULL,
  `fb_email` varchar(120) NOT NULL,
  `owner` varchar(120) NOT NULL,
  `device_id` varchar(120) NOT NULL,
  `secret_key` varchar(120) NOT NULL,
  `platform` varchar(20) NOT NULL,
  `status` varchar(20) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
-- --------------------------------------------------------

--
-- Table structure for table `reroll_account`
--

CREATE TABLE `reroll_account` (
  `id` int(11) NOT NULL,
  `ign` varchar(120) NOT NULL,
  `detail` text NOT NULL,
  `device_id` varchar(120) NOT NULL,
  `secret_key` varchar(120) NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL,
  `user` varchar(120) NOT NULL,
  `owner` text NOT NULL,
  `role` varchar(20) NOT NULL,
  `gem` int(11) NOT NULL,
  `bot_day` int(11) NOT NULL,
  `date_buy_bot` datetime NOT NULL,
  `platform` varchar(120) NOT NULL,
  `btlid` varchar(11) NOT NULL,
  `device_id` varchar(120) NOT NULL,
  `secret_key` varchar(120) NOT NULL,
  `token` varchar(120) NOT NULL,
  `device_id_ap` varchar(120) NOT NULL,
  `secret_key_ap` varchar(120) NOT NULL,
  `token_ap` varchar(120) NOT NULL,
  `last_login` datetime NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `account`
--
ALTER TABLE `account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reroll_account`
--
ALTER TABLE `reroll_account`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `account`
--
ALTER TABLE `account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=84;

--
-- AUTO_INCREMENT for table `reroll_account`
--
ALTER TABLE `reroll_account`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
