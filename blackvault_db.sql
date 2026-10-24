-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 24, 2026 at 05:27 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `blackvault_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `about_team`
--

CREATE TABLE `about_team` (
  `team_id` int(10) UNSIGNED NOT NULL,
  `group_allocation` varchar(150) NOT NULL,
  `team_name` varchar(100) NOT NULL,
  `tutorial_day` varchar(20) NOT NULL,
  `tutorial_time` varchar(50) NOT NULL,
  `photo_path` varchar(255) NOT NULL,
  `photo_alt` varchar(255) NOT NULL,
  `photo_caption` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `about_team`
--

INSERT INTO `about_team` (`team_id`, `group_allocation`, `team_name`, `tutorial_day`, `tutorial_time`, `photo_path`, `photo_alt`, `photo_caption`) VALUES
(1, 'G01 - Cybersecurity Services', 'Team Blaze', 'Wednesday', '2:30 PM - 4:30 PM', 'images/group_photo.jpeg', 'The four BlackVault Cyber team members', 'BlackVaultCyber Core Deployment Team: Mohammad, Simran, Jai, and Dat.');

-- --------------------------------------------------------

--
-- Table structure for table `eoi`
--

CREATE TABLE `eoi` (
  `EOInumber` int(11) NOT NULL,
  `job_ref` varchar(30) NOT NULL,
  `first_name` varchar(50) NOT NULL,
  `last_name` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `start_date` date DEFAULT NULL,
  `experience` varchar(30) DEFAULT NULL,
  `linkedin` varchar(255) DEFAULT NULL,
  `cover_letter` text DEFAULT NULL,
  `status` enum('New','Current','Final') NOT NULL DEFAULT 'New',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `dob` date DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `street` varchar(40) DEFAULT NULL,
  `suburb` varchar(40) DEFAULT NULL,
  `state` varchar(3) DEFAULT NULL,
  `postcode` char(4) DEFAULT NULL,
  `skills` text DEFAULT NULL,
  `other_skills` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `team_members`
--

CREATE TABLE `team_members` (
  `member_id` int(10) UNSIGNED NOT NULL,
  `team_id` int(10) UNSIGNED NOT NULL,
  `member_name` varchar(100) NOT NULL,
  `student_id` varchar(20) NOT NULL,
  `favourite_technology` varchar(150) NOT NULL,
  `fun_fact` text NOT NULL,
  `part1_contribution` text NOT NULL,
  `part2_contribution` text DEFAULT NULL,
  `native_quote` text NOT NULL,
  `quote_language` varchar(30) NOT NULL,
  `quote_language_code` varchar(10) NOT NULL,
  `quote_direction` enum('ltr','rtl') NOT NULL DEFAULT 'ltr',
  `quote_translation` text NOT NULL,
  `display_order` int(10) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `team_members`
--

INSERT INTO `team_members` (`member_id`, `team_id`, `member_name`, `student_id`, `favourite_technology`, `fun_fact`, `part1_contribution`, `part2_contribution`, `native_quote`, `quote_language`, `quote_language_code`, `quote_direction`, `quote_translation`, `display_order`) VALUES
(1, 1, 'Jai', '106206655', 'Automated Threat Scanners', 'Pakistan is home to the world\'s second-highest mountain peak (K2).', 'Led project management using Jira, managed GitHub collaboration, and developed about.html with the required structures, figures and data tables.', 'Implemented shared PHP components and database settings. Currently developing the database-driven About page. Responsible for presentation order and timing.', 'محنت اتنی خاموشی سے کرو کہ تمہاری کامیابی شور مچا دے۔', 'Urdu', 'ur', 'rtl', 'Work hard in silence, let your success be your noise.', 1),
(2, 1, 'Mohammad', '106205050', 'Modern Bidet Attachment', 'Did you know that there are over 100 pyramids in Egypt?', 'Developed index.html, established cybersecurity branding, implemented the services comparison table and configured the CSS background pattern.', 'Implemented HR management code for listing and filtering EOIs, deleting by job reference, updating status and sorting results. Authentication integration, fixes and testing remain.', 'امشي عدل يحتار عدوک فيك', 'Arabic', 'ar', 'rtl', 'Walk the straight and narrow, and your enemies will have nothing on you.', 2),
(3, 1, 'Dat', '106638766', 'Prosthetic Limb Engineering', 'Admittedly not a writer, but somehow always gets by.', 'Structured jobs.html, authored cybersecurity job descriptions and qualification requirements, and developed the complementary aside content layout.', 'Assigned the jobs table, database-driven job descriptions and search. Also responsible for the user table and login page. Completion is pending verification.', 'Cứ thử đi.', 'Vietnamese', 'vi', 'ltr', 'Just try it.', 3),
(4, 1, 'Simran', '106578257', 'Kindle', 'Owner of two dogs and a pet snake.', 'Built the recruitment form in apply.html, configured fieldsets, labels and HTML5 client-side validation, and styled the form using CSS.', 'Assigned the EOI table and application processing, including server-side validation, sanitisation, record insertion, direct-access protection and EOI confirmation. Completion is pending verification.', 'मैं उड़ना चाहता हूँ, दौड़ना चाहता हूँ, गिरना भी चाहता हूँ ... बस रुकना नहीं चाहता।', 'Hindi', 'hi', 'ltr', 'I want to fly, I want to run, I even want to fall ... I just don\'t want to stop.', 4);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `about_team`
--
ALTER TABLE `about_team`
  ADD PRIMARY KEY (`team_id`);

--
-- Indexes for table `eoi`
--
ALTER TABLE `eoi`
  ADD PRIMARY KEY (`EOInumber`);

--
-- Indexes for table `team_members`
--
ALTER TABLE `team_members`
  ADD PRIMARY KEY (`member_id`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `fk_members_team` (`team_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `about_team`
--
ALTER TABLE `about_team`
  MODIFY `team_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eoi`
--
ALTER TABLE `eoi`
  MODIFY `EOInumber` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `team_members`
--
ALTER TABLE `team_members`
  MODIFY `member_id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `team_members`
--
ALTER TABLE `team_members`
  ADD CONSTRAINT `fk_members_team` FOREIGN KEY (`team_id`) REFERENCES `about_team` (`team_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
