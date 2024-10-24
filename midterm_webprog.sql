-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 24, 2024 at 01:16 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `midterm_webprog`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `date` date NOT NULL,
  `time` time NOT NULL,
  `max_participants` int(11) NOT NULL,
  `picture` varchar(255) NOT NULL,
  `status` enum('open','closed','canceled') NOT NULL DEFAULT 'open'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`id`, `name`, `description`, `location`, `date`, `time`, `max_participants`, `picture`, `status`) VALUES
(2, 'Justin Bieber', 'Konser Justin Bieber yang bertajuk Justice World Tour ini rencananya akan digelar di Stadion Madya, Gelora Bung Karno.', 'Jakarta Selatan', '2024-10-26', '19:30:00', 1500, '../../upload/6717432a5f6fa-jb.jpeg', 'open'),
(4, 'Color Run', 'The Color Run merupakan sebuah kegiatan berlari atau jalan sehat sejauh 5 km dan pada setiap satu kilometer, pelari akan diberi hujan warna dari kepala hingga kaki dengan bubuk berwarna warni. Alhasil, peserta yang saat start mengenakan warna putih akan menyelesaikan lomba dengan corak seluruh badan yang beraneka warna.', 'Bogor', '2024-10-18', '07:00:00', 500, '../../upload/671743c284331-cr.jpg', 'closed'),
(12, 'Niki', 'Nicole Zefanya, known professionally as Niki, is an Indonesian singer-songwriter. She is currently based in the United States and signed with the record label 88rising. She released her first full-length studio album, Moonchild, in 2020. It was followed by Nicole and Buzz.', 'Brussels', '2024-10-02', '19:35:00', 222, '../../upload/671747181e03f-nikki.jpg', 'canceled'),
(13, 'Meghan Trainor', 'Meghan Elizabeth Trainor is an American singer-songwriter and television personality. She rose to prominence after signing with Epic Records in 2014 and releasing her debut single \"All About That Bass\", which reached number one on the U.S. Billboard Hot 100 chart and sold 11 million copies worldwide. ', 'New York', '2024-10-16', '13:13:00', 750, '../../upload/67175b1e72b91-mt.jpg', 'open'),
(14, 'Juicy Luicy', 'Juicy Luicy merupakan sebuah grup musik asal Bandung, Indonesia yang dibentuk pada tahun 2006. Grup musik ini beranggotakan 5 orang yaitu Julian Kaisar (vokal), Denis Ligia (gitar elektrik), Zamzam Y.M (Saxsophone, gitar akustik), Dwi Nugroho (Drum), dan Bina Bagja (Bass).', 'Batam', '2024-10-22', '13:26:00', 200, '../../upload/67174613332a2-jl.jpeg', 'canceled'),
(15, 'BlackPink', 'The In Your Area World Tour was the first worldwide concert tour and the second overall by South Korean girl group Blackpink. The tour began on November 10, 2018, in Seoul, South Korea, and ended on February 22, 2020, in Fukuoka, Japan, in support of their albums Square Up and Kill This', 'Singapura', '2024-11-01', '09:00:00', 10000, '../../upload/6717a93d7f956-Screenshot 2024-10-22 132045.png', 'open'),
(16, 'Scream Or Dance', 'Scream or Dance is The biggest Halloween music festival in indonesia. Most of the audience will dress up to attend the event and become another character during the event, this year scream or dance will embrace The Undiscovered universe as their concept and magical theme.\r\n\r\nScream or Dance have succeeded to bring amazing performers like Clean bandit, Disclosure, Brennan Heart, Andrew Rayel, Coone and many more.\r\n\r\nDont miss this year Scream or Dance on 1st & 2nd November 2024 at Carnaval Ancol (Race Track). as they have more suprises yet to happen!', 'Ancol', '2024-11-01', '01:01:00', 500, '../../upload/6717455466fb8-sod.jpeg', 'open'),
(17, 'BrunoMars', 'Peter Gene Hernandez, known professionally as Bruno Mars, is an American singer-songwriter. He is known for his stage performances, retro showmanship, and for singing in a wide range of musical styles, including pop, R&B, funk, soul, reggae, disco, and rock.', 'Gading Serpong', '2024-10-26', '17:00:00', 3000, '../../upload/6717452932226-bruno.png', 'open');

-- --------------------------------------------------------

--
-- Table structure for table `history`
--

CREATE TABLE `history` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `history`
--

INSERT INTO `history` (`id`, `event_id`, `user_id`) VALUES
(4, 16, 7),
(5, 17, 7),
(6, 2, 7),
(7, 13, 7),
(8, 15, 7),
(9, 15, 7),
(10, 16, 7),
(11, 13, 7),
(12, 15, 7),
(13, 17, 7),
(14, 13, 12),
(15, 15, 12),
(16, 13, 12),
(17, 17, 12),
(18, 2, 12),
(19, 17, 12);

-- --------------------------------------------------------

--
-- Table structure for table `registrations`
--

CREATE TABLE `registrations` (
  `id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `registration_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `registrations`
--

INSERT INTO `registrations` (`id`, `event_id`, `user_id`, `registration_date`) VALUES
(69, 15, 12, '2024-10-24 14:00:35'),
(70, 13, 12, '2024-10-24 14:03:02'),
(73, 17, 12, '2024-10-24 17:20:37');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `profile` varchar(255) NOT NULL,
  `role` int(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `profile`, `role`) VALUES
(7, 'muiz', 'mumu@gmail.com', '$2y$10$p2rTZL9X.By1ltA34IY6reb8XpTRZCM4cOCtTZM3AoGmL5.pMvL6W', '../../../pp_user/6719ca78afda1_olaf.jpg', 0),
(8, 'karel', 'karrel@gmail.com', '$2y$10$ukDnDRekY7IObiNDzo5x0e.JX7ruSD22NPF5fQO4t3/hfcah3mU7S', '../../../pp_user/6718e84e87468_mo.jpg', 0),
(9, 'mauldynan', 'mauldynan@gmail.com', '$2y$10$g/Pv9MZtf7KROMZCzt3iburs3j.TiK1Ri7SqyzK4jbxDt5qSLqqs.', '', 1),
(12, 'gista', 'gista@gmail.com', '$2y$10$1bqwp1.l6ZGwCHZI1CtFBOnwf1yB1twp62DFnZ.XxUvRhA3zp0S1a', '../../pp_user/6719fb9a68ac5_rp.jpg', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `registrations`
--
ALTER TABLE `registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `event` (`event_id`),
  ADD KEY `user` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `history`
--
ALTER TABLE `history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `registrations`
--
ALTER TABLE `registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=74;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `registrations`
--
ALTER TABLE `registrations`
  ADD CONSTRAINT `event` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`),
  ADD CONSTRAINT `user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
