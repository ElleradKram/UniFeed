-- phpMyAdmin SQL Dump
-- UniFeed - Simple Event Posting System
-- Database: bullboard_db

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create database
CREATE DATABASE IF NOT EXISTS `bullboard_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `bullboard_db`;

-- --------------------------------------------------------

-- Table structure for table `users`
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `student_id` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `profile_picture` varchar(255) DEFAULT NULL,
  `role` enum('student','admin','org_officer') DEFAULT 'student',
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `student_id` (`student_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `organizations`
CREATE TABLE `organizations` (
  `org_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `logo_url` varchar(255) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `categories`
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#1B5E20',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`category_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `posts`
CREATE TABLE `posts` (
  `post_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `org_id` int(11) DEFAULT NULL,
  `category_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `post_type` enum('announcement','event','memo') DEFAULT 'event',
  `image_url` varchar(255) DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `org_id` (`org_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE SET NULL,
  CONSTRAINT `posts_ibfk_3` FOREIGN KEY (`category_id`) REFERENCES `categories` (`category_id`) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `saved_posts`
CREATE TABLE `saved_posts` (
  `save_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`save_id`),
  UNIQUE KEY `unique_save` (`user_id`,`post_id`),
  KEY `post_id` (`post_id`),
  CONSTRAINT `saved_posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `saved_posts_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Dumping data for table `categories`
INSERT INTO `categories` (`category_id`, `name`, `description`, `color`, `created_at`) VALUES
(1, 'General Events', 'General campus events and activities', '#1B5E20', current_timestamp()),
(2, 'Academic Events', 'Academic conferences, seminars, and workshops', '#2196F3', current_timestamp()),
(3, 'Sports Events', 'Sports competitions and recreational activities', '#4CAF50', current_timestamp()),
(4, 'Cultural Events', 'Arts, culture, and entertainment events', '#E91E63', current_timestamp()),
(5, 'Organization Events', 'Student organization specific events', '#FF9800', current_timestamp()),
(6, 'Announcements', 'Important announcements and memos', '#9C27B0', current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `organizations`
INSERT INTO `organizations` (`org_id`, `name`, `description`, `logo_url`, `contact_email`, `is_active`, `created_at`) VALUES
(1, 'Computer Science Society', 'Organization for Computer Science students', 'images/avatar.png', 'css@cvsu.edu.ph', 1, current_timestamp()),
(2, 'Student Government', 'Official student government body', 'images/avatar.png', 'sg@cvsu.edu.ph', 1, current_timestamp()),
(3, 'Engineering Society', 'Organization for Engineering students', 'images/avatar.png', 'es@cvsu.edu.ph', 1, current_timestamp()),
(4, 'Business Club', 'Organization for Business students', 'images/avatar.png', 'bc@cvsu.edu.ph', 1, current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `users`
INSERT INTO `users` (`user_id`, `student_id`, `email`, `password`, `full_name`, `profile_picture`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2021-00001', 'admin@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', NULL, 'admin', 1, current_timestamp(), current_timestamp()),
(2, '2021-12345', 'student@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', NULL, 'student', 1, current_timestamp(), current_timestamp()),
(3, '2021-54321', 'test@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', NULL, 'student', 1, current_timestamp(), current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `posts`
INSERT INTO `posts` (`post_id`, `user_id`, `org_id`, `category_id`, `title`, `content`, `post_type`, `image_url`, `event_date`, `event_location`, `is_pinned`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Welcome to UniFeed!', 'This is the official bulletin board system for CvSU Silang Campus. Post your events and announcements here!', 'announcement', NULL, NULL, NULL, 1, 1, current_timestamp(), current_timestamp()),
(2, 2, 2, 3, 'Annual Intramurals 2024', 'Join us for the annual intramurals! Various sports categories available. Registration starts next week.', 'event', NULL, '2024-12-20 09:00:00', 'CvSU Gymnasium', 0, 1, current_timestamp(), current_timestamp()),
(3, 1, 1, 2, 'Programming Workshop', 'Free programming workshop for beginners. Learn web development basics with HTML, CSS, and JavaScript.', 'event', NULL, '2024-12-18 14:00:00', 'Computer Laboratory', 0, 1, current_timestamp(), current_timestamp());

-- --------------------------------------------------------

COMMIT;
