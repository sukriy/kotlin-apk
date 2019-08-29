-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Aug 29, 2019 at 04:45 AM
-- Server version: 10.3.16-MariaDB
-- PHP Version: 7.3.2

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `id9051801_prokop`
--

-- --------------------------------------------------------

--
-- Table structure for table `pinjaman`
--

CREATE TABLE `pinjaman` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `tenor` int(11) NOT NULL,
  `bunga` varchar(4) NOT NULL,
  `cicilan` int(11) NOT NULL,
  `keterangan` varchar(250) DEFAULT NULL,
  `tgl_acc_admin` datetime DEFAULT NULL,
  `id_admin` int(11) DEFAULT NULL,
  `acc_admin` tinyint(1) DEFAULT NULL,
  `note_admin` text DEFAULT NULL,
  `tgl_acc_ketua` datetime DEFAULT NULL,
  `id_ketua` int(11) DEFAULT NULL,
  `acc_ketua` tinyint(1) DEFAULT NULL,
  `note_ketua` text DEFAULT NULL,
  `flag` char(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `pinjaman`
--

INSERT INTO `pinjaman` (`id`, `id_user`, `nominal`, `tenor`, `bunga`, `cicilan`, `keterangan`, `tgl_acc_admin`, `id_admin`, `acc_admin`, `note_admin`, `tgl_acc_ketua`, `id_ketua`, `acc_ketua`, `note_ketua`, `flag`, `created_at`, `updated_at`) VALUES
(1, 3, 300000, 3, '2%', 102000, NULL, '2019-08-07 11:44:02', 2, 1, 'Ok', '2019-08-07 11:45:16', 1, 1, 'Sip', '5', '2019-08-07 04:43:26', '2019-08-07 13:13:08'),
(2, 4, 700000, 6, '2%', 119000, NULL, '2019-08-07 11:47:59', 2, 1, 'Sip', '2019-08-07 11:48:25', 1, 1, 'Ok', '5', '2019-08-07 04:47:19', '2019-08-07 13:13:10'),
(3, 5, 500000, 3, '2%', 170000, NULL, NULL, NULL, NULL, NULL, '2019-08-07 12:37:42', 1, 1, NULL, '5', '2019-08-07 05:37:08', '2019-08-07 13:13:11'),
(5, 7, 500000, 6, '2%', 85000, NULL, '2019-08-07 19:39:21', 2, 1, 'Ok', '2019-08-07 19:40:07', 1, 1, 'Disetujui', '5', '2019-08-07 12:25:37', '2019-08-12 10:18:04'),
(7, 9, 2000000, 6, '2%', 373334, NULL, NULL, NULL, NULL, NULL, '2019-08-08 16:24:03', 1, 1, 'ok', '5', '2019-08-08 16:22:42', '2019-08-12 10:18:05'),
(11, 6, 1000000, 2, '2%', 520000, NULL, '2019-08-14 09:58:19', 2, 1, 'Ok', '2019-08-14 10:05:54', 1, 0, NULL, '0', '2019-08-14 09:57:33', '2019-08-14 10:05:54'),
(14, 6, 1000000, 4, '2%', 270000, NULL, '2019-08-15 11:20:32', 2, 1, 'Ok', '2019-08-15 11:22:35', 1, 1, 'Disetujui', '4', '2019-08-15 00:30:23', '2019-08-15 11:22:51'),
(23, 7, 6000000, 6, '2%', 1120000, NULL, '2019-08-26 13:40:00', 2, 1, 'Diterima', '2019-08-26 13:41:31', 1, 1, 'Disetujui', '4', '2019-08-26 13:15:23', '2019-08-26 13:46:50');

-- --------------------------------------------------------

--
-- Table structure for table `saldo`
--

CREATE TABLE `saldo` (
  `id_user` int(11) NOT NULL,
  `nominal` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `saldo`
--

INSERT INTO `saldo` (`id_user`, `nominal`) VALUES
(3, 0),
(4, 0),
(5, 0),
(6, 850000),
(7, 850000),
(9, 650000),
(10, 300000);

-- --------------------------------------------------------

--
-- Table structure for table `sukarela`
--

CREATE TABLE `sukarela` (
  `id` int(11) NOT NULL,
  `id_user` int(11) NOT NULL,
  `nominal` int(11) NOT NULL,
  `keterangan` varchar(250) DEFAULT NULL,
  `flag` char(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `sukarela`
--

INSERT INTO `sukarela` (`id`, `id_user`, `nominal`, `keterangan`, `flag`, `created_at`, `updated_at`) VALUES
(1, 3, 300000, NULL, '2', '2019-08-07 03:47:37', '2019-08-07 11:25:03'),
(2, 6, 100000, NULL, '2', '2019-08-07 08:05:24', '2019-08-07 15:05:52'),
(3, 7, 200000, NULL, '2', '2019-08-07 15:03:08', '2019-08-07 22:03:50');

-- --------------------------------------------------------

--
-- Table structure for table `transaksi`
--

CREATE TABLE `transaksi` (
  `id` int(11) NOT NULL,
  `id_pembayaran` int(11) NOT NULL,
  `tgl_pembayaran` date NOT NULL,
  `nominal` int(11) NOT NULL,
  `jenis` varchar(250) NOT NULL,
  `pembayaran` varchar(250) NOT NULL,
  `gambar` varchar(250) DEFAULT NULL,
  `tgl_acc` datetime DEFAULT NULL,
  `id_acc` int(11) DEFAULT NULL,
  `note` text DEFAULT NULL,
  `flag` char(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `transaksi`
--

INSERT INTO `transaksi` (`id`, `id_pembayaran`, `tgl_pembayaran`, `nominal`, `jenis`, `pembayaran`, `gambar`, `tgl_acc`, `id_acc`, `note`, `flag`, `created_at`, `updated_at`) VALUES
(1, 3, '2019-08-07', 200000, 'Tunai', 'Wajib', NULL, NULL, NULL, NULL, '2', '2019-08-07 10:31:10', '2019-08-07 10:31:10'),
(3, 5, '2019-08-07', 200000, 'Tunai', 'Wajib', NULL, NULL, NULL, NULL, '2', '2019-08-07 10:31:16', '2019-08-07 10:31:16'),
(4, 1, '2019-08-07', 300000, 'Transfer', 'Sukarela', 'Sukarela_1.jpg', '2019-08-07 11:27:14', 2, NULL, '2', '2019-08-07 03:47:37', '2019-08-07 11:27:14'),
(5, 3, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:15:54', '2019-08-07 11:15:54'),
(7, 5, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:15:56', '2019-08-07 11:15:56'),
(8, 1, '2019-08-07', 102000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:49:06', '2019-08-07 11:49:06'),
(9, 3, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:49:06', '2019-08-07 11:49:06'),
(10, 2, '2019-08-07', 119000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:49:08', '2019-08-07 11:49:08'),
(12, 5, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 11:49:10', '2019-08-07 11:49:10'),
(13, 1, '2019-08-07', 102000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:02:47', '2019-08-07 12:02:47'),
(14, 3, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:02:47', '2019-08-07 12:02:47'),
(15, 2, '2019-08-07', 119000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:02:49', '2019-08-07 12:02:49'),
(17, 5, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:02:50', '2019-08-07 12:02:50'),
(20, 5, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:19:47', '2019-08-07 12:19:47'),
(21, 3, '2019-08-07', 170000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:38:06', '2019-08-07 12:38:06'),
(22, 5, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 12:38:06', '2019-08-07 12:38:06'),
(40, 1, '2019-08-07', 102000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 13:13:08', '2019-08-07 13:13:08'),
(41, 2, '2019-08-07', 476000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 13:13:10', '2019-08-07 13:13:10'),
(42, 3, '2019-08-07', 340000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-07 13:13:11', '2019-08-07 13:13:11'),
(43, 6, '2019-08-07', 200000, 'Transfer', 'Wajib', '43.jpg', NULL, NULL, NULL, '2', '2019-08-07 08:00:46', '2019-08-07 08:03:58'),
(44, 2, '2019-08-07', 100000, 'Transfer', 'Sukarela', 'Sukarela_2.jpg', '2019-08-07 15:05:52', 2, 'Ok', '2', '2019-08-07 08:05:24', '2019-08-07 15:05:52'),
(45, 6, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 15:16:05', '2019-08-07 15:16:05'),
(46, 6, '2019-08-07', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-07 15:24:52', '2019-08-07 15:24:52'),
(47, 7, '2019-08-07', 200000, 'Transfer', 'Wajib', '47.jpg', NULL, NULL, NULL, '2', '2019-08-07 11:31:19', '2019-08-07 11:32:07'),
(48, 3, '2019-08-07', 200000, 'Transfer', 'Sukarela', 'Sukarela_3.jpg', '2019-08-07 22:04:16', 1, 'Ok', '2', '2019-08-07 15:03:08', '2019-08-07 22:04:16'),
(49, 9, '2019-08-07', 200000, 'Transfer', 'Wajib', '49.jpg', NULL, NULL, NULL, '2', '2019-08-07 23:54:54', '2019-08-07 23:55:12'),
(50, 6, '2019-08-08', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-08 16:24:13', '2019-08-08 16:24:13'),
(51, 5, '2019-08-08', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-08 16:24:16', '2019-08-08 16:24:16'),
(52, 7, '2019-08-08', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-08 16:24:16', '2019-08-08 16:24:16'),
(53, 7, '2019-08-08', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-08 16:24:18', '2019-08-08 16:24:18'),
(54, 9, '2019-08-08', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-08 16:24:18', '2019-08-08 16:24:18'),
(55, 6, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:25', '2019-08-10 22:23:25'),
(56, 5, '2019-08-10', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:28', '2019-08-10 22:23:28'),
(57, 7, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:28', '2019-08-10 22:23:28'),
(58, 7, '2019-08-10', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:29', '2019-08-10 22:23:29'),
(59, 9, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:29', '2019-08-10 22:23:29'),
(60, 6, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:39', '2019-08-10 22:23:39'),
(61, 5, '2019-08-10', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:41', '2019-08-10 22:23:41'),
(62, 7, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:41', '2019-08-10 22:23:41'),
(63, 7, '2019-08-10', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:44', '2019-08-10 22:23:44'),
(64, 9, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:44', '2019-08-10 22:23:44'),
(65, 6, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:57', '2019-08-10 22:23:57'),
(66, 5, '2019-08-10', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:58', '2019-08-10 22:23:58'),
(67, 7, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:23:58', '2019-08-10 22:23:58'),
(68, 7, '2019-08-10', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:24:00', '2019-08-10 22:24:00'),
(69, 9, '2019-08-10', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-10 22:24:00', '2019-08-10 22:24:00'),
(70, 6, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:15:56', '2019-08-12 10:15:56'),
(71, 5, '2019-08-12', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:15:59', '2019-08-12 10:15:59'),
(72, 7, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:15:59', '2019-08-12 10:15:59'),
(73, 7, '2019-08-12', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:16:00', '2019-08-12 10:16:00'),
(74, 9, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:16:00', '2019-08-12 10:16:00'),
(75, 6, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:18:02', '2019-08-12 10:18:02'),
(76, 5, '2019-08-12', 85000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:18:04', '2019-08-12 10:18:04'),
(77, 7, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:18:04', '2019-08-12 10:18:04'),
(78, 7, '2019-08-12', 373334, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:18:05', '2019-08-12 10:18:05'),
(79, 9, '2019-08-12', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-12 10:18:05', '2019-08-12 10:18:05'),
(81, 6, '2019-08-14', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-14 07:57:35', '2019-08-14 07:57:35'),
(82, 7, '2019-08-14', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-14 07:57:38', '2019-08-14 07:57:38'),
(83, 9, '2019-08-14', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-14 07:57:39', '2019-08-14 07:57:39'),
(84, 10, '2019-08-15', 200000, 'Transfer', 'Wajib', '84.jpg', NULL, NULL, NULL, '2', '2019-08-15 11:13:26', '2019-08-15 11:17:09'),
(85, 14, '2019-08-15', 270000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-15 11:22:51', '2019-08-15 11:22:51'),
(86, 6, '2019-08-15', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-15 11:22:51', '2019-08-15 11:22:51'),
(87, 7, '2019-08-15', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-15 11:22:53', '2019-08-15 11:22:53'),
(88, 9, '2019-08-15', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-15 11:22:55', '2019-08-15 11:22:55'),
(89, 10, '2019-08-15', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-15 11:22:56', '2019-08-15 11:22:56'),
(90, 14, '2019-08-15', 500000, 'Transfer', 'Cicilan', '90.jpg', '2019-08-26 13:46:38', 1, NULL, '2', '2019-08-15 11:27:56', '2019-08-26 13:46:38'),
(92, 14, '2019-08-26', 270000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:47', '2019-08-26 13:46:47'),
(93, 6, '2019-08-26', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:47', '2019-08-26 13:46:47'),
(94, 23, '2019-08-26', 1120000, 'Transfer', 'Cicilan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:49', '2019-08-26 13:46:49'),
(95, 7, '2019-08-26', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:50', '2019-08-26 13:46:50'),
(96, 9, '2019-08-26', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:52', '2019-08-26 13:46:52'),
(97, 10, '2019-08-26', 50000, 'Transfer', 'Bulanan', NULL, NULL, NULL, NULL, '2', '2019-08-26 13:46:53', '2019-08-26 13:46:53');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(250) NOT NULL,
  `namalengkap` varchar(250) NOT NULL,
  `password` varchar(250) NOT NULL,
  `email` varchar(250) NOT NULL,
  `jenis_kelamin` tinyint(1) DEFAULT NULL,
  `jabatan` varchar(250) DEFAULT NULL,
  `alamat` varchar(250) DEFAULT NULL,
  `telepon` varchar(250) DEFAULT NULL,
  `gaji` int(11) DEFAULT NULL,
  `level` varchar(250) NOT NULL DEFAULT 'Anggota',
  `api_token` varchar(250) DEFAULT NULL,
  `gambar` varchar(250) DEFAULT NULL,
  `tgl_join` date DEFAULT NULL,
  `tgl_resign` date DEFAULT NULL,
  `flag` char(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `namalengkap`, `password`, `email`, `jenis_kelamin`, `jabatan`, `alamat`, `telepon`, `gaji`, `level`, `api_token`, `gambar`, `tgl_join`, `tgl_resign`, `flag`, `created_at`, `updated_at`) VALUES
(1, 'ketua1', 'ketua1', '$2y$10$4hU3pxZkNTjPoGWJ9/iI8ON62VCml7tRcUXeQFvl6ep3z0kuhcGBS', 'topan.arashi90@gmail.com', 1, 'SPV', 'Jakarta', '1234567890', 0, 'Ketua', 'RN1yqhelBoM0MQbhO2fRe8hkcc90NMUDrSrDktEa2rayvUSYkGMOuLqP2377', NULL, '2017-01-01', NULL, '2', NULL, '2019-08-07 07:48:14'),
(2, 'Admin1', 'Admin Operator', '$2y$10$EOYc9jS8xDDlHGg1o3kEHOR0pmoHm5JkEfyRaJIhFnQAcANVdeXgG', 'admin1@mailinator.com', 1, 'SPV', 'fdfdf', '434343', 2000000, 'Admin', 'pweUhpXuwjtuMVIeifBu3ClXomKEEHbMWRc3weGtDAkU8mpKksQKBdQCV8IX', '', '2019-08-16', NULL, '2', '2019-08-06 02:02:11', '2019-08-06 09:29:38'),
(3, 'anggota1', 'anggota1', '$2y$10$LAv.3ke969WPCY7IXbXySuFu9y3bcUjaIguYnaOKwb4NNfyor2RUa', 'abc@xxx.xx', 0, 'Staff', 'abcksnj', '6408769', 500000, 'Anggota', 'YiOZIvKF9zzNcrLywIz4eZlLblKr8SQHl0S5gnDgNOD7lyIVoNcmO22NiQuh', '', '2017-11-14', '2019-08-07', '4', '2019-08-06 02:05:15', '2019-08-07 07:46:34'),
(4, 'Anggota2', 'Anggota2', '$2y$10$Bd/7DcRRsklX8mHP7Vf6ROW0tOA7hvfTjjpkwmwPIN1BflJCMP9Sq', 'def@xxx.xx', 1, 'Staff', 'Jakbar', '123456789', 5000000, 'Anggota', '7UoCXzVDEcz1TeUPArjoxQxNz8mfGRqyF5ETieRavopLuuRFvMBSP9APAKSO', '', '2017-12-14', '2019-08-07', '4', '2019-08-06 04:10:18', '2019-08-07 07:46:54'),
(5, 'Anggota3', 'Anggota3', '$2y$10$TAv8sQYGVNyxF0rEeSnzKu8HxJlL0zusk.UwSUE0NclGvVIsBW6KO', 'ghi@xxx.xx', 0, 'Staff', 'Jakut', '959866', 3500000, 'Anggota', 'xpSSnMLZqYsiW5TImYzwMwrQJHWy5cVEwCG4LPcHpl5EA9uLlzp0v1WlHQXx', '', '2017-10-10', '2019-08-23', '4', '2019-08-07 01:00:28', '2019-08-07 07:47:13'),
(6, 'Anggota4', 'Anggota4', '$2y$10$/1vdzF8bDEiIfUx9RoZoPO9jIQe5.HCEOMBSqMIhyX/kxGFKysZRa', 'anggotaanggota4@gmail.com', 1, 'Staff', 'Jogja', '885656', 5000000, 'Anggota', 'dwdGD74xbb9TiDvdzD40LHJpTNKbbubjaif4XMquX4wumqqeYhxDIYiCTVeB', '', '2017-10-10', NULL, '2', '2019-08-07 08:00:31', '2019-08-07 11:39:45'),
(7, 'Anggota5', 'Anggota5', '$2y$10$/oYJZCkDTvLjF5ECDE5J.e3L7HYFCPeWpXY1aQWGUZT47yw4cvroy', 'taufani1490@gmail.com', 1, 'Staff', 'Jaktim', '65555855', 4500000, 'Anggota', 'LhqP3NjzAfyALKsBNRMkls8nhIqeNA8n9xcmeYDTVNQ65F9udRi15Juvvpwf', '', '2015-08-15', NULL, '2', '2019-08-07 11:30:57', '2019-08-15 01:48:26'),
(9, 'anggota6', 'anggota6', '$2y$10$axZcck5xvlZK8BQ1ThMdyesC5UQyOSe.G8vInwSM2bqOOrRLGrzEi', 'anggota6@mailinator.com', 0, 'Staff', 'r4r4f', '08223481122', 2000000, 'Anggota', 'Y9TiH6uFRFLccqcHDH1z5CzkN1z9kdWbO4NOlwENyW1CqmUY5k0tA3miPY3Q', '', '2017-08-22', NULL, '2', '2019-08-07 23:54:03', '2019-08-07 23:55:45'),
(10, 'Anggota7', 'Anggota7', '$2y$10$PHlnai5nBHQkFmDapzRD2.S4FUAp0xG6ZO3lmqhckBztXZa0odAmG', 'topan.arashi@gmail.com', 1, 'Staff', 'Jaktim', '1234567890', 4500000, 'Anggota', '7qkCsT1YGtdKXWhiaKD7pX739gvS3x58o2MMU6shVkCjcchOyJ4EKHxj3aAq', '', '2015-08-14', NULL, '2', '2019-08-15 11:11:59', '2019-08-15 11:17:07');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `saldo`
--
ALTER TABLE `saldo`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `id_user` (`id_user`);

--
-- Indexes for table `sukarela`
--
ALTER TABLE `sukarela`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `transaksi`
--
ALTER TABLE `transaksi`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `api_token` (`api_token`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `pinjaman`
--
ALTER TABLE `pinjaman`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `sukarela`
--
ALTER TABLE `sukarela`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `transaksi`
--
ALTER TABLE `transaksi`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=98;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `pinjaman`
--
ALTER TABLE `pinjaman`
  ADD CONSTRAINT `pinjaman_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `saldo`
--
ALTER TABLE `saldo`
  ADD CONSTRAINT `saldo_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);

--
-- Constraints for table `sukarela`
--
ALTER TABLE `sukarela`
  ADD CONSTRAINT `sukarela_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
