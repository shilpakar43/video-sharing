-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 12, 2024 at 03:37 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Database: `silver_screen`

-- --------------------------------------------------------

-- Table structure for table `admin`

CREATE TABLE `admin` (
  `admin_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `username` VARCHAR(255) NOT NULL,
  `password` VARCHAR(255) NOT NULL,
  `flag_admin` TINYINT(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`admin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `admin`

INSERT INTO `admin` (`admin_id`, `username`, `password`, `flag_admin`) VALUES
(1, 'admin', '5f4dcc3b5aa765d61d8327deb882cf99', 1),
(2, 'root', 'f3ed11bbdb94fd9ebdefbaf646ab94d3', 1);

-- --------------------------------------------------------

-- Table structure for table `comment`

CREATE TABLE `comment` (
  `comment_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `comment_movie_id` INT(10) UNSIGNED DEFAULT NULL,
  `comment_user_id` INT(10) UNSIGNED DEFAULT NULL,
  `comment_username` VARCHAR(255) DEFAULT NULL,
  `comment_avatar` VARCHAR(255) DEFAULT NULL,
  `comment_text` LONGTEXT DEFAULT NULL,
  PRIMARY KEY (`comment_id`),
  KEY `comment_movie_id` (`comment_movie_id`),
  KEY `comment_user_id` (`comment_user_id`),
  CONSTRAINT `comment_ibfk_1` FOREIGN KEY (`comment_movie_id`) REFERENCES `movies` (`movie_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `comment_ibfk_2` FOREIGN KEY (`comment_user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `comment`

INSERT INTO `comment` (`comment_id`, `comment_movie_id`, `comment_user_id`, `comment_username`, `comment_avatar`, `comment_text`) VALUES
(1, 5, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'awesome'),
(2, 4, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'test comment'),
(3, 4, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'please upload mission impossible'),
(4, 6, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'this is comment'),
(5, 5, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'this is comment'),
(6, 7, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'this is comment'),
(7, 9, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'herr mero comment'),
(8, 3, 7, 'rikesh', '6560494ad2b91_Screenshot 2023-05-13 154456.png', 'qwertyu'),
(9, 1, 1, 'admin', '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg', 'Aewsome');

-- --------------------------------------------------------

-- Table structure for table `movies`

-- Create the 'movies' table
CREATE TABLE `movies` (
  `movie_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `movie_name` VARCHAR(255) DEFAULT NULL,
  `movie_upload_name` VARCHAR(255) DEFAULT NULL,
  `movie_upload_image` VARCHAR(255) DEFAULT NULL,
  `movie_description` LONGTEXT DEFAULT NULL,
  `movie_genre` VARCHAR(255) DEFAULT NULL,
  `movie_subscription` VARCHAR(255) DEFAULT NULL,
  `release_date` VARCHAR(255) DEFAULT NULL,
  `movie_language` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`movie_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Create an index on the 'movie_name' column for faster search
CREATE INDEX idx_movie_name ON movies(movie_name);

-- Dumping data for table `movies`

INSERT INTO `movies` (`movie_id`, `movie_name`, `movie_upload_name`, `movie_upload_image`, `movie_description`, `movie_genre`, `movie_subscription`, `release_date`, `movie_language`) VALUES
(1, 'movie_test', '651d7112c4aa8_242468465_594261368247553_2322973193556881581_n.mp4', '651d7112c4946_272999124_3180732085582625_6391622957893453487_n.jpg', 'This is description', 'Action', 'free', '2023', 'nepali'),
(2, 'Carla Brock', '651d7724e4377_2022-09-26_20-20-10.mp4', '651d7724e427a_280752917_688300048897747_4471061614589813199_n.jpg', 'Consequat Expedita ', 'Drama', 'free', '27-Apr-2011', 'Qui nostrum ipsum vo'),
(3, 'Dawn Mann', '651d77417f398_335888522_1158628708134306_5783167234078438848_n (1).mp4', '651d77417f1e4_313018198_577233477537424_1416717656470396202_n.jpg', 'Inventore ad tempora', 'Comedy', 'free', '04-May-2017', 'Dolorum maxime rerum'),
(4, 'Noah Shannon', '651d775a63afc_349016056_813979160084075_2209021640985995234_n.mp4', '651d775a639ea_319560288_6327084213971570_8892882654511116947_n.jpg', 'Voluptate cumque vel', 'Action', 'free', '17-May-1986', 'Officia quia anim eu'),
(5, 'Damon Battle', '651d7772ce0e2_vlc-record-2023-03-13-21h32m59s-Timid Tabby -Tom & Jerry SuperCartoons.mp4-.mp4', '651d7772ce00c_344349355_625429062440856_7061349186950783311_n.jpg', 'Qui irure minima ali', 'Adventure', 'free', '21-Jul-1973', 'Quod reiciendis esse'),
(6, 'Amery Berry', '651d8913be1dd_331411521_164324416475745_5915473970060853601_n.mp4', '651d8913be106_337880594_810880610469519_803996174794976070_n.jpg', 'Deleniti corporis la', 'Drama', 'free', '06-Aug-2021', 'Nesciunt pariatur '),
(7, 'test movie 123', '651e342deb0d8_319226934_521584639905418_5340707859004247823_n.mp4', '651e342deb012_324925924_1446227429117311_3271049359093216385_n.jpg', 'this is description of id 6', 'Action', 'premium', '2023', 'newari'),
(9, 'Movie test 2', '651d8227e1843_320292229_654295694078706_2554450750911760703_n.mp4', '651d8227e1791_338732551_560454321426739_2054728345993030548_n.jpg', 'test description', 'action', 'free', '2024', 'english');

-- --------------------------------------------------------

-- Table structure for table `users`

CREATE TABLE `users` (
  `user_id` INT(10) UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_firstname` VARCHAR(255) NOT NULL,
  `user_lastname` VARCHAR(255) NOT NULL,
  `user_username` VARCHAR(255) NOT NULL,
  `user_email` VARCHAR(255) NOT NULL,
  `user_password` VARCHAR(255) NOT NULL,
  `user_phone` VARCHAR(255) NOT NULL,
  `user_role` VARCHAR(255) DEFAULT 'user',
  `user_status` TINYINT(1) NOT NULL DEFAULT 0,
  `user_avatar` VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_username` (`user_username`),
  UNIQUE KEY `user_email` (`user_email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Dumping data for table `users`

INSERT INTO `users` (`user_id`, `user_firstname`, `user_lastname`, `user_username`, `user_email`, `user_password`, `user_phone`, `user_role`, `user_status`, `user_avatar`) VALUES
(1, 'Admin', 'User', 'admin', 'admin@domain.com', '5f4dcc3b5aa765d61d8327deb882cf99', '1234567890', 'admin', 1, '651d5953ccd96_324017406_469004508758904_5932528951960013059_n.jpg'),
(2, 'Rikesh', 'Poudel', 'rikesh', 'rikesh@domain.com', 'e10adc3949ba59abbe56e057f20f883e', '9800000000', 'user', 1, '6560494ad2b91_Screenshot 2023-05-13 154456.png'),
(7, 'Vicky', 'Shrestha', 'vicky', 'vicky@domain.com', '4ca312cd52056a3c0c5380d5b4726e92', '9822334444', 'user', 1, NULL);
-- Complete the rest of the data entries for the users as per your previous data.

-- Commit Transaction
COMMIT;
