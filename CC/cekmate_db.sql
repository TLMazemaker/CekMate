-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 34.128.119.108
-- Generation Time: Jun 15, 2023 at 09:50 AM
-- Server version: 5.7.40-google-log
-- PHP Version: 7.4.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cekmate_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `data_deteksi`
--

CREATE TABLE `data_deteksi` (
  `id` int(11) NOT NULL,
  `foto_mata_path` text NOT NULL,
  `foto_mata_url` text NOT NULL,
  `users_id` int(11) NOT NULL,
  `status_penyakit` varchar(20) NOT NULL,
  `status_deteksi` varchar(20) NOT NULL,
  `confidence` varchar(20) NOT NULL,
  `keterangan_lain` varchar(200) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `data_deteksi`
--

INSERT INTO `data_deteksi` (`id`, `foto_mata_path`, `foto_mata_url`, `users_id`, `status_penyakit`, `status_deteksi`, `confidence`, `keterangan_lain`) VALUES
(1, 'mata1.jpg', '', 2, '0', '0', '', ''),
(2, 'mata2.jpg', '', 2, '0', '0', '', ''),
(3, '2023-06-05_19-14-37_2.jpg', '', 2, '', '', '', '');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(20) NOT NULL DEFAULT '',
  `email` varchar(100) NOT NULL DEFAULT '',
  `password` varchar(255) NOT NULL DEFAULT '',
  `nama` varchar(100) DEFAULT NULL,
  `no_hp` varchar(20) DEFAULT NULL,
  `tanggal_lahir` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `nama`, `no_hp`, `tanggal_lahir`) VALUES
(2, 'test', 'user1@example.com', '$2y$10$GPk9C1q3g4EthSIDlsmDGOHimobo3qhSSHnTCNFguybKUPcQzfpB.', '', '', '2023-06-03'),
(3, 'test2', 'user2@example.com', '$2y$10$nhygYCzayrGvYVKxX5ZPLuG2gADN1J31vszllvzMOxknpvzxQuQXm', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `data_deteksi`
--
ALTER TABLE `data_deteksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `data_deteksi`
--
ALTER TABLE `data_deteksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
