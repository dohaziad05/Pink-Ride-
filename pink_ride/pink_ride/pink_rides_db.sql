-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: 04 مايو 2026 الساعة 14:41
-- إصدار الخادم: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pink_rides_db`
--

-- --------------------------------------------------------

--
-- بنية الجدول `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `ride_id` int(11) NOT NULL,
  `passenger_id` int(11) NOT NULL,
  `booking_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `bookings`
--

INSERT INTO `bookings` (`id`, `ride_id`, `passenger_id`, `booking_time`) VALUES
(1, 4, 9, '2026-05-04 07:21:23'),
(2, 2, 9, '2026-05-04 07:21:37'),
(3, 2, 9, '2026-05-04 07:22:00'),
(4, 6, 11, '2026-05-04 07:44:25'),
(5, 6, 11, '2026-05-04 07:45:02'),
(6, 7, 13, '2026-05-04 12:31:21'),
(7, 6, 13, '2026-05-04 12:31:30');

-- --------------------------------------------------------

--
-- بنية الجدول `rides`
--

CREATE TABLE `rides` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `start_point` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `available_seats` int(11) NOT NULL,
  `status` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `rides`
--

INSERT INTO `rides` (`id`, `driver_id`, `start_point`, `destination`, `price`, `available_seats`, `status`) VALUES
(2, 5, 'الزرقاء', 'عمان', 3.00, 0, 'متاحة'),
(4, 6, 'العبدلي', 'الشميساني', 3.00, 0, 'متاحة'),
(6, 10, 'العبدلي', 'الشميساني', 3.50, 1, 'متاحة'),
(7, 12, 'عمان', 'الشميساني', 3.50, 3, 'متاحة');

-- --------------------------------------------------------

--
-- بنية الجدول `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `dob` date NOT NULL,
  `role` enum('driver','passenger') NOT NULL,
  `profile_pic` varchar(255) NOT NULL DEFAULT 'default.png',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- إرجاع أو استيراد بيانات الجدول `users`
--

INSERT INTO `users` (`id`, `full_name`, `email`, `PASSWORD`, `phone_number`, `dob`, `role`, `profile_pic`, `created_at`) VALUES
(1, 'تقى', 'Toqaabuhera@gmail.com', '$2y$10$xlXlPSAMcm5k9BPicQN1FefSYjkFqUV5TLz9R58iQlP/MXlDD9O7u', '0783929275', '2008-01-24', 'driver', 'default.png', '2026-05-04 06:01:46'),
(2, 'aya', 'bskoprgkp3r@gmail.com', '$2y$10$bo2IXt/Tvy4TcljgcfenhufAm4BfUUIa7KMhkXyWHQhKTahx4qCN.', '0794673849', '2008-04-23', 'driver', 'default.png', '2026-05-04 06:03:38'),
(3, 'zain', 'zainzaid12345@gmail.com', '$2y$10$Re1KwBgFaPL82lXDZUgKJ.H.yR6QfDBWPCtfsKDoUHM8Osd6BsU5q', '0793572864', '2006-02-13', 'passenger', 'default.png', '2026-05-04 06:14:27'),
(4, 'mona', 'mona123456789@gmail.com', '$2y$10$raeuetS.UY2LHaqAuOmZheZ08avLvCE.bkoE4HH7wbgymXwTdJf2u', '0793672834', '2004-02-17', 'passenger', 'default.png', '2026-05-04 06:19:51'),
(5, 'Toqa', 'Toqaahmad@gmail.com', '$2y$10$nDRsXUz.UlNgp40..dHfRuIetZFf1jHSsHg2E65zTNXjI91CuUjq2', '0789352809', '2005-11-07', 'driver', 'default.png', '2026-05-04 06:22:29'),
(6, 'محمود', 'mdhkslkkkl@gmail.com', '$2y$10$IvNlq4DQXvCX2jOi8XNf.eyB6/OCimF4Xv17AV0dSwHhgkssWlDtS', '0783567284', '2001-02-14', 'driver', 'default.png', '2026-05-04 07:03:11'),
(7, 'هيا', 'haya2672@gmail.com', '$2y$10$XqepkmYxKg5tIkTAzSOtPu1n8YRpnrpi1J.yR5CuJJQIPFAU1DmNi', '0794563728', '2005-06-14', 'passenger', 'default.png', '2026-05-04 07:06:24'),
(8, 'kjbcw', 'HBFBGNJK@gmail.com', '$2y$10$z1NCEGfWOzDxcFUtwUdX4O6GD9urxwRY.LqouHnt3E1JtbgnoQI/.', '0783572897', '2008-05-01', 'passenger', 'default.png', '2026-05-04 07:07:20'),
(9, 'نالاي', 'FHGL@gmail.com', '$2y$10$mAYejJ4hX2aJe3pl/dTMC.7TOjIV/4C6L0fprcl7C0tVgFN8KxcQO', '0783575273', '2008-05-04', 'passenger', 'default.png', '2026-05-04 07:11:44'),
(10, 'تقى احمد', 'Hhjgjbknj@gmail.com', '$2y$10$klE6afBspYO22zyJzwo2y.XWYQMs1RCB821PYsuMKh1GuGHRyB8v2', '0783562747', '2008-05-02', 'driver', 'default.png', '2026-05-04 07:41:59'),
(11, 'اية', 'ayaahmad@gmail.com', '$2y$10$Obp2yN1bX/NTg/i.7wz75u5oemtg1uReA/JAo3GbF4sPQZfgDjZPu', '0783562833', '2005-03-16', 'passenger', 'default.png', '2026-05-04 07:43:49'),
(12, 'تقى ابو هرة', 'toqaabhuhera@gmail.com', '$2y$10$lMLozCkHjYghOAAZO2B1KejB9KbHylfRoZTeQ1IuH2W94kvH/9Hi.', '0783963526', '2008-05-02', 'driver', 'default.png', '2026-05-04 12:10:32'),
(13, 'تقى ابو هرة', 'toqaabumsfmhera@gmail.com', '$2y$10$9yjIfuQ8lk4uzROKggCJMO3qkvl5PX71DaOkn/AygTJFeN/2rdtq2', '0783527714', '2008-05-03', 'passenger', 'default.png', '2026-05-04 12:25:20');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rides`
--
ALTER TABLE `rides`
  ADD PRIMARY KEY (`id`),
  ADD KEY `driver_id` (`driver_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rides`
--
ALTER TABLE `rides`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
