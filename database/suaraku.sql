-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 19, 2024 at 12:20 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.1.25

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `suaraku`
--

-- --------------------------------------------------------

--
-- Table structure for table `laporan`
--

CREATE TABLE `laporan` (
  `id_laporan` int(11) NOT NULL,
  `tgl_laporan` varchar(40) NOT NULL,
  `nama_pengguna` varchar(40) NOT NULL,
  `isi_laporan` text NOT NULL,
  `tlp` varchar(13) NOT NULL,
  `alamat` varchar(100) NOT NULL,
  `bukti` varchar(255) CHARACTER SET armscii8 COLLATE armscii8_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `laporan`
--

INSERT INTO `laporan` (`id_laporan`, `tgl_laporan`, `nama_pengguna`, `isi_laporan`, `tlp`, `alamat`, `bukti`) VALUES
(118, '2024-11-14 11:24:31', 'sugi', 'terbentur, terbentur , terbentuk', '085932931415', 'JL. trans sulawesi', 'tio.png'),
(119, '2024-11-14 11:33:55', 'siti', 'ini saya bernama siti', '08115310223', 'JL. Madagaskar', 'siti1.png'),
(120, '2024-11-14 11:35:07', 'zia', 'saya seorang bernama ziaku', '085932931415', 'JL. Madagaskar', 'fauzia2.png'),
(121, '2024-11-14 11:37:06', 'sugi', 'sugi bilang rehan paling cool', '085932931415', 'Jl. Tanjung Pinang', 'rehan.mp4'),
(122, '2024-12-02 11:04:07', 'sugi', 'iyaa', '085932931415', 'oke', 'Screenshot 2024-11-30 062443.png');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `laporan`
--
ALTER TABLE `laporan`
  ADD PRIMARY KEY (`id_laporan`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `laporan`
--
ALTER TABLE `laporan`
  MODIFY `id_laporan` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=123;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
