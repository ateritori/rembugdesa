-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Apr 15, 2026 at 04:41 PM
-- Server version: 8.0.45
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `rembugdesa`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatives`
--

CREATE TABLE `alternatives` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `code` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `rab` bigint UNSIGNED DEFAULT NULL,
  `coverage` int UNSIGNED DEFAULT NULL,
  `beneficiaries` int UNSIGNED DEFAULT NULL,
  `criteria_id` bigint UNSIGNED DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alternatives`
--

INSERT INTO `alternatives` (`id`, `decision_session_id`, `code`, `name`, `order`, `is_active`, `rab`, `coverage`, `beneficiaries`, `criteria_id`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'A1', 'Pelatihan administrasi dasar dan tata kelola bagi pengurus RT dan RW', 1, 1, 7500000, 100, 80, 1, NULL, '2026-04-14 14:16:22', '2026-04-15 14:59:43'),
(2, 1, 'A2', 'Peningkatan akses jalan penghubung antar padukuhan sepanjang ±400 meter', 2, 1, 150000000, 75, 800, 2, NULL, '2026-04-14 14:16:53', '2026-04-15 15:00:10'),
(3, 1, 'A3', 'Program pencegahan stunting melalui pemberian makanan tambahan bagi balita berisiko', 3, 1, 30000000, 100, 50, 1, NULL, '2026-04-14 14:17:15', '2026-04-15 16:30:52'),
(4, 1, 'A4', 'Digitalisasi layanan administrasi desa dan arsip surat-menyurat', 4, 1, 10000000, 100, 500, 4, NULL, '2026-04-14 14:17:39', '2026-04-15 15:00:43'),
(5, 1, 'A5', 'Pengadaan sarana produksi peternakan atau perikanan skala rumah tangga', 5, 1, 40000000, 50, 100, 3, NULL, '2026-04-14 14:18:01', '2026-04-14 14:18:01'),
(6, 1, 'A6', 'Rehabilitasi jembatan lingkungan kecil untuk akses pejalan kaki', 6, 1, 8000000, 25, 500, 2, NULL, '2026-04-14 14:18:18', '2026-04-14 14:18:18'),
(7, 1, 'A7', 'Pelatihan pencatatan keuangan dan manajemen usaha sederhana bagi UMKM desa', 7, 1, 20000000, 100, 60, 3, NULL, '2026-04-14 14:18:39', '2026-04-14 14:18:39'),
(8, 1, 'A8', 'Pemasangan rambu keselamatan dan marka jalan pada titik rawan kecelakaan', 8, 1, 25000000, 100, 100, 5, NULL, '2026-04-14 14:18:58', '2026-04-15 15:00:57'),
(9, 1, 'A9', 'Peningkatan layanan Poskesdes desa', 9, 1, 15000000, 100, 120, 1, NULL, '2026-04-14 14:19:21', '2026-04-15 16:32:25'),
(10, 1, 'A10', 'Peningkatan akses jalan lingkungan dan gang sempit sepanjang ±500 meter di wilayah padat penduduk', 10, 1, 150000000, 50, 800, 2, NULL, '2026-04-14 14:19:47', '2026-04-14 14:19:47'),
(11, 1, 'A11', 'Penyelenggaraan kegiatan kepemudaan rutin Karang Taruna tingkat desa', 11, 1, 30000000, 100, 200, 1, NULL, '2026-04-14 14:20:04', '2026-04-14 14:20:04'),
(12, 1, 'A12', 'Normalisasi dan perbaikan saluran drainase eksisting pada kawasan rawan genangan', 12, 1, 150000000, 50, 700, 2, NULL, '2026-04-14 14:20:26', '2026-04-14 14:20:26'),
(13, 1, 'A13', 'Pemasangan papan informasi dan penunjuk arah wilayah desa', 13, 1, 10000000, 100, 200, 4, NULL, '2026-04-14 14:20:50', '2026-04-15 15:01:55'),
(14, 1, 'A14', 'Pendampingan teknis budidaya pertanian bagi satu kelompok tani aktif', 14, 1, 5000000, 50, 50, 3, NULL, '2026-04-14 14:21:11', '2026-04-15 15:02:30'),
(15, 1, 'A15', 'Rehabilitasi satu bangunan publik desa dengan tingkat kerusakan sedang - berat', 15, 1, 30000000, 100, 100, 2, NULL, '2026-04-14 14:21:53', '2026-04-15 15:02:14'),
(16, 1, 'A16', 'Pelaksanaan simulasi kesiapsiagaan kebencanaan desa satu kali dalam setahun', 16, 1, 15000000, 100, 300, 5, NULL, '2026-04-14 14:22:09', '2026-04-14 14:22:09'),
(17, 1, 'A17', 'Pendampingan legalitas usaha dan akses permodalan bagi UMKM desa', 17, 1, 30000000, 100, 80, 3, NULL, '2026-04-14 14:22:27', '2026-04-14 14:22:27'),
(18, 1, 'A18', 'Penyediaan dan penataan lokasi pemakaman desa', 18, 1, 50000000, 100, 300, 2, NULL, '2026-04-14 14:22:50', '2026-04-15 15:02:52'),
(19, 1, 'A19', 'Kegiatan pengendalian penyakit berbasis lingkungan', 19, 1, 15000000, 100, 80, 1, NULL, '2026-04-14 14:23:16', '2026-04-15 15:03:20'),
(20, 1, 'A20', 'Pelatihan keterampilan kerja berbasis kebutuhan lokal bagi warga usia produktif', 20, 1, 35000000, 100, 100, 3, NULL, '2026-04-14 14:23:40', '2026-04-14 14:23:40'),
(21, 1, 'A21', 'Penguatan talud kali pada satu titik rawan longsor atau erosi', 21, 1, 150000000, 50, 600, 2, NULL, '2026-04-14 14:24:00', '2026-04-14 14:24:00'),
(22, 1, 'A22', 'Pembentukan dan operasionalisasi sistem ronda malam berbasis warga', 22, 1, 20000000, 0, 400, 5, NULL, '2026-04-14 14:24:20', '2026-04-14 14:24:20'),
(23, 1, 'A23', 'Pembangunan saluran drainase baru pada satu titik genangan prioritas', 23, 1, 150000000, 50, 500, 2, NULL, '2026-04-14 14:24:37', '2026-04-14 14:24:37'),
(24, 1, 'A24', 'Pemasangan 10 titik lampu penerangan jalan umum di area minim pencahayaan', 24, 1, 50000000, 25, 600, 5, NULL, '2026-04-14 14:24:56', '2026-04-14 14:24:56');

-- --------------------------------------------------------

--
-- Table structure for table `alternative_evaluations`
--

CREATE TABLE `alternative_evaluations` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `dm_id` bigint UNSIGNED NOT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `raw_value` decimal(15,5) NOT NULL,
  `utility_value` decimal(6,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `borda_aggregations`
--

CREATE TABLE `borda_aggregations` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `method` enum('SMART','SAW') COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` enum('group','system','final') COLLATE utf8mb4_unicode_ci NOT NULL,
  `source` enum('partisipatif','strategis','system','final') COLLATE utf8mb4_unicode_ci NOT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `borda_score` double NOT NULL,
  `rank` int NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `borda_aggregations`
--

INSERT INTO `borda_aggregations` (`id`, `decision_session_id`, `method`, `level`, `source`, `alternative_id`, `borda_score`, `rank`, `created_at`, `updated_at`) VALUES
(1, 1, 'SMART', 'group', 'partisipatif', 9, 19, 6, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(2, 1, 'SMART', 'group', 'partisipatif', 19, 24, 1, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(3, 1, 'SMART', 'group', 'partisipatif', 3, 20, 5, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(4, 1, 'SMART', 'group', 'partisipatif', 5, 15, 10, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(5, 1, 'SMART', 'group', 'partisipatif', 6, 10, 15, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(6, 1, 'SMART', 'group', 'partisipatif', 15, 16, 9, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(7, 1, 'SMART', 'group', 'partisipatif', 21, 12, 13, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(8, 1, 'SMART', 'group', 'partisipatif', 23, 8, 17, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(9, 1, 'SMART', 'group', 'partisipatif', 1, 18, 7, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(10, 1, 'SMART', 'group', 'partisipatif', 2, 11, 14, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(11, 1, 'SMART', 'group', 'partisipatif', 10, 9, 16, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(12, 1, 'SMART', 'group', 'partisipatif', 12, 13, 12, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(13, 1, 'SMART', 'group', 'partisipatif', 11, 17, 8, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(14, 1, 'SMART', 'group', 'partisipatif', 14, 14, 11, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(15, 1, 'SMART', 'group', 'partisipatif', 24, 7, 18, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(16, 1, 'SMART', 'group', 'partisipatif', 7, 23, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(17, 1, 'SMART', 'group', 'partisipatif', 17, 22, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(18, 1, 'SMART', 'group', 'partisipatif', 20, 21, 4, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(19, 1, 'SMART', 'group', 'partisipatif', 8, 5, 20, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(20, 1, 'SMART', 'group', 'partisipatif', 16, 4, 21, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(21, 1, 'SMART', 'group', 'partisipatif', 22, 2, 23, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(22, 1, 'SMART', 'group', 'partisipatif', 18, 6, 19, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(23, 1, 'SMART', 'group', 'partisipatif', 4, 3, 22, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(24, 1, 'SMART', 'group', 'partisipatif', 13, 1, 24, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(25, 1, 'SMART', 'group', 'strategis', 3, 12, 13, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(26, 1, 'SMART', 'group', 'strategis', 19, 9, 16, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(27, 1, 'SMART', 'group', 'strategis', 9, 11, 14, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(28, 1, 'SMART', 'group', 'strategis', 12, 19, 6, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(29, 1, 'SMART', 'group', 'strategis', 18, 16, 9, '2026-04-15 13:55:48', '2026-04-15 14:27:13'),
(30, 1, 'SMART', 'group', 'strategis', 23, 18, 7, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(31, 1, 'SMART', 'group', 'strategis', 2, 8, 17, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(32, 1, 'SMART', 'group', 'strategis', 10, 7, 18, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(33, 1, 'SMART', 'group', 'strategis', 21, 3, 22, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(34, 1, 'SMART', 'group', 'strategis', 5, 24, 1, '2026-04-15 13:55:48', '2026-04-15 14:27:13'),
(35, 1, 'SMART', 'group', 'strategis', 7, 23, 2, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(36, 1, 'SMART', 'group', 'strategis', 1, 13, 12, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(37, 1, 'SMART', 'group', 'strategis', 11, 10, 15, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(38, 1, 'SMART', 'group', 'strategis', 6, 4, 21, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(39, 1, 'SMART', 'group', 'strategis', 15, 17, 8, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(40, 1, 'SMART', 'group', 'strategis', 16, 15, 10, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(41, 1, 'SMART', 'group', 'strategis', 22, 14, 11, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(42, 1, 'SMART', 'group', 'strategis', 24, 5, 20, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(43, 1, 'SMART', 'group', 'strategis', 14, 22, 3, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(44, 1, 'SMART', 'group', 'strategis', 17, 21, 4, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(45, 1, 'SMART', 'group', 'strategis', 20, 20, 5, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(46, 1, 'SMART', 'group', 'strategis', 8, 6, 19, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(47, 1, 'SMART', 'group', 'strategis', 4, 2, 23, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(48, 1, 'SMART', 'group', 'strategis', 13, 1, 24, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(49, 1, 'SMART', 'system', 'system', 19, 21, 4, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(50, 1, 'SMART', 'system', 'system', 9, 24, 1, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(51, 1, 'SMART', 'system', 'system', 11, 23, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(52, 1, 'SMART', 'system', 'system', 3, 20, 5, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(53, 1, 'SMART', 'system', 'system', 1, 22, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(54, 1, 'SMART', 'system', 'system', 7, 17, 8, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(55, 1, 'SMART', 'system', 'system', 20, 19, 6, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(56, 1, 'SMART', 'system', 'system', 17, 18, 7, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(57, 1, 'SMART', 'system', 'system', 2, 9, 16, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(58, 1, 'SMART', 'system', 'system', 18, 16, 9, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(59, 1, 'SMART', 'system', 'system', 8, 11, 14, '2026-04-15 13:55:48', '2026-04-15 16:26:32'),
(60, 1, 'SMART', 'system', 'system', 15, 15, 10, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(61, 1, 'SMART', 'system', 'system', 5, 14, 11, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(62, 1, 'SMART', 'system', 'system', 14, 10, 15, '2026-04-15 13:55:48', '2026-04-15 16:26:32'),
(63, 1, 'SMART', 'system', 'system', 6, 13, 12, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(64, 1, 'SMART', 'system', 'system', 16, 12, 13, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(65, 1, 'SMART', 'system', 'system', 13, 6, 19, '2026-04-15 13:55:48', '2026-04-15 16:27:50'),
(66, 1, 'SMART', 'system', 'system', 4, 8, 17, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(67, 1, 'SMART', 'system', 'system', 22, 7, 18, '2026-04-15 13:55:48', '2026-04-15 16:26:32'),
(68, 1, 'SMART', 'system', 'system', 10, 4, 21, '2026-04-15 13:55:48', '2026-04-15 16:27:50'),
(69, 1, 'SMART', 'system', 'system', 24, 5, 20, '2026-04-15 13:55:48', '2026-04-15 16:27:50'),
(70, 1, 'SMART', 'system', 'system', 12, 3, 22, '2026-04-15 13:55:48', '2026-04-15 16:27:50'),
(71, 1, 'SMART', 'system', 'system', 21, 2, 23, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(72, 1, 'SMART', 'system', 'system', 23, 1, 24, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(73, 1, 'SMART', 'final', 'final', 19, 54, 5, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(74, 1, 'SMART', 'final', 'final', 9, 54, 4, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(75, 1, 'SMART', 'final', 'final', 3, 52, 8, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(76, 1, 'SMART', 'final', 'final', 2, 28, 15, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(77, 1, 'SMART', 'final', 'final', 1, 53, 7, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(78, 1, 'SMART', 'final', 'final', 5, 53, 6, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(79, 1, 'SMART', 'final', 'final', 11, 50, 9, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(80, 1, 'SMART', 'final', 'final', 7, 63, 1, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(81, 1, 'SMART', 'final', 'final', 15, 48, 10, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(82, 1, 'SMART', 'final', 'final', 6, 27, 17, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(83, 1, 'SMART', 'final', 'final', 18, 38, 12, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(84, 1, 'SMART', 'final', 'final', 12, 35, 13, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(85, 1, 'SMART', 'final', 'final', 23, 27, 16, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(86, 1, 'SMART', 'final', 'final', 10, 20, 20, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(87, 1, 'SMART', 'final', 'final', 21, 17, 22, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(88, 1, 'SMART', 'final', 'final', 17, 61, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(89, 1, 'SMART', 'final', 'final', 20, 60, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(90, 1, 'SMART', 'final', 'final', 14, 46, 11, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(91, 1, 'SMART', 'final', 'final', 16, 31, 14, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(92, 1, 'SMART', 'final', 'final', 8, 22, 19, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(93, 1, 'SMART', 'final', 'final', 24, 17, 21, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(94, 1, 'SMART', 'final', 'final', 22, 23, 18, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(95, 1, 'SMART', 'final', 'final', 4, 13, 23, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(96, 1, 'SMART', 'final', 'final', 13, 8, 24, '2026-04-15 13:55:48', '2026-04-15 16:27:50'),
(97, 1, 'SAW', 'group', 'partisipatif', 9, 22, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(98, 1, 'SAW', 'group', 'partisipatif', 19, 24, 1, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(99, 1, 'SAW', 'group', 'partisipatif', 3, 23, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(100, 1, 'SAW', 'group', 'partisipatif', 1, 21, 4, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(101, 1, 'SAW', 'group', 'partisipatif', 5, 15, 10, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(102, 1, 'SAW', 'group', 'partisipatif', 11, 20, 5, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(103, 1, 'SAW', 'group', 'partisipatif', 6, 10, 15, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(104, 1, 'SAW', 'group', 'partisipatif', 15, 16, 9, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(105, 1, 'SAW', 'group', 'partisipatif', 21, 12, 13, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(106, 1, 'SAW', 'group', 'partisipatif', 23, 8, 17, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(107, 1, 'SAW', 'group', 'partisipatif', 2, 11, 14, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(108, 1, 'SAW', 'group', 'partisipatif', 10, 9, 16, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(109, 1, 'SAW', 'group', 'partisipatif', 12, 13, 12, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(110, 1, 'SAW', 'group', 'partisipatif', 14, 14, 11, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(111, 1, 'SAW', 'group', 'partisipatif', 7, 19, 6, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(112, 1, 'SAW', 'group', 'partisipatif', 17, 18, 7, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(113, 1, 'SAW', 'group', 'partisipatif', 20, 17, 8, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(114, 1, 'SAW', 'group', 'partisipatif', 24, 7, 18, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(115, 1, 'SAW', 'group', 'partisipatif', 8, 5, 20, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(116, 1, 'SAW', 'group', 'partisipatif', 18, 6, 19, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(117, 1, 'SAW', 'group', 'partisipatif', 16, 4, 21, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(118, 1, 'SAW', 'group', 'partisipatif', 22, 3, 22, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(119, 1, 'SAW', 'group', 'partisipatif', 4, 2, 23, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(120, 1, 'SAW', 'group', 'partisipatif', 13, 1, 24, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(121, 1, 'SAW', 'group', 'strategis', 3, 18, 7, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(122, 1, 'SAW', 'group', 'strategis', 19, 15, 10, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(123, 1, 'SAW', 'group', 'strategis', 9, 17, 8, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(124, 1, 'SAW', 'group', 'strategis', 1, 19, 6, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(125, 1, 'SAW', 'group', 'strategis', 11, 16, 9, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(126, 1, 'SAW', 'group', 'strategis', 12, 14, 11, '2026-04-15 13:55:48', '2026-04-15 14:27:13'),
(127, 1, 'SAW', 'group', 'strategis', 18, 11, 14, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(128, 1, 'SAW', 'group', 'strategis', 23, 13, 12, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(129, 1, 'SAW', 'group', 'strategis', 5, 24, 1, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(130, 1, 'SAW', 'group', 'strategis', 7, 23, 2, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(131, 1, 'SAW', 'group', 'strategis', 2, 8, 17, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(132, 1, 'SAW', 'group', 'strategis', 10, 7, 18, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(133, 1, 'SAW', 'group', 'strategis', 21, 5, 20, '2026-04-15 13:55:48', '2026-04-15 14:27:13'),
(134, 1, 'SAW', 'group', 'strategis', 6, 6, 19, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(135, 1, 'SAW', 'group', 'strategis', 15, 12, 13, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(136, 1, 'SAW', 'group', 'strategis', 14, 22, 3, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(137, 1, 'SAW', 'group', 'strategis', 17, 21, 4, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(138, 1, 'SAW', 'group', 'strategis', 20, 20, 5, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(139, 1, 'SAW', 'group', 'strategis', 16, 10, 15, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(140, 1, 'SAW', 'group', 'strategis', 22, 9, 16, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(141, 1, 'SAW', 'group', 'strategis', 24, 3, 22, '2026-04-15 13:55:48', '2026-04-15 14:27:13'),
(142, 1, 'SAW', 'group', 'strategis', 8, 4, 21, '2026-04-15 13:55:48', '2026-04-15 14:33:39'),
(143, 1, 'SAW', 'group', 'strategis', 4, 2, 23, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(144, 1, 'SAW', 'group', 'strategis', 13, 1, 24, '2026-04-15 13:55:48', '2026-04-15 13:55:48'),
(145, 1, 'SAW', 'system', 'system', 19, 21, 4, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(146, 1, 'SAW', 'system', 'system', 9, 23, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(147, 1, 'SAW', 'system', 'system', 11, 24, 1, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(148, 1, 'SAW', 'system', 'system', 3, 20, 5, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(149, 1, 'SAW', 'system', 'system', 7, 19, 6, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(150, 1, 'SAW', 'system', 'system', 17, 18, 7, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(151, 1, 'SAW', 'system', 'system', 1, 22, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(152, 1, 'SAW', 'system', 'system', 20, 17, 8, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(153, 1, 'SAW', 'system', 'system', 2, 11, 14, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(154, 1, 'SAW', 'system', 'system', 14, 16, 9, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(155, 1, 'SAW', 'system', 'system', 18, 15, 10, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(156, 1, 'SAW', 'system', 'system', 5, 14, 11, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(157, 1, 'SAW', 'system', 'system', 8, 7, 18, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(158, 1, 'SAW', 'system', 'system', 15, 13, 12, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(159, 1, 'SAW', 'system', 'system', 6, 12, 13, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(160, 1, 'SAW', 'system', 'system', 16, 9, 16, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(161, 1, 'SAW', 'system', 'system', 13, 1, 24, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(162, 1, 'SAW', 'system', 'system', 4, 3, 22, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(163, 1, 'SAW', 'system', 'system', 10, 10, 15, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(164, 1, 'SAW', 'system', 'system', 12, 8, 17, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(165, 1, 'SAW', 'system', 'system', 24, 5, 20, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(166, 1, 'SAW', 'system', 'system', 21, 6, 19, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(167, 1, 'SAW', 'system', 'system', 23, 4, 21, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(168, 1, 'SAW', 'system', 'system', 22, 2, 23, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(169, 1, 'SAW', 'final', 'final', 19, 60, 6, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(170, 1, 'SAW', 'final', 'final', 9, 62, 2, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(171, 1, 'SAW', 'final', 'final', 3, 61, 4, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(172, 1, 'SAW', 'final', 'final', 11, 60, 5, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(173, 1, 'SAW', 'final', 'final', 1, 62, 1, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(174, 1, 'SAW', 'final', 'final', 5, 53, 9, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(175, 1, 'SAW', 'final', 'final', 7, 61, 3, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(176, 1, 'SAW', 'final', 'final', 2, 30, 14, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(177, 1, 'SAW', 'final', 'final', 6, 28, 15, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(178, 1, 'SAW', 'final', 'final', 15, 41, 11, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(179, 1, 'SAW', 'final', 'final', 18, 32, 13, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(180, 1, 'SAW', 'final', 'final', 12, 35, 12, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(181, 1, 'SAW', 'final', 'final', 17, 57, 7, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(182, 1, 'SAW', 'final', 'final', 14, 52, 10, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(183, 1, 'SAW', 'final', 'final', 23, 25, 17, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(184, 1, 'SAW', 'final', 'final', 10, 26, 16, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(185, 1, 'SAW', 'final', 'final', 20, 54, 8, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(186, 1, 'SAW', 'final', 'final', 21, 23, 19, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(187, 1, 'SAW', 'final', 'final', 8, 16, 20, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(188, 1, 'SAW', 'final', 'final', 16, 23, 18, '2026-04-15 13:55:48', '2026-04-15 16:35:03'),
(189, 1, 'SAW', 'final', 'final', 24, 15, 21, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(190, 1, 'SAW', 'final', 'final', 4, 7, 23, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(191, 1, 'SAW', 'final', 'final', 13, 3, 24, '2026-04-15 13:55:48', '2026-04-15 16:23:40'),
(192, 1, 'SAW', 'final', 'final', 22, 14, 22, '2026-04-15 13:55:48', '2026-04-15 16:23:40');

-- --------------------------------------------------------

--
-- Table structure for table `borda_results`
--

CREATE TABLE `borda_results` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `borda_score` int UNSIGNED NOT NULL,
  `final_rank` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `level` tinyint NOT NULL,
  `type` enum('benefit','cost') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `evaluator_type` enum('system','human') COLLATE utf8mb4_unicode_ci DEFAULT 'human',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `decision_session_id`, `name`, `level`, `type`, `is_active`, `evaluator_type`, `order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Pelayanan Dasar', 1, 'benefit', 1, 'human', 1, '2026-04-14 13:59:11', '2026-04-14 13:59:11', NULL),
(2, 1, 'Infrastruktur dan Lingkungan', 1, 'benefit', 1, 'human', 2, '2026-04-14 13:59:29', '2026-04-14 13:59:29', NULL),
(3, 1, 'Ekonomi Produktif dan Pertanian', 1, 'benefit', 1, 'human', 3, '2026-04-14 13:59:52', '2026-04-14 13:59:52', NULL),
(4, 1, 'Teknologi Tepat Guna', 1, 'benefit', 1, 'human', 4, '2026-04-14 14:00:03', '2026-04-14 14:00:03', NULL),
(5, 1, 'Keamanan dan Ketertiban', 1, 'benefit', 1, 'human', 5, '2026-04-14 14:00:18', '2026-04-14 14:00:18', NULL),
(6, 1, 'Rencana Anggaran Biaya (RAB)', 2, 'cost', 1, 'system', 6, '2026-04-14 14:00:53', '2026-04-14 14:00:53', NULL),
(7, 1, 'Penerima Manfaat', 2, 'benefit', 1, 'system', 7, '2026-04-14 14:01:08', '2026-04-14 14:01:08', NULL),
(8, 1, 'Cakupan Wilayah', 2, 'benefit', 1, 'system', 8, '2026-04-14 14:01:24', '2026-04-14 14:01:24', NULL),
(9, 1, 'Keselarasan Visi & Misi', 2, 'benefit', 1, 'human', 9, '2026-04-14 14:01:46', '2026-04-14 14:01:46', NULL),
(10, 1, 'Urgensi', 2, 'benefit', 1, 'human', 10, '2026-04-14 14:02:18', '2026-04-14 14:02:18', NULL),
(11, 1, 'Dampak', 2, 'benefit', 1, 'human', 11, '2026-04-14 14:02:28', '2026-04-14 14:02:28', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `criteria_group_weights`
--

CREATE TABLE `criteria_group_weights` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `weights` json NOT NULL,
  `cr` decimal(8,5) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_group_weights`
--

INSERT INTO `criteria_group_weights` (`id`, `decision_session_id`, `weights`, `cr`, `created_at`, `updated_at`) VALUES
(1, 1, '{\"1\": 0.10404893425997662, \"2\": 0.21825495711384765, \"3\": 0.2551012819518793, \"4\": 0.16896321730921274, \"5\": 0.2536316093650837}', 0.01820, '2026-04-14 14:47:44', '2026-04-15 16:33:20');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_pairwise`
--

CREATE TABLE `criteria_pairwise` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `dm_id` bigint UNSIGNED NOT NULL,
  `criteria_id_1` bigint UNSIGNED NOT NULL,
  `criteria_id_2` bigint UNSIGNED NOT NULL,
  `value` tinyint UNSIGNED NOT NULL,
  `direction` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_pairwise`
--

INSERT INTO `criteria_pairwise` (`id`, `decision_session_id`, `dm_id`, `criteria_id_1`, `criteria_id_2`, `value`, `direction`, `created_at`, `updated_at`) VALUES
(11, 1, 3, 1, 2, 8, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(12, 1, 3, 1, 3, 3, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(13, 1, 3, 1, 4, 7, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(14, 1, 3, 1, 5, 4, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(15, 1, 3, 2, 3, 5, 'left', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(16, 1, 3, 2, 4, 3, 'left', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(17, 1, 3, 2, 5, 4, 'left', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(18, 1, 3, 3, 4, 4, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(19, 1, 3, 3, 5, 3, 'right', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(20, 1, 3, 4, 5, 3, 'left', '2026-04-14 14:33:44', '2026-04-14 14:33:44'),
(21, 1, 14, 1, 2, 8, 'right', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(22, 1, 14, 1, 3, 8, 'right', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(23, 1, 14, 1, 4, 2, 'right', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(24, 1, 14, 1, 5, 2, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(25, 1, 14, 2, 3, 4, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(26, 1, 14, 2, 4, 8, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(27, 1, 14, 2, 5, 8, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(28, 1, 14, 3, 4, 6, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(29, 1, 14, 3, 5, 7, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(30, 1, 14, 4, 5, 3, 'left', '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(31, 1, 13, 1, 2, 4, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(32, 1, 13, 1, 3, 7, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(33, 1, 13, 1, 4, 7, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(34, 1, 13, 1, 5, 9, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(35, 1, 13, 2, 3, 3, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(36, 1, 13, 2, 4, 7, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(37, 1, 13, 2, 5, 6, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(38, 1, 13, 3, 4, 4, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(39, 1, 13, 3, 5, 6, 'right', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(40, 1, 13, 4, 5, 1, 'left', '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(41, 1, 10, 1, 2, 3, 'right', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(42, 1, 10, 1, 3, 3, 'right', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(43, 1, 10, 1, 4, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(44, 1, 10, 1, 5, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(45, 1, 10, 2, 3, 2, 'right', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(46, 1, 10, 2, 4, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(47, 1, 10, 2, 5, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(48, 1, 10, 3, 4, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(49, 1, 10, 3, 5, 3, 'left', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(50, 1, 10, 4, 5, 2, 'right', '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(51, 1, 7, 1, 2, 2, 'left', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(52, 1, 7, 1, 3, 2, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(53, 1, 7, 1, 4, 2, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(54, 1, 7, 1, 5, 4, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(55, 1, 7, 2, 3, 2, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(56, 1, 7, 2, 4, 2, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(57, 1, 7, 2, 5, 3, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(58, 1, 7, 3, 4, 2, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(59, 1, 7, 3, 5, 3, 'right', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(60, 1, 7, 4, 5, 2, 'left', '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(61, 1, 8, 1, 2, 1, 'left', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(62, 1, 8, 1, 3, 6, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(63, 1, 8, 1, 4, 1, 'left', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(64, 1, 8, 1, 5, 6, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(65, 1, 8, 2, 3, 6, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(66, 1, 8, 2, 4, 1, 'left', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(67, 1, 8, 2, 5, 7, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(68, 1, 8, 3, 4, 3, 'left', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(69, 1, 8, 3, 5, 6, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(70, 1, 8, 4, 5, 7, 'right', '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(71, 1, 4, 1, 2, 3, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(72, 1, 4, 1, 3, 3, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(73, 1, 4, 1, 4, 3, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(74, 1, 4, 1, 5, 6, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(75, 1, 4, 2, 3, 3, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(76, 1, 4, 2, 4, 3, 'left', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(77, 1, 4, 2, 5, 5, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(78, 1, 4, 3, 4, 3, 'left', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(79, 1, 4, 3, 5, 3, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(80, 1, 4, 4, 5, 4, 'right', '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(81, 1, 11, 1, 2, 3, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(82, 1, 11, 1, 3, 2, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(83, 1, 11, 1, 4, 5, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(84, 1, 11, 1, 5, 4, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(85, 1, 11, 2, 3, 2, 'right', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(86, 1, 11, 2, 4, 3, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(87, 1, 11, 2, 5, 2, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(88, 1, 11, 3, 4, 3, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(89, 1, 11, 3, 5, 2, 'left', '2026-04-14 14:47:11', '2026-04-14 14:47:11'),
(90, 1, 11, 4, 5, 2, 'right', '2026-04-14 14:47:11', '2026-04-14 14:47:11');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_scoring_parameters`
--

CREATE TABLE `criteria_scoring_parameters` (
  `id` bigint UNSIGNED NOT NULL,
  `scoring_rule_id` bigint UNSIGNED NOT NULL,
  `param_key` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `param_value` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_scoring_parameters`
--

INSERT INTO `criteria_scoring_parameters` (`id`, `scoring_rule_id`, `param_key`, `param_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:04:02', '2026-04-14 14:04:02'),
(2, 1, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-04-14 14:04:02', '2026-04-14 14:04:02'),
(3, 1, 'scale_utilities', '{\"1\": \"0\", \"2\": \"50\", \"3\": \"71\", \"4\": \"87\", \"5\": \"100\"}', '2026-04-14 14:04:02', '2026-04-14 14:04:02'),
(4, 2, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:04:25', '2026-04-14 14:04:25'),
(5, 2, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-04-14 14:04:25', '2026-04-14 14:04:25'),
(6, 2, 'scale_utilities', '{\"1\": \"0\", \"2\": \"25\", \"3\": \"50\", \"4\": \"75\", \"5\": \"100\"}', '2026-04-14 14:04:25', '2026-04-14 14:04:25'),
(7, 3, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:04:42', '2026-04-14 14:04:42'),
(8, 3, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-04-14 14:04:42', '2026-04-14 14:04:42'),
(9, 3, 'scale_utilities', '{\"1\": \"0\", \"2\": \"6\", \"3\": \"25\", \"4\": \"56\", \"5\": \"100\"}', '2026-04-14 14:04:42', '2026-04-14 14:04:42'),
(10, 4, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:05:04', '2026-04-14 14:05:04'),
(11, 4, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-04-14 14:05:04', '2026-04-14 14:05:04'),
(12, 4, 'scale_utilities', '{\"1\": \"0\", \"2\": \"6\", \"3\": \"25\", \"4\": \"56\", \"5\": \"100\"}', '2026-04-14 14:05:04', '2026-04-14 14:05:04'),
(13, 5, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:05:25', '2026-04-14 14:05:25'),
(14, 5, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-04-14 14:05:25', '2026-04-14 14:05:25'),
(15, 5, 'scale_utilities', '{\"1\": \"0\", \"2\": \"50\", \"3\": \"71\", \"4\": \"87\", \"5\": \"100\"}', '2026-04-14 14:05:25', '2026-04-14 14:05:25'),
(16, 8, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:06:38', '2026-04-14 14:06:38'),
(17, 8, 'scale_semantics', '{\"1\": \"RT\", \"2\": \"Antar RT\", \"3\": \"Padukuhan\", \"4\": \"Antar Padukuhan\", \"5\": \"Kalurahan dan Atasnya\"}', '2026-04-14 14:06:38', '2026-04-14 14:06:38'),
(18, 8, 'scale_utilities', '{\"1\": \"0\", \"2\": \"6\", \"3\": \"25\", \"4\": \"56\", \"5\": \"100\"}', '2026-04-14 14:06:38', '2026-04-14 14:06:38'),
(19, 9, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:07:33', '2026-04-14 14:07:33'),
(20, 9, 'scale_semantics', '{\"1\": \"Tidak Selaras\", \"2\": \"Kurang Selaras\", \"3\": \"Cukup Selaras\", \"4\": \"Selaras\", \"5\": \"Sangat Selaras/ Prioritas\"}', '2026-04-14 14:07:33', '2026-04-14 14:07:33'),
(21, 9, 'scale_utilities', '{\"1\": \"0\", \"2\": \"25\", \"3\": \"50\", \"4\": \"75\", \"5\": \"100\"}', '2026-04-14 14:07:33', '2026-04-14 14:07:33'),
(22, 10, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:08:21', '2026-04-14 14:08:21'),
(23, 10, 'scale_semantics', '{\"1\": \"Tidak Mendesak\", \"2\": \"Kurang Mendesak\", \"3\": \"Cukup Mendesak\", \"4\": \"Mendesak\", \"5\": \"Sangat Mendesak\"}', '2026-04-14 14:08:21', '2026-04-14 14:08:21'),
(24, 10, 'scale_utilities', '{\"1\": \"0\", \"2\": \"50\", \"3\": \"71\", \"4\": \"87\", \"5\": \"100\"}', '2026-04-14 14:08:21', '2026-04-14 14:08:21'),
(25, 11, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-04-14 14:09:07', '2026-04-14 14:09:07'),
(26, 11, 'scale_semantics', '{\"1\": \"TIdak Berdampak\", \"2\": \"Kurang Berdampak\", \"3\": \"Cukup Berdampak\", \"4\": \"Berdampak\", \"5\": \"Sangat Berdampak\"}', '2026-04-14 14:09:07', '2026-04-14 14:09:07'),
(27, 11, 'scale_utilities', '{\"1\": \"0\", \"2\": \"6\", \"3\": \"25\", \"4\": \"56\", \"5\": \"100\"}', '2026-04-14 14:09:07', '2026-04-14 14:09:07');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_scoring_rules`
--

CREATE TABLE `criteria_scoring_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED DEFAULT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `input_type` enum('scale','numeric') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `utility_function` enum('linear','concave','convex') COLLATE utf8mb4_unicode_ci NOT NULL,
  `scale_min` double DEFAULT NULL,
  `scale_max` double DEFAULT NULL,
  `curve_degree` double DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ;

--
-- Dumping data for table `criteria_scoring_rules`
--

INSERT INTO `criteria_scoring_rules` (`id`, `decision_session_id`, `criteria_id`, `input_type`, `utility_function`, `scale_min`, `scale_max`, `curve_degree`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'scale', 'concave', 1, 5, 0.2, '2026-04-14 14:04:02', '2026-04-14 14:04:02'),
(2, 1, 2, 'scale', 'linear', 1, 5, NULL, '2026-04-14 14:04:25', '2026-04-14 14:04:25'),
(3, 1, 3, 'scale', 'convex', 1, 5, 4, '2026-04-14 14:04:42', '2026-04-14 14:04:42'),
(4, 1, 4, 'scale', 'convex', 1, 5, 4, '2026-04-14 14:05:04', '2026-04-14 14:05:04'),
(5, 1, 5, 'scale', 'concave', 1, 5, 0.2, '2026-04-14 14:05:25', '2026-04-14 14:05:25'),
(6, 1, 6, 'numeric', 'linear', NULL, NULL, NULL, '2026-04-14 14:05:42', '2026-04-14 14:05:42'),
(7, 1, 7, 'numeric', 'concave', NULL, NULL, 0.2, '2026-04-14 14:06:01', '2026-04-14 14:06:01'),
(8, 1, 8, 'scale', 'convex', 1, 5, 4, '2026-04-14 14:06:38', '2026-04-14 14:06:38'),
(9, 1, 9, 'scale', 'linear', 1, 5, NULL, '2026-04-14 14:07:33', '2026-04-14 14:07:33'),
(10, 1, 10, 'scale', 'concave', 1, 5, 0.2, '2026-04-14 14:08:21', '2026-04-14 14:08:21'),
(11, 1, 11, 'scale', 'convex', 1, 5, 4, '2026-04-14 14:09:07', '2026-04-14 14:09:07');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_weights`
--

CREATE TABLE `criteria_weights` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `dm_id` bigint UNSIGNED DEFAULT NULL,
  `weights` json NOT NULL,
  `cr` decimal(6,4) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_weights`
--

INSERT INTO `criteria_weights` (`id`, `decision_session_id`, `dm_id`, `weights`, `cr`, `created_at`, `updated_at`) VALUES
(1, 1, 3, '{\"1\": 0.037742126024538886, \"2\": 0.4770330428409358, \"3\": 0.07622443535220538, \"4\": 0.2702289173842191, \"5\": 0.1387714783981008}', 0.0536, '2026-04-14 14:33:33', '2026-04-14 14:33:33'),
(2, 1, 14, '{\"1\": 0.05164606991184564, \"2\": 0.5451791824720822, \"3\": 0.28782540922518896, \"4\": 0.07828080376053864, \"5\": 0.037068534630344485}', 0.0797, '2026-04-14 14:36:17', '2026-04-14 14:36:17'),
(3, 1, 13, '{\"1\": 0.02893000287430926, \"2\": 0.06471218771511725, \"3\": 0.125617400962174, \"4\": 0.37076557793847614, \"5\": 0.4099748305099232}', 0.0843, '2026-04-14 14:37:56', '2026-04-14 14:37:56'),
(4, 1, 10, '{\"1\": 0.1681210090769274, \"2\": 0.28293634678719887, \"3\": 0.3733367478308918, \"4\": 0.07570825496623007, \"5\": 0.09989764133875184}', 0.0702, '2026-04-14 14:39:23', '2026-04-14 14:39:23'),
(5, 1, 7, '{\"1\": 0.11707776203644996, \"2\": 0.0939831855445325, \"3\": 0.16363423023231383, \"4\": 0.30897006636536434, \"5\": 0.31633475582133935}', 0.0719, '2026-04-14 14:42:00', '2026-04-14 14:42:00'),
(6, 1, 8, '{\"1\": 0.06302972177200203, \"2\": 0.06111615619093437, \"3\": 0.23007021123321528, \"4\": 0.07020402808026817, \"5\": 0.5755798827235802}', 0.0724, '2026-04-14 14:44:31', '2026-04-14 14:44:31'),
(7, 1, 4, '{\"1\": 0.05419711450903466, \"2\": 0.13536594454927023, \"3\": 0.23266318544264897, \"4\": 0.09121009723762248, \"5\": 0.4865636582614237}', 0.0829, '2026-04-14 14:46:05', '2026-04-14 14:46:05'),
(8, 1, 11, '{\"1\": 0.42690371809450584, \"2\": 0.16386782449802145, \"3\": 0.23448979024465105, \"4\": 0.06662622178967997, \"5\": 0.10811244537314178}', 0.0177, '2026-04-14 14:47:11', '2026-04-14 14:47:11');

-- --------------------------------------------------------

--
-- Table structure for table `decision_sessions`
--

CREATE TABLE `decision_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `year` year NOT NULL,
  `status` enum('draft','configured','scoring','agregated','closed') CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'draft',
  `created_by` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `decision_sessions`
--

INSERT INTO `decision_sessions` (`id`, `name`, `year`, `status`, `created_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Musrenbang Kalurahan Wonosari Tahun 2025', '2025', 'closed', 2, '2026-04-14 13:39:51', '2026-04-15 16:35:03', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `decision_session_assignments`
--

CREATE TABLE `decision_session_assignments` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `can_pairwise` tinyint(1) NOT NULL DEFAULT '0',
  `can_evaluate` tinyint(1) NOT NULL DEFAULT '0',
  `criteria_id` bigint UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `decision_session_assignments`
--

INSERT INTO `decision_session_assignments` (`id`, `decision_session_id`, `user_id`, `can_pairwise`, `can_evaluate`, `criteria_id`, `created_at`, `updated_at`) VALUES
(1, 1, 3, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(2, 1, 4, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(3, 1, 7, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(4, 1, 8, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(5, 1, 10, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(6, 1, 11, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(7, 1, 13, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(8, 1, 14, 1, 0, NULL, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(9, 1, 3, 0, 1, 9, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(10, 1, 4, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(11, 1, 7, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(12, 1, 8, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(13, 1, 10, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(14, 1, 11, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(15, 1, 13, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(16, 1, 14, 0, 1, 10, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(17, 1, 4, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(18, 1, 7, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(19, 1, 8, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(20, 1, 10, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(21, 1, 11, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(22, 1, 13, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31'),
(23, 1, 14, 0, 1, 11, '2026-04-14 14:26:31', '2026-04-14 14:26:31');

-- --------------------------------------------------------

--
-- Table structure for table `decision_session_criteria`
--

CREATE TABLE `decision_session_criteria` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `decision_session_dm`
--

CREATE TABLE `decision_session_dm` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_aggregations`
--

CREATE TABLE `evaluation_aggregations` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `method` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `score` double NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_aggregations`
--

INSERT INTO `evaluation_aggregations` (`id`, `decision_session_id`, `user_id`, `alternative_id`, `method`, `score`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 'saw', 0.86352317598013, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(2, 1, NULL, 2, 'saw', 0.28676869287154, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(3, 1, NULL, 3, 'saw', 0.78020334686237, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(4, 1, NULL, 4, 'saw', 0.17093072072939, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(5, 1, NULL, 5, 'saw', 0.3107663541978, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(6, 1, NULL, 6, 'saw', 0.29976510653862, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(7, 1, NULL, 7, 'saw', 0.44784854651783, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(8, 1, NULL, 8, 'saw', 0.20852032567371, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(9, 1, NULL, 9, 'saw', 0.86421014748051, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(10, 1, NULL, 10, 'saw', 0.24580173674703, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(11, 1, NULL, 11, 'saw', 0.86558409048127, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(12, 1, NULL, 12, 'saw', 0.22395269348063, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(13, 1, NULL, 13, 'saw', 0.14428023201351, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(14, 1, NULL, 14, 'saw', 0.35173468536698, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(15, 1, NULL, 15, 'saw', 0.31040709743993, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(16, 1, NULL, 16, 'saw', 0.24480634182194, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(17, 1, NULL, 17, 'saw', 0.43792989791898, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(18, 1, NULL, 18, 'saw', 0.33150272542128, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(19, 1, NULL, 19, 'saw', 0.84144194918214, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(20, 1, NULL, 20, 'saw', 0.43609710415614, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(21, 1, NULL, 21, 'saw', 0.20210365021423, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(22, 1, NULL, 22, 'saw', 0.14738087380752, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(23, 1, NULL, 23, 'saw', 0.18025460694782, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(24, 1, NULL, 24, 'saw', 0.18087087843748, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(25, 1, NULL, 1, 'smart', 1.0707019243321, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(26, 1, NULL, 2, 'smart', 0.2157166283431, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(27, 1, NULL, 3, 'smart', 0.78020334686237, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(28, 1, NULL, 4, 'smart', 0.19111050086328, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(29, 1, NULL, 5, 'smart', 0.32897310115858, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(30, 1, NULL, 6, 'smart', 0.30907061976523, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(31, 1, NULL, 7, 'smart', 0.54360275087194, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(32, 1, NULL, 8, 'smart', 0.26421381323191, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(33, 1, NULL, 9, 'smart', 1.0900326053325, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(34, 1, NULL, 10, 'smart', 0.17410956352915, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(35, 1, NULL, 11, 'smart', 1.0896144800857, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(36, 1, NULL, 12, 'smart', 0.16948611625338, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(37, 1, NULL, 13, 'smart', 0.17924431827642, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(38, 1, NULL, 14, 'smart', 0.24914540213494, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(39, 1, NULL, 15, 'smart', 0.39482263917862, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(40, 1, NULL, 16, 'smart', 0.29555521327545, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(41, 1, NULL, 17, 'smart', 0.55172909709447, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(42, 1, NULL, 18, 'smart', 0.40842363061507, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(43, 1, NULL, 19, 'smart', 1.0486206975341, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(44, 1, NULL, 20, 'smart', 0.55689313434896, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(45, 1, NULL, 21, 'smart', 0.16425353074144, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(46, 1, NULL, 22, 'smart', 0.18975598320177, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(47, 1, NULL, 23, 'smart', 0.15819479439161, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(48, 1, NULL, 24, 'smart', 0.17659251015066, '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(49, 1, 3, 1, 'smart', 0.106726, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(50, 1, 3, 2, 'smart', 0.081934, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(51, 1, 3, 3, 'smart', 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(52, 1, 3, 4, 'smart', 0.016657, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(53, 1, 3, 5, 'smart', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(54, 1, 3, 6, 'smart', 0.040967, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(55, 1, 3, 7, 'smart', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(56, 1, 3, 8, 'smart', 0.054056, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(57, 1, 3, 9, 'smart', 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(58, 1, 3, 10, 'smart', 0.081934, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(59, 1, 3, 11, 'smart', 0.106726, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(60, 1, 3, 12, 'smart', 0.163868, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(61, 1, 3, 13, 'smart', 0.016657, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(62, 1, 3, 14, 'smart', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(63, 1, 3, 15, 'smart', 0.122901, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(64, 1, 3, 16, 'smart', 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(65, 1, 3, 17, 'smart', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(66, 1, 3, 18, 'smart', 0.122901, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(67, 1, 3, 19, 'smart', 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(68, 1, 3, 20, 'smart', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(69, 1, 3, 21, 'smart', 0.040967, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(70, 1, 3, 22, 'smart', 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(71, 1, 3, 23, 'smart', 0.163868, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(72, 1, 3, 24, 'smart', 0.054056, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(73, 1, 3, 1, 'saw', 0.170761, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(74, 1, 3, 2, 'saw', 0.098321, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(75, 1, 3, 3, 'saw', 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(76, 1, 3, 4, 'saw', 0.02665, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(77, 1, 3, 5, 'saw', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(78, 1, 3, 6, 'saw', 0.065547, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(79, 1, 3, 7, 'saw', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(80, 1, 3, 8, 'saw', 0.064867, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(81, 1, 3, 9, 'saw', 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(82, 1, 3, 10, 'saw', 0.098321, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(83, 1, 3, 11, 'saw', 0.170761, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(84, 1, 3, 12, 'saw', 0.163868, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(85, 1, 3, 13, 'saw', 0.02665, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(86, 1, 3, 14, 'saw', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(87, 1, 3, 15, 'saw', 0.131094, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(88, 1, 3, 16, 'saw', 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(89, 1, 3, 17, 'saw', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(90, 1, 3, 18, 'saw', 0.131094, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(91, 1, 3, 19, 'saw', 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(92, 1, 3, 20, 'saw', 0.23449, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(93, 1, 3, 21, 'saw', 0.065547, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(94, 1, 3, 22, 'saw', 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(95, 1, 3, 23, 'saw', 0.163868, '2026-04-15 03:08:05', '2026-04-15 03:08:05'),
(96, 1, 3, 24, 'saw', 0.064867, '2026-04-15 03:08:05', '2026-04-15 14:14:48'),
(97, 1, 14, 1, 'smart', 0.175107, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(98, 1, 14, 2, 'smart', 0.097252, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(99, 1, 14, 3, 'smart', 0.199161, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(100, 1, 14, 4, 'smart', 0.0623135, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(101, 1, 14, 5, 'smart', 0.125952, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(102, 1, 14, 6, 'smart', 0.097252, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(103, 1, 14, 7, 'smart', 0.2061, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(104, 1, 14, 8, 'smart', 0.064162, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(105, 1, 14, 9, 'smart', 0.199161, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(106, 1, 14, 10, 'smart', 0.097252, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(107, 1, 14, 11, 'smart', 0.175107, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(108, 1, 14, 12, 'smart', 0.1032775, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(109, 1, 14, 13, 'smart', 0.00013, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(110, 1, 14, 14, 'smart', 0.125952, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(111, 1, 14, 15, 'smart', 0.159287, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(112, 1, 14, 16, 'smart', 0.064162, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(113, 1, 14, 17, 'smart', 0.2061, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(114, 1, 14, 18, 'smart', 0.0672155, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(115, 1, 14, 19, 'smart', 0.2690545, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(116, 1, 14, 20, 'smart', 0.2061, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(117, 1, 14, 21, 'smart', 0.1032775, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(118, 1, 14, 22, 'smart', 0.050437, '2026-04-15 03:09:02', '2026-04-15 16:34:28'),
(119, 1, 14, 23, 'smart', 0.0764485, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(120, 1, 14, 24, 'smart', 0.0681375, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(121, 1, 14, 1, 'saw', 0.469594, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(122, 1, 14, 2, 'saw', 0.253995, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(123, 1, 14, 3, 'saw', 0.57632, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(124, 1, 14, 4, 'saw', 0.116596, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(125, 1, 14, 5, 'saw', 0.304837, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(126, 1, 14, 6, 'saw', 0.253995, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(127, 1, 14, 7, 'saw', 0.351735, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(128, 1, 14, 8, 'saw', 0.167574, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(129, 1, 14, 9, 'saw', 0.57632, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(130, 1, 14, 10, 'saw', 0.253995, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(131, 1, 14, 11, 'saw', 0.469594, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(132, 1, 14, 12, 'saw', 0.294962, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(133, 1, 14, 13, 'saw', 0.043307, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(134, 1, 14, 14, 'saw', 0.304837, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(135, 1, 14, 15, 'saw', 0.327736, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(136, 1, 14, 16, 'saw', 0.167574, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(137, 1, 14, 17, 'saw', 0.351735, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(138, 1, 14, 18, 'saw', 0.180255, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(139, 1, 14, 19, 'saw', 0.768427, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(140, 1, 14, 20, 'saw', 0.351735, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(141, 1, 14, 21, 'saw', 0.294962, '2026-04-15 03:09:02', '2026-04-15 16:34:28'),
(142, 1, 14, 22, 'saw', 0.145951, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(143, 1, 14, 23, 'saw', 0.221222, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(144, 1, 14, 24, 'saw', 0.194602, '2026-04-15 03:09:02', '2026-04-15 16:34:28');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_results`
--

CREATE TABLE `evaluation_results` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `method` enum('smart','saw','weighted_product','topsis') COLLATE utf8mb4_unicode_ci NOT NULL,
  `evaluation_score` decimal(12,6) NOT NULL,
  `weighted_score` decimal(12,6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_results`
--

INSERT INTO `evaluation_results` (`id`, `decision_session_id`, `user_id`, `alternative_id`, `criteria_id`, `method`, `evaluation_score`, `weighted_score`, `created_at`, `updated_at`) VALUES
(49, 1, 3, 1, 9, 'smart', 0.250000, 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(50, 1, 3, 2, 9, 'smart', 0.500000, 0.081934, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(51, 1, 3, 3, 9, 'smart', 0.250000, 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(52, 1, 3, 4, 9, 'smart', 0.250000, 0.016657, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(53, 1, 3, 5, 9, 'smart', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(54, 1, 3, 6, 9, 'smart', 0.250000, 0.040967, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(55, 1, 3, 7, 9, 'smart', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(56, 1, 3, 8, 9, 'smart', 0.500000, 0.054056, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(57, 1, 3, 9, 9, 'smart', 0.250000, 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(58, 1, 3, 10, 9, 'smart', 0.500000, 0.081934, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(59, 1, 3, 11, 9, 'smart', 0.250000, 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(60, 1, 3, 12, 9, 'smart', 1.000000, 0.163868, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(61, 1, 3, 13, 9, 'smart', 0.250000, 0.016657, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(62, 1, 3, 14, 9, 'smart', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(63, 1, 3, 15, 9, 'smart', 0.750000, 0.122901, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(64, 1, 3, 16, 9, 'smart', 1.000000, 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(65, 1, 3, 17, 9, 'smart', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(66, 1, 3, 18, 9, 'smart', 0.750000, 0.122901, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(67, 1, 3, 19, 9, 'smart', 0.250000, 0.106726, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(68, 1, 3, 20, 9, 'smart', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(69, 1, 3, 21, 9, 'smart', 0.250000, 0.040967, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(70, 1, 3, 22, 9, 'smart', 1.000000, 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(71, 1, 3, 23, 9, 'smart', 1.000000, 0.163868, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(72, 1, 3, 24, 9, 'smart', 0.500000, 0.054056, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(73, 1, 3, 1, 9, 'saw', 0.400000, 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(74, 1, 3, 2, 9, 'saw', 0.600000, 0.098321, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(75, 1, 3, 3, 9, 'saw', 0.400000, 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(76, 1, 3, 4, 9, 'saw', 0.400000, 0.026650, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(77, 1, 3, 5, 9, 'saw', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(78, 1, 3, 6, 9, 'saw', 0.400000, 0.065547, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(79, 1, 3, 7, 9, 'saw', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(80, 1, 3, 8, 9, 'saw', 0.600000, 0.064867, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(81, 1, 3, 9, 9, 'saw', 0.400000, 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(82, 1, 3, 10, 9, 'saw', 0.600000, 0.098321, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(83, 1, 3, 11, 9, 'saw', 0.400000, 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(84, 1, 3, 12, 9, 'saw', 1.000000, 0.163868, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(85, 1, 3, 13, 9, 'saw', 0.400000, 0.026650, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(86, 1, 3, 14, 9, 'saw', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(87, 1, 3, 15, 9, 'saw', 0.800000, 0.131094, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(88, 1, 3, 16, 9, 'saw', 1.000000, 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(89, 1, 3, 17, 9, 'saw', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(90, 1, 3, 18, 9, 'saw', 0.800000, 0.131094, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(91, 1, 3, 19, 9, 'saw', 0.400000, 0.170761, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(92, 1, 3, 20, 9, 'saw', 1.000000, 0.234490, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(93, 1, 3, 21, 9, 'saw', 0.400000, 0.065547, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(94, 1, 3, 22, 9, 'saw', 1.000000, 0.108112, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(95, 1, 3, 23, 9, 'saw', 1.000000, 0.163868, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(96, 1, 3, 24, 9, 'saw', 0.600000, 0.064867, '2026-04-15 03:08:05', '2026-04-15 14:33:16'),
(97, 1, 14, 1, 10, 'smart', 0.757858, 0.323533, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(98, 1, 14, 1, 11, 'smart', 0.062500, 0.026681, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(99, 1, 14, 2, 10, 'smart', 0.870551, 0.142655, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(100, 1, 14, 2, 11, 'smart', 0.316406, 0.051849, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(101, 1, 14, 3, 10, 'smart', 0.870551, 0.371641, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(102, 1, 14, 3, 11, 'smart', 0.062500, 0.026681, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(103, 1, 14, 4, 10, 'smart', 0.870551, 0.058001, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(104, 1, 14, 4, 11, 'smart', 1.000000, 0.066626, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(105, 1, 14, 5, 10, 'smart', 0.757858, 0.177710, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(106, 1, 14, 5, 11, 'smart', 0.316406, 0.074194, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(107, 1, 14, 6, 10, 'smart', 0.870551, 0.142655, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(108, 1, 14, 6, 11, 'smart', 0.316406, 0.051849, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(109, 1, 14, 7, 10, 'smart', 0.757858, 0.177710, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(110, 1, 14, 7, 11, 'smart', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(111, 1, 14, 8, 10, 'smart', 0.870551, 0.094117, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(112, 1, 14, 8, 11, 'smart', 0.316406, 0.034207, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(113, 1, 14, 9, 10, 'smart', 0.870551, 0.371641, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(114, 1, 14, 9, 11, 'smart', 0.062500, 0.026681, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(115, 1, 14, 10, 10, 'smart', 0.870551, 0.142655, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(116, 1, 14, 10, 11, 'smart', 0.316406, 0.051849, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(117, 1, 14, 11, 10, 'smart', 0.757858, 0.323533, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(118, 1, 14, 11, 11, 'smart', 0.062500, 0.026681, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(119, 1, 14, 12, 10, 'smart', 0.944088, 0.154706, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(120, 1, 14, 12, 11, 'smart', 0.316406, 0.051849, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(121, 1, 14, 13, 10, 'smart', 0.000000, 0.000000, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(122, 1, 14, 13, 11, 'smart', 0.003906, 0.000260, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(123, 1, 14, 14, 10, 'smart', 0.757858, 0.177710, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(124, 1, 14, 14, 11, 'smart', 0.316406, 0.074194, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(125, 1, 14, 15, 10, 'smart', 0.944088, 0.154706, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(126, 1, 14, 15, 11, 'smart', 1.000000, 0.163868, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(127, 1, 14, 16, 10, 'smart', 0.870551, 0.094117, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(128, 1, 14, 16, 11, 'smart', 0.316406, 0.034207, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(129, 1, 14, 17, 10, 'smart', 0.757858, 0.177710, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(130, 1, 14, 17, 11, 'smart', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(131, 1, 14, 18, 10, 'smart', 0.757858, 0.124189, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(132, 1, 14, 18, 11, 'smart', 0.062500, 0.010242, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(133, 1, 14, 19, 10, 'smart', 0.944088, 0.403034, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(134, 1, 14, 19, 11, 'smart', 0.316406, 0.135075, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(135, 1, 14, 20, 10, 'smart', 0.757858, 0.177710, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(136, 1, 14, 20, 11, 'smart', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(137, 1, 14, 21, 10, 'smart', 0.944088, 0.154706, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(138, 1, 14, 21, 11, 'smart', 0.316406, 0.051849, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(139, 1, 14, 22, 10, 'smart', 0.870551, 0.094117, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(140, 1, 14, 22, 11, 'smart', 0.062500, 0.006757, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(141, 1, 14, 23, 10, 'smart', 0.870551, 0.142655, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(142, 1, 14, 23, 11, 'smart', 0.062500, 0.010242, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(143, 1, 14, 24, 10, 'smart', 0.944088, 0.102068, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(144, 1, 14, 24, 11, 'smart', 0.316406, 0.034207, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(145, 1, 14, 1, 10, 'saw', 0.500000, 0.213452, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(146, 1, 14, 1, 11, 'saw', 0.600000, 0.256142, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(147, 1, 14, 2, 10, 'saw', 0.750000, 0.122901, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(148, 1, 14, 2, 11, 'saw', 0.800000, 0.131094, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(149, 1, 14, 3, 10, 'saw', 0.750000, 0.320178, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(150, 1, 14, 3, 11, 'saw', 0.600000, 0.256142, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(151, 1, 14, 4, 10, 'saw', 0.750000, 0.049970, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(152, 1, 14, 4, 11, 'saw', 1.000000, 0.066626, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(153, 1, 14, 5, 10, 'saw', 0.500000, 0.117245, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(154, 1, 14, 5, 11, 'saw', 0.800000, 0.187592, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(155, 1, 14, 6, 10, 'saw', 0.750000, 0.122901, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(156, 1, 14, 6, 11, 'saw', 0.800000, 0.131094, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(157, 1, 14, 7, 10, 'saw', 0.500000, 0.117245, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(158, 1, 14, 7, 11, 'saw', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(159, 1, 14, 8, 10, 'saw', 0.750000, 0.081084, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(160, 1, 14, 8, 11, 'saw', 0.800000, 0.086490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(161, 1, 14, 9, 10, 'saw', 0.750000, 0.320178, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(162, 1, 14, 9, 11, 'saw', 0.600000, 0.256142, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(163, 1, 14, 10, 10, 'saw', 0.750000, 0.122901, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(164, 1, 14, 10, 11, 'saw', 0.800000, 0.131094, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(165, 1, 14, 11, 10, 'saw', 0.500000, 0.213452, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(166, 1, 14, 11, 11, 'saw', 0.600000, 0.256142, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(167, 1, 14, 12, 10, 'saw', 1.000000, 0.163868, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(168, 1, 14, 12, 11, 'saw', 0.800000, 0.131094, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(169, 1, 14, 13, 10, 'saw', 0.250000, 0.016657, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(170, 1, 14, 13, 11, 'saw', 0.400000, 0.026650, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(171, 1, 14, 14, 10, 'saw', 0.500000, 0.117245, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(172, 1, 14, 14, 11, 'saw', 0.800000, 0.187592, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(173, 1, 14, 15, 10, 'saw', 1.000000, 0.163868, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(174, 1, 14, 15, 11, 'saw', 1.000000, 0.163868, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(175, 1, 14, 16, 10, 'saw', 0.750000, 0.081084, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(176, 1, 14, 16, 11, 'saw', 0.800000, 0.086490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(177, 1, 14, 17, 10, 'saw', 0.500000, 0.117245, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(178, 1, 14, 17, 11, 'saw', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(179, 1, 14, 18, 10, 'saw', 0.500000, 0.081934, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(180, 1, 14, 18, 11, 'saw', 0.600000, 0.098321, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(181, 1, 14, 19, 10, 'saw', 1.000000, 0.426904, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(182, 1, 14, 19, 11, 'saw', 0.800000, 0.341523, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(183, 1, 14, 20, 10, 'saw', 0.500000, 0.117245, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(184, 1, 14, 20, 11, 'saw', 1.000000, 0.234490, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(185, 1, 14, 21, 10, 'saw', 1.000000, 0.163868, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(186, 1, 14, 21, 11, 'saw', 0.800000, 0.131094, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(187, 1, 14, 22, 10, 'saw', 0.750000, 0.081084, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(188, 1, 14, 22, 11, 'saw', 0.600000, 0.064867, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(189, 1, 14, 23, 10, 'saw', 0.750000, 0.122901, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(190, 1, 14, 23, 11, 'saw', 0.600000, 0.098321, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(191, 1, 14, 24, 10, 'saw', 1.000000, 0.108112, '2026-04-15 03:09:02', '2026-04-15 16:34:49'),
(192, 1, 14, 24, 11, 'saw', 0.800000, 0.086490, '2026-04-15 03:09:02', '2026-04-15 16:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `evaluation_scores`
--

CREATE TABLE `evaluation_scores` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `value` decimal(18,4) NOT NULL,
  `source` enum('human','system') COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `evaluation_scores`
--

INSERT INTO `evaluation_scores` (`id`, `decision_session_id`, `user_id`, `criteria_id`, `alternative_id`, `value`, `source`, `created_at`, `updated_at`) VALUES
(485, 1, NULL, 6, 1, 7500000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(486, 1, NULL, 7, 1, 80.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(487, 1, NULL, 8, 1, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(488, 1, NULL, 6, 2, 150000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(489, 1, NULL, 7, 2, 800.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(490, 1, NULL, 8, 2, 75.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(491, 1, NULL, 6, 3, 30000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(492, 1, NULL, 7, 3, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(493, 1, NULL, 8, 3, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(494, 1, NULL, 6, 4, 10000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(495, 1, NULL, 7, 4, 500.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(496, 1, NULL, 8, 4, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(497, 1, NULL, 6, 5, 40000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(498, 1, NULL, 7, 5, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(499, 1, NULL, 8, 5, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(500, 1, NULL, 6, 6, 8000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(501, 1, NULL, 7, 6, 500.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(502, 1, NULL, 8, 6, 25.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(503, 1, NULL, 6, 7, 20000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(504, 1, NULL, 7, 7, 60.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(505, 1, NULL, 8, 7, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(506, 1, NULL, 6, 8, 25000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(507, 1, NULL, 7, 8, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(508, 1, NULL, 8, 8, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(509, 1, NULL, 6, 9, 15000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(510, 1, NULL, 7, 9, 120.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(511, 1, NULL, 8, 9, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(512, 1, NULL, 6, 10, 150000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(513, 1, NULL, 7, 10, 800.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(514, 1, NULL, 8, 10, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(515, 1, NULL, 6, 11, 30000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(516, 1, NULL, 7, 11, 200.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(517, 1, NULL, 8, 11, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(518, 1, NULL, 6, 12, 150000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(519, 1, NULL, 7, 12, 700.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(520, 1, NULL, 8, 12, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(521, 1, NULL, 6, 13, 10000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(522, 1, NULL, 7, 13, 200.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(523, 1, NULL, 8, 13, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(524, 1, NULL, 6, 14, 5000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(525, 1, NULL, 7, 14, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(526, 1, NULL, 8, 14, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(527, 1, NULL, 6, 15, 30000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(528, 1, NULL, 7, 15, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(529, 1, NULL, 8, 15, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(530, 1, NULL, 6, 16, 15000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(531, 1, NULL, 7, 16, 300.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(532, 1, NULL, 8, 16, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(533, 1, NULL, 6, 17, 30000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(534, 1, NULL, 7, 17, 80.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(535, 1, NULL, 8, 17, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(536, 1, NULL, 6, 18, 50000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(537, 1, NULL, 7, 18, 300.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(538, 1, NULL, 8, 18, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(539, 1, NULL, 6, 19, 15000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(540, 1, NULL, 7, 19, 80.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(541, 1, NULL, 8, 19, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(542, 1, NULL, 6, 20, 35000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(543, 1, NULL, 7, 20, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(544, 1, NULL, 8, 20, 100.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(545, 1, NULL, 6, 21, 150000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(546, 1, NULL, 7, 21, 600.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(547, 1, NULL, 8, 21, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(548, 1, NULL, 6, 22, 20000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(549, 1, NULL, 7, 22, 400.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(550, 1, NULL, 8, 22, 0.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(551, 1, NULL, 6, 23, 150000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(552, 1, NULL, 7, 23, 500.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(553, 1, NULL, 8, 23, 50.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(554, 1, NULL, 6, 24, 50000000.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(555, 1, NULL, 7, 24, 600.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(556, 1, NULL, 8, 24, 25.0000, 'system', '2026-04-15 16:33:20', '2026-04-15 16:33:20'),
(1373, 1, 11, 10, 1, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1374, 1, 11, 10, 2, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1375, 1, 11, 10, 3, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1376, 1, 11, 10, 4, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1377, 1, 11, 10, 5, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1378, 1, 11, 10, 6, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1379, 1, 11, 10, 7, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1380, 1, 11, 10, 8, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1381, 1, 11, 10, 9, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1382, 1, 11, 10, 10, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1383, 1, 11, 10, 11, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1384, 1, 11, 10, 12, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1385, 1, 11, 10, 13, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1386, 1, 11, 10, 14, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1387, 1, 11, 10, 15, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1388, 1, 11, 10, 16, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1389, 1, 11, 10, 17, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1390, 1, 11, 10, 18, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1391, 1, 11, 10, 19, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1392, 1, 11, 10, 20, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1393, 1, 11, 10, 21, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1394, 1, 11, 10, 22, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1395, 1, 11, 10, 23, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1396, 1, 11, 10, 24, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1397, 1, 11, 11, 1, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1398, 1, 11, 11, 2, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1399, 1, 11, 11, 3, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1400, 1, 11, 11, 4, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1401, 1, 11, 11, 5, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1402, 1, 11, 11, 6, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1403, 1, 11, 11, 7, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1404, 1, 11, 11, 8, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1405, 1, 11, 11, 9, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1406, 1, 11, 11, 10, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1407, 1, 11, 11, 11, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1408, 1, 11, 11, 12, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1409, 1, 11, 11, 13, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1410, 1, 11, 11, 14, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1411, 1, 11, 11, 15, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1412, 1, 11, 11, 16, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1413, 1, 11, 11, 17, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1414, 1, 11, 11, 18, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1415, 1, 11, 11, 19, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1416, 1, 11, 11, 20, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1417, 1, 11, 11, 21, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1418, 1, 11, 11, 22, 3.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1419, 1, 11, 11, 23, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1420, 1, 11, 11, 24, 4.0000, 'human', '2026-04-15 14:18:41', '2026-04-15 14:18:41'),
(1469, 1, 13, 10, 1, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1470, 1, 13, 10, 2, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1471, 1, 13, 10, 3, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1472, 1, 13, 10, 4, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1473, 1, 13, 10, 5, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1474, 1, 13, 10, 6, 2.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1475, 1, 13, 10, 7, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1476, 1, 13, 10, 8, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1477, 1, 13, 10, 9, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1478, 1, 13, 10, 10, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1479, 1, 13, 10, 11, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1480, 1, 13, 10, 12, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1481, 1, 13, 10, 13, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1482, 1, 13, 10, 14, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1483, 1, 13, 10, 15, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1484, 1, 13, 10, 16, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1485, 1, 13, 10, 17, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1486, 1, 13, 10, 18, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1487, 1, 13, 10, 19, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1488, 1, 13, 10, 20, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1489, 1, 13, 10, 21, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1490, 1, 13, 10, 22, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1491, 1, 13, 10, 23, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1492, 1, 13, 10, 24, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1493, 1, 13, 11, 1, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1494, 1, 13, 11, 2, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1495, 1, 13, 11, 3, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1496, 1, 13, 11, 4, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1497, 1, 13, 11, 5, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1498, 1, 13, 11, 6, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1499, 1, 13, 11, 7, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1500, 1, 13, 11, 8, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1501, 1, 13, 11, 9, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1502, 1, 13, 11, 10, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1503, 1, 13, 11, 11, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1504, 1, 13, 11, 12, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1505, 1, 13, 11, 13, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1506, 1, 13, 11, 14, 3.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1507, 1, 13, 11, 15, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1508, 1, 13, 11, 16, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1509, 1, 13, 11, 17, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1510, 1, 13, 11, 18, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1511, 1, 13, 11, 19, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1512, 1, 13, 11, 20, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1513, 1, 13, 11, 21, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1514, 1, 13, 11, 22, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1515, 1, 13, 11, 23, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1516, 1, 13, 11, 24, 4.0000, 'human', '2026-04-15 14:22:18', '2026-04-15 14:22:18'),
(1517, 1, 8, 10, 1, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1518, 1, 8, 10, 2, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1519, 1, 8, 10, 3, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1520, 1, 8, 10, 4, 4.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1521, 1, 8, 10, 5, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1522, 1, 8, 10, 6, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1523, 1, 8, 10, 7, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1524, 1, 8, 10, 8, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1525, 1, 8, 10, 9, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1526, 1, 8, 10, 10, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1527, 1, 8, 10, 11, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1528, 1, 8, 10, 12, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1529, 1, 8, 10, 13, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1530, 1, 8, 10, 14, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1531, 1, 8, 10, 15, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1532, 1, 8, 10, 16, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1533, 1, 8, 10, 17, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1534, 1, 8, 10, 18, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1535, 1, 8, 10, 19, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1536, 1, 8, 10, 20, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1537, 1, 8, 10, 21, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1538, 1, 8, 10, 22, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1539, 1, 8, 10, 23, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1540, 1, 8, 10, 24, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1541, 1, 8, 11, 1, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1542, 1, 8, 11, 2, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1543, 1, 8, 11, 3, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1544, 1, 8, 11, 4, 4.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1545, 1, 8, 11, 5, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1546, 1, 8, 11, 6, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1547, 1, 8, 11, 7, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1548, 1, 8, 11, 8, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1549, 1, 8, 11, 9, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1550, 1, 8, 11, 10, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1551, 1, 8, 11, 11, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1552, 1, 8, 11, 12, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1553, 1, 8, 11, 13, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1554, 1, 8, 11, 14, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1555, 1, 8, 11, 15, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1556, 1, 8, 11, 16, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1557, 1, 8, 11, 17, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1558, 1, 8, 11, 18, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1559, 1, 8, 11, 19, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1560, 1, 8, 11, 20, 5.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1561, 1, 8, 11, 21, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1562, 1, 8, 11, 22, 2.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1563, 1, 8, 11, 23, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1564, 1, 8, 11, 24, 3.0000, 'human', '2026-04-15 14:24:18', '2026-04-15 14:24:18'),
(1565, 1, 4, 10, 1, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1566, 1, 4, 10, 2, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1567, 1, 4, 10, 3, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1568, 1, 4, 10, 4, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1569, 1, 4, 10, 5, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1570, 1, 4, 10, 6, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1571, 1, 4, 10, 7, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1572, 1, 4, 10, 8, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1573, 1, 4, 10, 9, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1574, 1, 4, 10, 10, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1575, 1, 4, 10, 11, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1576, 1, 4, 10, 12, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1577, 1, 4, 10, 13, 1.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1578, 1, 4, 10, 14, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1579, 1, 4, 10, 15, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1580, 1, 4, 10, 16, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1581, 1, 4, 10, 17, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1582, 1, 4, 10, 18, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1583, 1, 4, 10, 19, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1584, 1, 4, 10, 20, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1585, 1, 4, 10, 21, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1586, 1, 4, 10, 22, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1587, 1, 4, 10, 23, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1588, 1, 4, 10, 24, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1589, 1, 4, 11, 1, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1590, 1, 4, 11, 2, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1591, 1, 4, 11, 3, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1592, 1, 4, 11, 4, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1593, 1, 4, 11, 5, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1594, 1, 4, 11, 6, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1595, 1, 4, 11, 7, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1596, 1, 4, 11, 8, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1597, 1, 4, 11, 9, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1598, 1, 4, 11, 10, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1599, 1, 4, 11, 11, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1600, 1, 4, 11, 12, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1601, 1, 4, 11, 13, 1.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1602, 1, 4, 11, 14, 2.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1603, 1, 4, 11, 15, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1604, 1, 4, 11, 16, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1605, 1, 4, 11, 17, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1606, 1, 4, 11, 18, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1607, 1, 4, 11, 19, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1608, 1, 4, 11, 20, 3.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1609, 1, 4, 11, 21, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1610, 1, 4, 11, 22, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1611, 1, 4, 11, 23, 4.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1612, 1, 4, 11, 24, 5.0000, 'human', '2026-04-15 14:25:47', '2026-04-15 14:25:47'),
(1613, 1, 14, 10, 1, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1614, 1, 14, 10, 2, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1615, 1, 14, 10, 3, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1616, 1, 14, 10, 4, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1617, 1, 14, 10, 5, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1618, 1, 14, 10, 6, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1619, 1, 14, 10, 7, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1620, 1, 14, 10, 8, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1621, 1, 14, 10, 9, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1622, 1, 14, 10, 10, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1623, 1, 14, 10, 11, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1624, 1, 14, 10, 12, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1625, 1, 14, 10, 13, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1626, 1, 14, 10, 14, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1627, 1, 14, 10, 15, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1628, 1, 14, 10, 16, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1629, 1, 14, 10, 17, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1630, 1, 14, 10, 18, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1631, 1, 14, 10, 19, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1632, 1, 14, 10, 20, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1633, 1, 14, 10, 21, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1634, 1, 14, 10, 22, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1635, 1, 14, 10, 23, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1636, 1, 14, 10, 24, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1637, 1, 14, 11, 1, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1638, 1, 14, 11, 2, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1639, 1, 14, 11, 3, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1640, 1, 14, 11, 4, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1641, 1, 14, 11, 5, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1642, 1, 14, 11, 6, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1643, 1, 14, 11, 7, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1644, 1, 14, 11, 8, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1645, 1, 14, 11, 9, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1646, 1, 14, 11, 10, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1647, 1, 14, 11, 11, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1648, 1, 14, 11, 12, 4.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1649, 1, 14, 11, 13, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1650, 1, 14, 11, 14, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1651, 1, 14, 11, 15, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1652, 1, 14, 11, 16, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1653, 1, 14, 11, 17, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1654, 1, 14, 11, 18, 2.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1655, 1, 14, 11, 19, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1656, 1, 14, 11, 20, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1657, 1, 14, 11, 21, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1658, 1, 14, 11, 22, 3.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1659, 1, 14, 11, 23, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1660, 1, 14, 11, 24, 5.0000, 'human', '2026-04-15 14:27:04', '2026-04-15 14:27:04'),
(1661, 1, 3, 9, 1, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1662, 1, 3, 9, 2, 3.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1663, 1, 3, 9, 3, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1664, 1, 3, 9, 4, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1665, 1, 3, 9, 5, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1666, 1, 3, 9, 6, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1667, 1, 3, 9, 7, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1668, 1, 3, 9, 8, 3.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1669, 1, 3, 9, 9, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1670, 1, 3, 9, 10, 3.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1671, 1, 3, 9, 11, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1672, 1, 3, 9, 12, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1673, 1, 3, 9, 13, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1674, 1, 3, 9, 14, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1675, 1, 3, 9, 15, 4.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1676, 1, 3, 9, 16, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1677, 1, 3, 9, 17, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1678, 1, 3, 9, 18, 4.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1679, 1, 3, 9, 19, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1680, 1, 3, 9, 20, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1681, 1, 3, 9, 21, 2.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1682, 1, 3, 9, 22, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1683, 1, 3, 9, 23, 5.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1684, 1, 3, 9, 24, 3.0000, 'human', '2026-04-15 14:33:16', '2026-04-15 14:33:16'),
(1685, 1, 10, 10, 1, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1686, 1, 10, 10, 2, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1687, 1, 10, 10, 3, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1688, 1, 10, 10, 4, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1689, 1, 10, 10, 5, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1690, 1, 10, 10, 6, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1691, 1, 10, 10, 7, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1692, 1, 10, 10, 8, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1693, 1, 10, 10, 9, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1694, 1, 10, 10, 10, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1695, 1, 10, 10, 11, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1696, 1, 10, 10, 12, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1697, 1, 10, 10, 13, 1.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1698, 1, 10, 10, 14, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1699, 1, 10, 10, 15, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1700, 1, 10, 10, 16, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1701, 1, 10, 10, 17, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1702, 1, 10, 10, 18, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1703, 1, 10, 10, 19, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1704, 1, 10, 10, 20, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1705, 1, 10, 10, 21, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1706, 1, 10, 10, 22, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1707, 1, 10, 10, 23, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1708, 1, 10, 10, 24, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1709, 1, 10, 11, 1, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1710, 1, 10, 11, 2, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1711, 1, 10, 11, 3, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1712, 1, 10, 11, 4, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1713, 1, 10, 11, 5, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1714, 1, 10, 11, 6, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1715, 1, 10, 11, 7, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1716, 1, 10, 11, 8, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1717, 1, 10, 11, 9, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1718, 1, 10, 11, 10, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1719, 1, 10, 11, 11, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1720, 1, 10, 11, 12, 5.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1721, 1, 10, 11, 13, 1.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1722, 1, 10, 11, 14, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1723, 1, 10, 11, 15, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1724, 1, 10, 11, 16, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1725, 1, 10, 11, 17, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1726, 1, 10, 11, 18, 2.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1727, 1, 10, 11, 19, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1728, 1, 10, 11, 20, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1729, 1, 10, 11, 21, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1730, 1, 10, 11, 22, 3.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1731, 1, 10, 11, 23, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1732, 1, 10, 11, 24, 4.0000, 'human', '2026-04-15 16:34:28', '2026-04-15 16:34:28'),
(1733, 1, 7, 10, 1, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1734, 1, 7, 10, 2, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1735, 1, 7, 10, 3, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1736, 1, 7, 10, 4, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1737, 1, 7, 10, 5, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1738, 1, 7, 10, 6, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1739, 1, 7, 10, 7, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1740, 1, 7, 10, 8, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1741, 1, 7, 10, 9, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1742, 1, 7, 10, 10, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1743, 1, 7, 10, 11, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1744, 1, 7, 10, 12, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1745, 1, 7, 10, 13, 1.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1746, 1, 7, 10, 14, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1747, 1, 7, 10, 15, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1748, 1, 7, 10, 16, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1749, 1, 7, 10, 17, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1750, 1, 7, 10, 18, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1751, 1, 7, 10, 19, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1752, 1, 7, 10, 20, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1753, 1, 7, 10, 21, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1754, 1, 7, 10, 22, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1755, 1, 7, 10, 23, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1756, 1, 7, 10, 24, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1757, 1, 7, 11, 1, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1758, 1, 7, 11, 2, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1759, 1, 7, 11, 3, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1760, 1, 7, 11, 4, 5.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1761, 1, 7, 11, 5, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1762, 1, 7, 11, 6, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1763, 1, 7, 11, 7, 5.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1764, 1, 7, 11, 8, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1765, 1, 7, 11, 9, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1766, 1, 7, 11, 10, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1767, 1, 7, 11, 11, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1768, 1, 7, 11, 12, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1769, 1, 7, 11, 13, 2.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1770, 1, 7, 11, 14, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1771, 1, 7, 11, 15, 5.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1772, 1, 7, 11, 16, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1773, 1, 7, 11, 17, 5.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1774, 1, 7, 11, 18, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1775, 1, 7, 11, 19, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1776, 1, 7, 11, 20, 5.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1777, 1, 7, 11, 21, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1778, 1, 7, 11, 22, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1779, 1, 7, 11, 23, 3.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49'),
(1780, 1, 7, 11, 24, 4.0000, 'human', '2026-04-15 16:34:49', '2026-04-15 16:34:49');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2026_02_11_144158_create_alternative_evaluations_table', 1),
(2, '2026_02_11_144158_create_alternatives_table', 1),
(3, '2026_02_11_144158_create_cache_locks_table', 1),
(4, '2026_02_11_144158_create_cache_table', 1),
(5, '2026_02_11_144158_create_criteria_pairwise_table', 1),
(6, '2026_02_11_144158_create_criteria_scoring_parameters_table', 1),
(7, '2026_02_11_144158_create_criteria_scoring_rules_table', 1),
(8, '2026_02_11_144158_create_criteria_table', 1),
(9, '2026_02_11_144158_create_criteria_weights_table', 1),
(10, '2026_02_11_144158_create_decision_session_criteria_table', 1),
(11, '2026_02_11_144158_create_decision_session_dm_table', 1),
(12, '2026_02_11_144158_create_decision_sessions_table', 1),
(13, '2026_02_11_144158_create_failed_jobs_table', 1),
(14, '2026_02_11_144158_create_job_batches_table', 1),
(15, '2026_02_11_144158_create_jobs_table', 1),
(16, '2026_02_11_144158_create_model_has_permissions_table', 1),
(17, '2026_02_11_144158_create_model_has_roles_table', 1),
(18, '2026_02_11_144158_create_password_reset_tokens_table', 1),
(19, '2026_02_11_144158_create_permissions_table', 1),
(20, '2026_02_11_144158_create_role_has_permissions_table', 1),
(21, '2026_02_11_144158_create_roles_table', 1),
(22, '2026_02_11_144158_create_sessions_table', 1),
(23, '2026_02_11_144158_create_users_table', 1),
(24, '2026_02_14_115949_create_alternative_decision_session_table', 2),
(25, '2026_02_14_235243_create_smart_results_dm_table', 3),
(26, '2026_02_14_235338_create_borda_results_table', 3),
(27, '2026_02_18_010731_create_usability_instruments_table', 4),
(28, '2026_02_18_010732_create_usability_questions_table', 4),
(29, '2026_02_18_010733_create_usability_responses_table', 4),
(30, '2026_02_18_010734_create_usability_answers_table', 5),
(31, '2026_04_12_121640_create_decision_session_assignments_table', 6),
(32, '2026_04_13_160031_create_system_rankings_table', 7),
(34, '2026_04_13_233501_create_user_profiles_table', 8),
(35, '2026_04_14_002201_create_decision_results_table', 9),
(36, '2026_04_14_020504_create_saw_result_dms_table', 10),
(37, '2026_04_14_100612_create_dm_scores_table', 11),
(38, '2026_04_14_135332_create_criteria_group_weights_table', 12),
(39, '2026_04_14_152024_create_evaluation_scores_table', 13),
(40, '2026_04_14_154155_create_evaluation_results_table', 14),
(41, '2026_04_14_232657_create_evaluation_results_table', 15),
(42, '2026_04_15_024844_create_evaluation_aggregations_table', 16),
(43, '2026_04_15_124404_create_borda_aggregations_table', 17);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `model_has_roles`
--

INSERT INTO `model_has_roles` (`role_id`, `model_type`, `model_id`) VALUES
(1, 'App\\Models\\User', 1),
(2, 'App\\Models\\User', 2),
(3, 'App\\Models\\User', 3),
(3, 'App\\Models\\User', 4),
(3, 'App\\Models\\User', 5),
(3, 'App\\Models\\User', 6),
(3, 'App\\Models\\User', 7),
(3, 'App\\Models\\User', 8),
(3, 'App\\Models\\User', 9),
(3, 'App\\Models\\User', 10),
(3, 'App\\Models\\User', 11),
(3, 'App\\Models\\User', 12),
(3, 'App\\Models\\User', 13),
(3, 'App\\Models\\User', 14);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'user.view', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(2, 'user.create', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(3, 'user.edit', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(4, 'user.delete', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(5, 'role.view', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(6, 'role.create', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(7, 'role.edit', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(8, 'role.delete', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(9, 'decision.view', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(10, 'decision.create', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(11, 'decision.edit', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(12, 'decision.delete', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52'),
(13, 'decision.close', 'web', '2026-02-17 23:15:52', '2026-02-17 23:15:52');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `guard_name`, `created_at`, `updated_at`) VALUES
(1, 'superadmin', 'web', '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(2, 'admin', 'web', '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(3, 'dm', 'web', '2026-02-12 12:46:46', '2026-02-12 12:46:46');

-- --------------------------------------------------------

--
-- Table structure for table `role_has_permissions`
--

CREATE TABLE `role_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `role_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_has_permissions`
--

INSERT INTO `role_has_permissions` (`permission_id`, `role_id`) VALUES
(1, 1),
(2, 1),
(3, 1),
(4, 1),
(5, 1),
(6, 1),
(7, 1),
(8, 1),
(9, 1),
(10, 1),
(11, 1),
(12, 1),
(13, 1);

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci,
  `payload` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('yakB4h7O0zokk4axgGnWG6B1bNKeRsWKW7VpARWZ', NULL, '172.18.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/147.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiM1RsV2dVVFhTSlZRZGlOYXVQWDN0RTh3dk9XMnlVZTdSNUlxS1UxNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMCI7czo1OiJyb3V0ZSI7Tjt9fQ==', 1776271274);

-- --------------------------------------------------------

--
-- Table structure for table `system_rankings`
--

CREATE TABLE `system_rankings` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `alternative_id` bigint UNSIGNED NOT NULL,
  `smart_score` decimal(10,6) NOT NULL,
  `rank_system` int UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_rankings`
--

INSERT INTO `system_rankings` (`id`, `decision_session_id`, `alternative_id`, `smart_score`, `rank_system`, `created_at`, `updated_at`) VALUES
(7, 5, 49, 0.084761, 1, '2026-04-14 02:15:03', '2026-04-14 02:15:03'),
(8, 5, 50, 0.071695, 2, '2026-04-14 02:15:03', '2026-04-14 02:15:03'),
(9, 5, 51, 0.050940, 3, '2026-04-14 02:15:03', '2026-04-14 02:15:03'),
(82, 1, 8, 0.175174, 1, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(83, 1, 16, 0.165540, 2, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(84, 1, 13, 0.163818, 3, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(85, 1, 2, 0.156301, 4, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(86, 1, 7, 0.147066, 5, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(87, 1, 17, 0.133735, 6, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(88, 1, 20, 0.129154, 7, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(89, 1, 18, 0.120810, 8, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(90, 1, 6, 0.118781, 9, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(91, 1, 4, 0.117360, 10, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(92, 1, 14, 0.100640, 11, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(93, 1, 15, 0.097375, 12, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(94, 1, 5, 0.086240, 13, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(95, 1, 22, 0.083396, 14, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(96, 1, 24, 0.078508, 15, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(97, 1, 19, 0.068391, 16, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(98, 1, 9, 0.060183, 17, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(99, 1, 10, 0.057922, 18, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(100, 1, 11, 0.056468, 19, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(101, 1, 12, 0.054564, 20, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(102, 1, 21, 0.051206, 21, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(103, 1, 3, 0.048414, 22, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(104, 1, 23, 0.047848, 23, '2026-04-15 03:04:54', '2026-04-15 03:04:54'),
(105, 1, 1, 0.031695, 24, '2026-04-15 03:04:54', '2026-04-15 03:04:54');

-- --------------------------------------------------------

--
-- Table structure for table `usability_answers`
--

CREATE TABLE `usability_answers` (
  `id` bigint UNSIGNED NOT NULL,
  `usability_response_id` bigint UNSIGNED NOT NULL,
  `usability_question_id` bigint UNSIGNED NOT NULL,
  `value` tinyint UNSIGNED NOT NULL COMMENT 'Likert scale 1–5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `usability_instruments`
--

CREATE TABLE `usability_instruments` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usability_instruments`
--

INSERT INTO `usability_instruments` (`id`, `name`, `description`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'System Usability Scale (SUS)', 'Instrumen standar untuk mengukur usability sistem.', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `usability_questions`
--

CREATE TABLE `usability_questions` (
  `id` bigint UNSIGNED NOT NULL,
  `usability_instrument_id` bigint UNSIGNED NOT NULL,
  `number` tinyint UNSIGNED NOT NULL,
  `question` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `polarity` enum('positive','negative') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `usability_questions`
--

INSERT INTO `usability_questions` (`id`, `usability_instrument_id`, `number`, `question`, `polarity`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'Saya ingin menggunakan sistem ini secara sering.', 'positive', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(2, 1, 2, 'Saya merasa sistem ini terlalu kompleks.', 'negative', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(3, 1, 3, 'Saya merasa sistem ini mudah digunakan.', 'positive', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(4, 1, 4, 'Saya membutuhkan bantuan teknis untuk dapat menggunakan sistem ini.', 'negative', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(5, 1, 5, 'Saya merasa berbagai fungsi dalam sistem ini terintegrasi dengan baik.', 'positive', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(6, 1, 6, 'Saya merasa terdapat terlalu banyak inkonsistensi dalam sistem ini.', 'negative', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(7, 1, 7, 'Saya merasa kebanyakan orang akan mudah mempelajari penggunaan sistem ini dengan cepat.', 'positive', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(8, 1, 8, 'Saya merasa sistem ini sangat merepotkan untuk digunakan.', 'negative', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(9, 1, 9, 'Saya merasa sangat percaya diri saat menggunakan sistem ini.', 'positive', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48'),
(10, 1, 10, 'Saya perlu mempelajari banyak hal sebelum dapat menggunakan sistem ini.', 'negative', 1, '2026-02-18 01:49:48', '2026-02-18 01:49:48');

-- --------------------------------------------------------

--
-- Table structure for table `usability_responses`
--

CREATE TABLE `usability_responses` (
  `id` bigint UNSIGNED NOT NULL,
  `usability_instrument_id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `role` varchar(50) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `decision_session_id` bigint UNSIGNED DEFAULT NULL,
  `total_score` decimal(5,2) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@wonosarigk.id', NULL, '$2y$12$LjZSt79diNOmML6z7fW6e.IAUqIDYZMkiGbKO70agXjFaZX8xCIxm', NULL, '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(2, 'Admin Keputusan', 'admin@wonosarigk.id', NULL, '$2y$12$9LK98yBUOoTvwFEc352pHe6dNfB7ZM99.KaT5YFFZAFkwAPqzmGsm', 'HTDsGD46ROKMOkS6oq9nQb9ghNzpjBQ4Hv1uiFJx6oJLhZWReYcyfXmPANDp', '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(3, 'Tumija', 'tumija@wonosarigk.id', NULL, '$2y$12$NStoc26b/AAcxtCnK5ryl.YCKm4UlV2D7sSdxbnTUYnLbGdZ8Q5SG', NULL, '2026-02-12 12:46:47', '2026-02-12 12:46:47'),
(4, 'Dedi Mustajab', 'dedi@wonosarigk.id', NULL, '$2y$12$407xvo06FQjNg6XqTkYvYuEiMX3A2ZG4l6XKZi9pi878Y71V6w5JG', 'U6rawMU1CKKD9AbDSevJqiKH2FVpsFjX03YPeVZXTLUR5oOCc5vGGYDw4NMC', '2026-02-12 14:53:58', '2026-02-12 14:53:58'),
(5, 'Windira Safitri', 'windira@wonosarigk.id', NULL, '$2y$12$3b.wmOO34rqbbj0gzraFuODiHTjWeQT2leiyWWMijFU5H48sgO2FO', NULL, '2026-02-12 14:54:17', '2026-02-12 14:54:17'),
(6, 'Tugino', 'tugino@wonosarigk.id', NULL, '$2y$12$601gCaq7fy9l8XZf5NCPKur6VPWXETXLdVleGJgmsSeFmE3nw9kAy', NULL, '2026-02-12 14:56:08', '2026-02-12 14:56:08'),
(7, 'Suindartini', 'suindartini@wonosarigk.id', NULL, '$2y$12$lPh6h.zlsrfidrBCx17rzuMRoJPn3GIccO8k8SkeZ0SQcbEUgYJP2', NULL, '2026-02-12 14:56:27', '2026-02-20 10:23:28'),
(8, 'Suyatna', 'suyatna@wonosarigk.id', NULL, '$2y$12$Ho1ZtN2g67VzkGaDTSZyH.lsh6NkgCNEqzr4kxxdSVDnuk5RUS7fW', NULL, '2026-02-12 14:56:51', '2026-02-12 14:56:51'),
(9, 'Ubaidilah Aminuddin Thoyieb', 'ubaidilah@wonosarigk.id', NULL, '$2y$12$3yNfBg5iTtVu1ko.Bxpq6Oiyq5flyuX7MZcnIeusxasRh7rpm3pf.', NULL, '2026-02-12 14:57:08', '2026-02-12 14:57:08'),
(10, 'Ari Ismawan', 'ari@wonosarigk.id', NULL, '$2y$12$dE0TKFHHO5vBcbKdi0rXt.NFv4/TWf3kNTRrGPEYfuMJnb4kqIMlK', NULL, '2026-02-12 14:57:24', '2026-02-12 14:57:24'),
(11, 'Sularno', 'sularno@wonosarigk.id', NULL, '$2y$12$MDFij4JGsW7/XY/xptYnTO0VSqMWN3mEXZ1l9T.swc4uamFqbNska', NULL, '2026-02-12 14:58:00', '2026-02-12 14:58:00'),
(12, 'Saban Nurhuda', 'saban@wonosarigk.id', NULL, '$2y$12$orwHCrqQPcMBsiYo5.nAnu1nj79RZyx2t1mhIeCqG6MnQTrDKr8JG', NULL, '2026-02-12 14:58:21', '2026-02-12 14:58:21'),
(13, 'Wahyono', 'wahyono@wonosarigk.id', NULL, '$2y$12$.mWXmxneZow3SN4X24UrL.HMjWZmzGt1Pc6qhPVHunL6cFfKPd2fC', NULL, '2026-02-12 14:58:38', '2026-02-12 14:58:38'),
(14, 'Wijayadi', 'wijayadi@wonosarigk.id', NULL, '$2y$12$llfxCOQF2SEO02hgofr5S.G6rI4.07HZFltUaWoEze6AIGeoAjMi6', NULL, '2026-02-12 14:59:04', '2026-02-12 14:59:04');

-- --------------------------------------------------------

--
-- Table structure for table `user_profiles`
--

CREATE TABLE `user_profiles` (
  `id` bigint UNSIGNED NOT NULL,
  `user_id` bigint UNSIGNED NOT NULL,
  `nama_lengkap` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `alamat` text COLLATE utf8mb4_unicode_ci,
  `dusun` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rt` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `rw` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unsur` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `jabatan` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `instansi` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `kategori_dm` enum('strategis','partisipatif','teknokratis') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'partisipatif',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `user_profiles`
--

INSERT INTO `user_profiles` (`id`, `user_id`, `nama_lengkap`, `alamat`, `dusun`, `rt`, `rw`, `unsur`, `jabatan`, `instansi`, `kategori_dm`, `created_at`, `updated_at`) VALUES
(1, 10, 'Ari Ismawan', 'Purbosari', 'Purbosari', '2', '6', 'wilayah', 'Ketua RT', '-', 'partisipatif', '2026-04-14 07:29:38', '2026-04-14 07:29:38'),
(2, 4, 'Dedi Mustajab, S.Ag, MA', 'Tawarsari', 'Tawarsari', '2', '18', 'wilayah', 'Ketua RT', '-', 'partisipatif', '2026-04-14 07:30:15', '2026-04-14 07:30:15'),
(3, 7, 'Suindartini, S.Sos, M.Si', 'Gadungsari', 'Gadungsari', '6', '11', 'perempuan', 'Tokoh Masyarakat', '-', 'partisipatif', '2026-04-14 07:31:02', '2026-04-14 07:31:02'),
(4, 11, 'Sularno, SE', 'Jeruksari', 'Jeruksari', '7', '22', 'wilayah', 'Ketua RT', '-', 'partisipatif', '2026-04-14 07:31:47', '2026-04-14 07:31:47'),
(5, 8, 'Suyatna, SE', 'Pandansari', 'Pandansari', '9', '17', 'lembaga', 'Sekretaris LPMK', '-', 'partisipatif', '2026-04-14 07:32:22', '2026-04-14 07:32:22'),
(6, 3, 'Tumija', 'Pandansari', 'Pandansari', '4', '15', 'Pemerintah Desa', 'Kepala Desa', 'Pemerintah Kalurahan Wonosari', 'strategis', '2026-04-14 07:32:57', '2026-04-14 07:32:57'),
(7, 13, 'Wahyono, S.Sos.', 'Ringinsari', 'Ringinsari', '4', '5', 'lembaga', 'Anggota LPMK', '-', 'partisipatif', '2026-04-14 07:33:33', '2026-04-14 07:33:33'),
(8, 14, 'Wijayadi, S.Sos.', 'Madusari', 'Madusari', '8', '3', 'wilayah', 'Ketua RT', '-', 'partisipatif', '2026-04-14 07:34:12', '2026-04-14 07:34:12');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alternatives_decision_session_id_code_unique` (`decision_session_id`,`code`),
  ADD KEY `fk_alternatives_criteria` (`criteria_id`);

--
-- Indexes for table `alternative_evaluations`
--
ALTER TABLE `alternative_evaluations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_dm_alt_criteria_session` (`decision_session_id`,`dm_id`,`alternative_id`,`criteria_id`),
  ADD KEY `alternative_evaluations_dm_id_foreign` (`dm_id`),
  ADD KEY `alternative_evaluations_alternative_id_foreign` (`alternative_id`),
  ADD KEY `alternative_evaluations_criteria_id_foreign` (`criteria_id`);

--
-- Indexes for table `borda_aggregations`
--
ALTER TABLE `borda_aggregations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borda_unique` (`decision_session_id`,`method`,`level`,`source`,`alternative_id`),
  ADD KEY `borda_aggregations_alternative_id_foreign` (`alternative_id`);

--
-- Indexes for table `borda_results`
--
ALTER TABLE `borda_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `borda_results_decision_session_id_alternative_id_unique` (`decision_session_id`,`alternative_id`),
  ADD KEY `borda_results_alternative_id_foreign` (`alternative_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_expiration_index` (`expiration`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`),
  ADD KEY `cache_locks_expiration_index` (`expiration`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`),
  ADD KEY `criteria_decision_session_id_foreign` (`decision_session_id`);

--
-- Indexes for table `criteria_group_weights`
--
ALTER TABLE `criteria_group_weights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `criteria_group_weights_decision_session_id_unique` (`decision_session_id`);

--
-- Indexes for table `criteria_pairwise`
--
ALTER TABLE `criteria_pairwise`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_pairwise_session_dm_criteria` (`decision_session_id`,`dm_id`,`criteria_id_1`,`criteria_id_2`),
  ADD KEY `criteria_pairwise_dm_id_foreign` (`dm_id`),
  ADD KEY `criteria_pairwise_criteria_id_1_foreign` (`criteria_id_1`),
  ADD KEY `criteria_pairwise_criteria_id_2_foreign` (`criteria_id_2`);

--
-- Indexes for table `criteria_scoring_parameters`
--
ALTER TABLE `criteria_scoring_parameters`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_scoring_rule_param` (`scoring_rule_id`,`param_key`);

--
-- Indexes for table `criteria_scoring_rules`
--
ALTER TABLE `criteria_scoring_rules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_criteria_rule` (`criteria_id`),
  ADD KEY `criteria_scoring_rules_decision_session_id_foreign` (`decision_session_id`);

--
-- Indexes for table `criteria_weights`
--
ALTER TABLE `criteria_weights`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `criteria_weights_decision_session_id_dm_id_unique` (`decision_session_id`,`dm_id`),
  ADD KEY `criteria_weights_dm_id_foreign` (`dm_id`);

--
-- Indexes for table `decision_sessions`
--
ALTER TABLE `decision_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `decision_sessions_created_by_foreign` (`created_by`);

--
-- Indexes for table `decision_session_assignments`
--
ALTER TABLE `decision_session_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `decision_session_assignments_decision_session_id_index` (`decision_session_id`),
  ADD KEY `decision_session_assignments_user_id_index` (`user_id`),
  ADD KEY `decision_session_assignments_criteria_id_index` (`criteria_id`);

--
-- Indexes for table `decision_session_criteria`
--
ALTER TABLE `decision_session_criteria`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `decision_session_criteria_decision_session_id_criteria_id_unique` (`decision_session_id`,`criteria_id`),
  ADD KEY `decision_session_criteria_criteria_id_foreign` (`criteria_id`);

--
-- Indexes for table `decision_session_dm`
--
ALTER TABLE `decision_session_dm`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `decision_session_dm_decision_session_id_user_id_unique` (`decision_session_id`,`user_id`),
  ADD KEY `decision_session_dm_user_id_foreign` (`user_id`);

--
-- Indexes for table `evaluation_aggregations`
--
ALTER TABLE `evaluation_aggregations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_eval_agg` (`decision_session_id`,`user_id`,`alternative_id`,`method`),
  ADD KEY `evaluation_aggregations_user_id_foreign` (`user_id`),
  ADD KEY `evaluation_aggregations_alternative_id_foreign` (`alternative_id`);

--
-- Indexes for table `evaluation_results`
--
ALTER TABLE `evaluation_results`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uniq_eval_result` (`decision_session_id`,`alternative_id`,`criteria_id`,`method`),
  ADD UNIQUE KEY `unique_evaluation` (`decision_session_id`,`user_id`,`alternative_id`,`criteria_id`,`method`),
  ADD KEY `evaluation_results_alternative_id_foreign` (`alternative_id`),
  ADD KEY `evaluation_results_criteria_id_foreign` (`criteria_id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_session_user_method` (`decision_session_id`,`user_id`,`method`);

--
-- Indexes for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD PRIMARY KEY (`id`),
  ADD KEY `evaluation_scores_user_id_foreign` (`user_id`),
  ADD KEY `evaluation_scores_criteria_id_foreign` (`criteria_id`),
  ADD KEY `evaluation_scores_alternative_id_foreign` (`alternative_id`),
  ADD KEY `evaluation_scores_decision_session_id_criteria_id_index` (`decision_session_id`,`criteria_id`),
  ADD KEY `evaluation_scores_decision_session_id_alternative_id_index` (`decision_session_id`,`alternative_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `model_has_permissions`
--
ALTER TABLE `model_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`model_id`,`model_type`),
  ADD KEY `model_has_permissions_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `model_has_roles`
--
ALTER TABLE `model_has_roles`
  ADD PRIMARY KEY (`role_id`,`model_id`,`model_type`),
  ADD KEY `model_has_roles_model_id_model_type_index` (`model_id`,`model_type`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `permissions_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_guard_name_unique` (`name`,`guard_name`);

--
-- Indexes for table `role_has_permissions`
--
ALTER TABLE `role_has_permissions`
  ADD PRIMARY KEY (`permission_id`,`role_id`),
  ADD KEY `role_has_permissions_role_id_foreign` (`role_id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `system_rankings`
--
ALTER TABLE `system_rankings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `system_rankings_decision_session_id_alternative_id_unique` (`decision_session_id`,`alternative_id`),
  ADD KEY `system_rankings_alternative_id_foreign` (`alternative_id`);

--
-- Indexes for table `usability_answers`
--
ALTER TABLE `usability_answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usability_answer_once` (`usability_response_id`,`usability_question_id`),
  ADD KEY `usability_answers_usability_question_id_index` (`usability_question_id`);

--
-- Indexes for table `usability_instruments`
--
ALTER TABLE `usability_instruments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `usability_questions`
--
ALTER TABLE `usability_questions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `usability_questions_usability_instrument_id_number_unique` (`usability_instrument_id`,`number`);

--
-- Indexes for table `usability_responses`
--
ALTER TABLE `usability_responses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_usability_response_once` (`usability_instrument_id`,`user_id`,`decision_session_id`),
  ADD KEY `usability_responses_user_id_foreign` (`user_id`),
  ADD KEY `1` (`decision_session_id`),
  ADD KEY `usability_responses_role_index` (`role`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- Indexes for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_profiles_user_id_foreign` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `alternative_evaluations`
--
ALTER TABLE `alternative_evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `borda_aggregations`
--
ALTER TABLE `borda_aggregations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=193;

--
-- AUTO_INCREMENT for table `borda_results`
--
ALTER TABLE `borda_results`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `criteria_group_weights`
--
ALTER TABLE `criteria_group_weights`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `criteria_pairwise`
--
ALTER TABLE `criteria_pairwise`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=91;

--
-- AUTO_INCREMENT for table `criteria_scoring_parameters`
--
ALTER TABLE `criteria_scoring_parameters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `criteria_scoring_rules`
--
ALTER TABLE `criteria_scoring_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `criteria_weights`
--
ALTER TABLE `criteria_weights`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `decision_sessions`
--
ALTER TABLE `decision_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `decision_session_assignments`
--
ALTER TABLE `decision_session_assignments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `decision_session_criteria`
--
ALTER TABLE `decision_session_criteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `decision_session_dm`
--
ALTER TABLE `decision_session_dm`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `evaluation_aggregations`
--
ALTER TABLE `evaluation_aggregations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=145;

--
-- AUTO_INCREMENT for table `evaluation_results`
--
ALTER TABLE `evaluation_results`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2017;

--
-- AUTO_INCREMENT for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=1781;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `system_rankings`
--
ALTER TABLE `system_rankings`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=106;

--
-- AUTO_INCREMENT for table `usability_answers`
--
ALTER TABLE `usability_answers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `usability_instruments`
--
ALTER TABLE `usability_instruments`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `usability_questions`
--
ALTER TABLE `usability_questions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `usability_responses`
--
ALTER TABLE `usability_responses`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `user_profiles`
--
ALTER TABLE `user_profiles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD CONSTRAINT `fk_alternatives_criteria` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `borda_aggregations`
--
ALTER TABLE `borda_aggregations`
  ADD CONSTRAINT `borda_aggregations_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borda_aggregations_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `borda_results`
--
ALTER TABLE `borda_results`
  ADD CONSTRAINT `borda_results_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `borda_results_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `criteria_group_weights`
--
ALTER TABLE `criteria_group_weights`
  ADD CONSTRAINT `criteria_group_weights_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `decision_session_assignments`
--
ALTER TABLE `decision_session_assignments`
  ADD CONSTRAINT `decision_session_assignments_criteria_id_foreign` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `decision_session_assignments_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `decision_session_assignments_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `evaluation_aggregations`
--
ALTER TABLE `evaluation_aggregations`
  ADD CONSTRAINT `evaluation_aggregations_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_aggregations_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_aggregations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `evaluation_results`
--
ALTER TABLE `evaluation_results`
  ADD CONSTRAINT `evaluation_results_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_results_criteria_id_foreign` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_results_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_evaluation_results_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `evaluation_scores`
--
ALTER TABLE `evaluation_scores`
  ADD CONSTRAINT `evaluation_scores_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_scores_criteria_id_foreign` FOREIGN KEY (`criteria_id`) REFERENCES `criteria` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_scores_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `evaluation_scores_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `system_rankings`
--
ALTER TABLE `system_rankings`
  ADD CONSTRAINT `system_rankings_alternative_id_foreign` FOREIGN KEY (`alternative_id`) REFERENCES `alternatives` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `system_rankings_decision_session_id_foreign` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usability_answers`
--
ALTER TABLE `usability_answers`
  ADD CONSTRAINT `fk_usability_answers_question` FOREIGN KEY (`usability_question_id`) REFERENCES `usability_questions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_usability_answers_response` FOREIGN KEY (`usability_response_id`) REFERENCES `usability_responses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usability_questions`
--
ALTER TABLE `usability_questions`
  ADD CONSTRAINT `usability_questions_usability_instrument_id_foreign` FOREIGN KEY (`usability_instrument_id`) REFERENCES `usability_instruments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `usability_responses`
--
ALTER TABLE `usability_responses`
  ADD CONSTRAINT `1` FOREIGN KEY (`decision_session_id`) REFERENCES `decision_sessions` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `usability_responses_usability_instrument_id_foreign` FOREIGN KEY (`usability_instrument_id`) REFERENCES `usability_instruments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `usability_responses_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `user_profiles`
--
ALTER TABLE `user_profiles`
  ADD CONSTRAINT `user_profiles_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
