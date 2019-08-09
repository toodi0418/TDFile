-- phpMyAdmin SQL Dump
-- version 4.4.15.10
-- https://www.phpmyadmin.net
--
-- 主機: localhost
-- 產生時間： 2019 年 08 月 09 日 03:21
-- 伺服器版本: 5.6.41
-- PHP 版本： 7.2.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- 資料庫： `upload`
--

-- --------------------------------------------------------

--
-- 資料表結構 `files`
--

CREATE TABLE IF NOT EXISTS `files` (
  `id` int(11) NOT NULL,
  `username` text NOT NULL,
  `filename` text NOT NULL,
  `filesize` text NOT NULL,
  `filequid` text NOT NULL,
  `filepass` text NOT NULL,
  `public` text NOT NULL,
  `UploadIP` text NOT NULL,
  `timestamp` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=156 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- 資料表結構 `users`
--

CREATE TABLE IF NOT EXISTS `users` (
  `id` int(11) NOT NULL,
  `uid` text NOT NULL,
  `name` text NOT NULL,
  `username` text NOT NULL,
  `password` text NOT NULL,
  `department` text NOT NULL,
  `groups` text NOT NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;

--
-- 資料表的匯出資料 `users`
--

INSERT INTO `users` (`id`, `uid`, `name`, `username`, `password`, `department`, `groups`) VALUES
(7, '{553D0981-2ABF-3F14-9238-825F9633908E}', 'TDFileAdmin', 'adminnn', '$2y$10$cW01WlOVHQE0Nnl1oi0R2.VGuXyWCQXapQkzXz1r3iT9gJ3bAK23m', 'ToodiTech', 'admin');

--
-- 已匯出資料表的索引
--

--
-- 資料表索引 `files`
--
ALTER TABLE `files`
  ADD PRIMARY KEY (`id`);

--
-- 資料表索引 `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- 在匯出的資料表使用 AUTO_INCREMENT
--

--
-- 使用資料表 AUTO_INCREMENT `files`
--
ALTER TABLE `files`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=156;
--
-- 使用資料表 AUTO_INCREMENT `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT,AUTO_INCREMENT=8;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
