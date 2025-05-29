-- Compiled BullBoard Database Dump
-- Combines full schema from bullboard_db.sql with event_location column from bullboard_simple.sql


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
  `bio` text DEFAULT NULL,
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
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`org_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `categories`
CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `description` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#1B5E20',
  `icon` varchar(50) DEFAULT NULL,
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
  `post_type` enum('announcement','event','memo','discussion') DEFAULT 'discussion',
  `image_url` varchar(255) DEFAULT NULL,
  `event_date` datetime DEFAULT NULL,
  `event_location` varchar(255) DEFAULT NULL,
  `is_pinned` tinyint(1) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `comment_count` int(11) DEFAULT 0,
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

-- Table structure for table `comments`
CREATE TABLE `comments` (
  `comment_id` int(11) NOT NULL AUTO_INCREMENT,
  `post_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `parent_comment_id` int(11) DEFAULT NULL,
  `content` text NOT NULL,
  `upvotes` int(11) DEFAULT 0,
  `downvotes` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  PRIMARY KEY (`comment_id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_comment_id` (`parent_comment_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `votes`
CREATE TABLE `votes` (
  `vote_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `post_id` int(11) DEFAULT NULL,
  `comment_id` int(11) DEFAULT NULL,
  `vote_type` enum('upvote','downvote') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`vote_id`),
  UNIQUE KEY `unique_post_vote` (`user_id`,`post_id`),
  UNIQUE KEY `unique_comment_vote` (`user_id`,`comment_id`),
  KEY `post_id` (`post_id`),
  KEY `comment_id` (`comment_id`),
  CONSTRAINT `votes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `votes_ibfk_3` FOREIGN KEY (`comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE
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

-- Table structure for table `organization_members`
CREATE TABLE `organization_members` (
  `member_id` int(11) NOT NULL AUTO_INCREMENT,
  `org_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `role` enum('member','officer','president') DEFAULT 'member',
  `joined_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`member_id`),
  UNIQUE KEY `unique_membership` (`org_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `organization_members_ibfk_1` FOREIGN KEY (`org_id`) REFERENCES `organizations` (`org_id`) ON DELETE CASCADE,
  CONSTRAINT `organization_members_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Table structure for table `notifications`
CREATE TABLE `notifications` (
  `notification_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `type` enum('comment','vote','mention','event','announcement') NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `related_post_id` int(11) DEFAULT NULL,
  `related_comment_id` int(11) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`notification_id`),
  KEY `user_id` (`user_id`),
  KEY `related_post_id` (`related_post_id`),
  KEY `related_comment_id` (`related_comment_id`),
  CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_2` FOREIGN KEY (`related_post_id`) REFERENCES `posts` (`post_id`) ON DELETE CASCADE,
  CONSTRAINT `notifications_ibfk_3` FOREIGN KEY (`related_comment_id`) REFERENCES `comments` (`comment_id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

-- Dumping data for table `categories`
INSERT INTO `categories` (`category_id`, `name`, `description`, `color`, `icon`, `created_at`) VALUES
(1, 'General', 'General discussions and announcements', '#1B5E20', 'general', current_timestamp()),
(2, 'Events', 'Upcoming events and activities', '#2196F3', 'calendar', current_timestamp()),
(3, 'Academic', 'Academic-related discussions', '#FF9800', 'academic', current_timestamp()),
(4, 'Sports', 'Sports and recreational activities', '#4CAF50', 'sports', current_timestamp()),
(5, 'Technology', 'Tech-related discussions', '#9C27B0', 'tech', current_timestamp()),
(6, 'Arts & Culture', 'Arts, culture, and creative activities', '#E91E63', 'arts', current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `organizations`
INSERT INTO `organizations` (`org_id`, `name`, `description`, `logo_url`, `contact_email`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Computer Science Society', 'Organization for Computer Science students', 'images/avatar.png', 'css@cvsu.edu.ph', 1, current_timestamp(), current_timestamp()),
(2, 'Student Government', 'Official student government body', 'images/avatar.png', 'sg@cvsu.edu.ph', 1, current_timestamp(), current_timestamp()),
(3, 'Engineering Society', 'Organization for Engineering students', 'images/avatar.png', 'es@cvsu.edu.ph', 1, current_timestamp(), current_timestamp()),
(4, 'Business Club', 'Organization for Business students', 'images/avatar.png', 'bc@cvsu.edu.ph', 1, current_timestamp(), current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `users`
-- Passwords: admin123, student123, test123 (all hashed with password_hash())
INSERT INTO `users` (`user_id`, `student_id`, `email`, `password`, `full_name`, `profile_picture`, `bio`, `role`, `is_active`, `created_at`, `updated_at`) VALUES
(1, '2021-00001', 'admin@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'System Administrator', NULL, NULL, 'admin', 1, current_timestamp(), current_timestamp()),
(2, '2021-12345', 'student@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'John Doe', NULL, NULL, 'student', 1, current_timestamp(), current_timestamp()),
(3, '2021-54321', 'test@cvsu.edu.ph', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Test User', NULL, NULL, 'student', 1, current_timestamp(), current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `posts`
INSERT INTO `posts` (`post_id`, `user_id`, `org_id`, `category_id`, `title`, `content`, `post_type`, `image_url`, `event_date`, `event_location`, `is_pinned`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 1, 'Welcome to UniFeed!', 'This is the official bulletin board system for CvSU Silang Campus. Share announcements, events, and connect with fellow students!', 'announcement', NULL, NULL, NULL, 1, 1, current_timestamp(), current_timestamp()),
(2, 2, 2, 2, 'Upcoming Intramurals 2024', 'Join us for the annual intramurals! Registration starts next week. Various sports categories available.', 'event', NULL, '2024-12-20 09:00:00', 'CvSU Gymnasium', 0, 1, current_timestamp(), current_timestamp()),
(3, 1, 1, 3, 'Programming Workshop', 'Free programming workshop for beginners. Learn the basics of web development with HTML, CSS, and JavaScript.', 'event', NULL, '2024-12-18 14:00:00', 'Computer Laboratory', 0, 1, current_timestamp(), current_timestamp());

-- --------------------------------------------------------

-- Dumping data for table `notifications`
INSERT INTO `notifications` (`notification_id`, `user_id`, `type`, `title`, `message`, `related_post_id`, `related_comment_id`, `is_read`, `created_at`) VALUES
(1, 2, 'announcement', 'Welcome to UniFeed!', 'System Administrator posted a new announcement', 1, NULL, 0, current_timestamp()),
(2, 2, 'event', 'New Event: Intramurals 2024', 'Student Government posted a new event', 2, NULL, 0, current_timestamp()),
(3, 2, 'event', 'Programming Workshop Available', 'Computer Science Society posted a new workshop', 3, NULL, 1, current_timestamp()),
(4, 1, 'event', 'Event Reminder', 'Your Programming Workshop is coming up soon!', 3, NULL, 0, current_timestamp()),
(5, 3, 'announcement', 'Welcome to UniFeed!', 'System Administrator posted a new announcement', 1, NULL, 0, current_timestamp());

-- --------------------------------------------------------

COMMIT;
