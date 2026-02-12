-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql
-- Generation Time: Feb 12, 2026 at 04:15 PM
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
  `code` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int UNSIGNED NOT NULL DEFAULT '0',
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `alternatives`
--

INSERT INTO `alternatives` (`id`, `decision_session_id`, `code`, `name`, `order`, `is_active`, `deleted_at`, `created_at`, `updated_at`) VALUES
(1, 1, 'A1', 'Peningkatan jalan lingkungan dan akses lokal permukiman', 1, 1, NULL, '2026-02-12 15:03:57', '2026-02-12 15:03:57'),
(2, 1, 'A2', 'Penguatan manajemen usaha dan permodalan ekonomi desa', 2, 1, NULL, '2026-02-12 15:04:03', '2026-02-12 15:04:03'),
(3, 1, 'A3', 'Normalisasi dan rehabilitasi saluran drainase eksisting', 3, 1, NULL, '2026-02-12 15:04:07', '2026-02-12 15:04:07'),
(4, 1, 'A4', 'Peningkatan layanan kesehatan dasar desa', 4, 1, NULL, '2026-02-12 15:04:32', '2026-02-12 15:04:32'),
(5, 1, 'A5', 'Penguatan administrasi dan layanan pemerintahan desa', 5, 1, NULL, '2026-02-12 15:04:36', '2026-02-12 15:04:36'),
(6, 1, 'A6', 'Pengelolaan sampah dan kebersihan lingkungan', 6, 1, NULL, '2026-02-12 15:04:40', '2026-02-12 15:04:40'),
(7, 1, 'A7', 'Pelatihan keterampilan kerja masyarakat', 7, 1, NULL, '2026-02-12 15:04:45', '2026-02-12 15:04:45'),
(8, 1, 'A8', 'Penyediaan dan pengelolaan layanan pemakaman desa', 8, 1, NULL, '2026-02-12 15:04:51', '2026-02-12 15:04:51'),
(9, 1, 'A9', 'Peningkatan keamanan lingkungan berbasis warga', 9, 1, NULL, '2026-02-12 15:04:55', '2026-02-12 15:04:55'),
(10, 1, 'A10', 'Revitalisasi dan pembangunan bangunan publik desa', 10, 1, NULL, '2026-02-12 15:04:59', '2026-02-12 15:04:59');

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
  `utility_value` decimal(8,6) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('rembug-desa-cache-admin@desa.id|127.0.0.1', 'i:1;', 1770908410),
('rembug-desa-cache-admin@desa.id|127.0.0.1:timer', 'i:1770908410;', 1770908410),
('rembug-desa-cache-ateritori@gmail.com|127.0.0.1', 'i:1;', 1770860393),
('rembug-desa-cache-ateritori@gmail.com|127.0.0.1:timer', 'i:1770860393;', 1770860393);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `type` enum('benefit','cost') COLLATE utf8mb4_unicode_ci NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT '1',
  `order` int NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `decision_session_id`, `name`, `type`, `is_active`, `order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'Pelayanan Dasar', 'benefit', 1, 1, '2026-02-12 15:04:18', '2026-02-12 15:04:18', NULL),
(2, 1, 'Infrastruktur dan Lingkungan', 'benefit', 1, 2, '2026-02-12 15:05:16', '2026-02-12 15:05:16', NULL),
(3, 1, 'Ekonomi Produktif dan Pertanian', 'benefit', 1, 3, '2026-02-12 15:05:25', '2026-02-12 15:05:25', NULL),
(4, 1, 'Teknologi Tepat Guna', 'benefit', 1, 4, '2026-02-12 15:05:33', '2026-02-12 15:05:33', NULL),
(5, 1, 'Ketertiban dan Ketentraman', 'benefit', 1, 5, '2026-02-12 15:05:38', '2026-02-12 15:05:38', NULL);

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
  `direction` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `criteria_scoring_parameters`
--

CREATE TABLE `criteria_scoring_parameters` (
  `id` bigint UNSIGNED NOT NULL,
  `scoring_rule_id` bigint UNSIGNED NOT NULL,
  `param_key` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `param_value` json NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_scoring_parameters`
--

INSERT INTO `criteria_scoring_parameters` (`id`, `scoring_rule_id`, `param_key`, `param_value`, `created_at`, `updated_at`) VALUES
(1, 1, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-02-12 15:16:14', '2026-02-12 15:16:14'),
(2, 1, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-02-12 15:16:14', '2026-02-12 15:16:14'),
(3, 1, 'scale_utilities', '{\"1\": \"0.00\", \"2\": \"0.4\", \"3\": \"0.7\", \"4\": \"0.9\", \"5\": \"1.00\"}', '2026-02-12 15:16:14', '2026-02-12 15:16:14'),
(4, 2, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-02-12 15:16:36', '2026-02-12 15:16:36'),
(5, 2, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-02-12 15:16:36', '2026-02-12 15:16:36'),
(6, 2, 'scale_utilities', '{\"1\": \"0.00\", \"2\": \"0.25\", \"3\": \"0.50\", \"4\": \"0.75\", \"5\": \"1.00\"}', '2026-02-12 15:16:36', '2026-02-12 15:16:36'),
(7, 3, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-02-12 15:17:14', '2026-02-12 15:17:14'),
(8, 3, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-02-12 15:17:14', '2026-02-12 15:17:14'),
(9, 3, 'scale_utilities', '{\"1\": \"0.00\", \"2\": \"0.1\", \"3\": \"0.25\", \"4\": \"0.55\", \"5\": \"1.00\"}', '2026-02-12 15:17:14', '2026-02-12 15:17:14'),
(10, 4, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-02-12 15:29:02', '2026-02-12 15:29:02'),
(11, 4, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-02-12 15:29:02', '2026-02-12 15:29:02'),
(12, 4, 'scale_utilities', '{\"1\": \"0.00\", \"2\": \"0.1\", \"3\": \"0.25\", \"4\": \"0.55\", \"5\": \"1.00\"}', '2026-02-12 15:29:02', '2026-02-12 15:29:02'),
(13, 5, 'scale_range', '{\"max\": 5, \"min\": 1}', '2026-02-12 15:29:39', '2026-02-12 15:29:39'),
(14, 5, 'scale_semantics', '{\"1\": \"Sangat Rendah\", \"2\": \"Rendah\", \"3\": \"Sedang\", \"4\": \"Tinggi\", \"5\": \"Sangat Tinggi\"}', '2026-02-12 15:29:39', '2026-02-12 15:29:39'),
(15, 5, 'scale_utilities', '{\"1\": \"0.00\", \"2\": \"0.4\", \"3\": \"0.7\", \"4\": \"0.9\", \"5\": \"1.00\"}', '2026-02-12 15:29:39', '2026-02-12 15:29:39');

-- --------------------------------------------------------

--
-- Table structure for table `criteria_scoring_rules`
--

CREATE TABLE `criteria_scoring_rules` (
  `id` bigint UNSIGNED NOT NULL,
  `decision_session_id` bigint UNSIGNED DEFAULT NULL,
  `criteria_id` bigint UNSIGNED NOT NULL,
  `input_type` enum('scale','numeric') COLLATE utf8mb4_unicode_ci NOT NULL,
  `preference_type` enum('linear','concave','convex') COLLATE utf8mb4_unicode_ci NOT NULL,
  `utility_mode` enum('system','custom') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'system',
  `curve_param` decimal(5,3) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `criteria_scoring_rules`
--

INSERT INTO `criteria_scoring_rules` (`id`, `decision_session_id`, `criteria_id`, `input_type`, `preference_type`, `utility_mode`, `curve_param`, `created_at`, `updated_at`) VALUES
(1, 1, 1, 'scale', 'concave', 'system', NULL, '2026-02-12 15:16:14', '2026-02-12 15:16:14'),
(2, 1, 2, 'scale', 'linear', 'system', NULL, '2026-02-12 15:16:36', '2026-02-12 15:16:36'),
(3, 1, 3, 'scale', 'convex', 'system', NULL, '2026-02-12 15:17:14', '2026-02-12 15:17:14'),
(4, 1, 4, 'scale', 'convex', 'system', NULL, '2026-02-12 15:29:02', '2026-02-12 15:29:02'),
(5, 1, 5, 'scale', 'concave', 'system', NULL, '2026-02-12 15:29:39', '2026-02-12 15:29:39');

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

-- --------------------------------------------------------

--
-- Table structure for table `decision_sessions`
--

CREATE TABLE `decision_sessions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(1, 'Musrenbang Kalurahan Wonosari', '2026', 'configured', 2, '2026-02-12 12:32:58', '2026-02-12 15:33:53', NULL);

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

--
-- Dumping data for table `decision_session_dm`
--

INSERT INTO `decision_session_dm` (`id`, `decision_session_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 1, 4, NULL, NULL),
(2, 1, 5, NULL, NULL),
(3, 1, 6, NULL, NULL),
(4, 1, 7, NULL, NULL),
(5, 1, 8, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
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
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
(23, '2026_02_11_144158_create_users_table', 1);

-- --------------------------------------------------------

--
-- Table structure for table `model_has_permissions`
--

CREATE TABLE `model_has_permissions` (
  `permission_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `model_id` bigint UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `model_has_roles`
--

CREATE TABLE `model_has_roles` (
  `role_id` bigint UNSIGNED NOT NULL,
  `model_type` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `guard_name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
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

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('e9ldaZZ2SbQKlDKC0NSP992TGoA94Wt2QJ3qznjl', 2, '127.0.0.1', 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/144.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoibW9vQ0RMNkVOeDFhb1ZYd3hta2cwc2g3NzM3dG5zcjJRVzY4NVgxayI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzE6Imh0dHA6Ly9sb2NhbGhvc3Q6ODAwMC9kYXNoYm9hcmQiO3M6NToicm91dGUiO3M6OToiZGFzaGJvYXJkIjt9czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1770912941);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'superadmin@wonosarigk.id', NULL, '$2y$12$LjZSt79diNOmML6z7fW6e.IAUqIDYZMkiGbKO70agXjFaZX8xCIxm', NULL, '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(2, 'Admin Keputusan', 'admin@wonosarigk.id', NULL, '$2y$12$9LK98yBUOoTvwFEc352pHe6dNfB7ZM99.KaT5YFFZAFkwAPqzmGsm', NULL, '2026-02-12 01:43:33', '2026-02-12 01:43:33'),
(3, 'Decision Maker', 'dm@wonosarigk.id', NULL, '$2y$12$NStoc26b/AAcxtCnK5ryl.YCKm4UlV2D7sSdxbnTUYnLbGdZ8Q5SG', NULL, '2026-02-12 12:46:47', '2026-02-12 12:46:47'),
(4, 'Dedi Mustajab', 'dedi@wonosarigk.id', NULL, '$2y$12$407xvo06FQjNg6XqTkYvYuEiMX3A2ZG4l6XKZi9pi878Y71V6w5JG', NULL, '2026-02-12 14:53:58', '2026-02-12 14:53:58'),
(5, 'Windira Safitri', 'windira@wonosarigk.id', NULL, '$2y$12$3b.wmOO34rqbbj0gzraFuODiHTjWeQT2leiyWWMijFU5H48sgO2FO', NULL, '2026-02-12 14:54:17', '2026-02-12 14:54:17'),
(6, 'Tugino', 'tugino@wonosarigk.id', NULL, '$2y$12$601gCaq7fy9l8XZf5NCPKur6VPWXETXLdVleGJgmsSeFmE3nw9kAy', NULL, '2026-02-12 14:56:08', '2026-02-12 14:56:08'),
(7, 'Suindartini', 'suindartini@wonosarigk.id', NULL, '$2y$12$Gk9GrnYhEQH3WknHHRlLxOv6UJ1ZGzkLVCncQQghScA4BXHLMbPh6', NULL, '2026-02-12 14:56:27', '2026-02-12 14:56:27'),
(8, 'Suyatna', 'suyatna@wonosarigk.id', NULL, '$2y$12$Ho1ZtN2g67VzkGaDTSZyH.lsh6NkgCNEqzr4kxxdSVDnuk5RUS7fW', NULL, '2026-02-12 14:56:51', '2026-02-12 14:56:51'),
(9, 'Ubaidilah Aminuddin Thoyieb', 'ubaidilah@wonosarigk.id', NULL, '$2y$12$3yNfBg5iTtVu1ko.Bxpq6Oiyq5flyuX7MZcnIeusxasRh7rpm3pf.', NULL, '2026-02-12 14:57:08', '2026-02-12 14:57:08'),
(10, 'Ari Ismawan', 'ari@wonosarigk.id', NULL, '$2y$12$dE0TKFHHO5vBcbKdi0rXt.NFv4/TWf3kNTRrGPEYfuMJnb4kqIMlK', NULL, '2026-02-12 14:57:24', '2026-02-12 14:57:24'),
(11, 'Sularno', 'sularno@wonosarigk.id', NULL, '$2y$12$MDFij4JGsW7/XY/xptYnTO0VSqMWN3mEXZ1l9T.swc4uamFqbNska', NULL, '2026-02-12 14:58:00', '2026-02-12 14:58:00'),
(12, 'Saban Nurhuda', 'saban@wonosarigk.id', NULL, '$2y$12$orwHCrqQPcMBsiYo5.nAnu1nj79RZyx2t1mhIeCqG6MnQTrDKr8JG', NULL, '2026-02-12 14:58:21', '2026-02-12 14:58:21'),
(13, 'Wahyono', 'wahyono@wonosarigk.id', NULL, '$2y$12$.mWXmxneZow3SN4X24UrL.HMjWZmzGt1Pc6qhPVHunL6cFfKPd2fC', NULL, '2026-02-12 14:58:38', '2026-02-12 14:58:38'),
(14, 'Wijayadi', 'wijayadi@wonosarigk.id', NULL, '$2y$12$llfxCOQF2SEO02hgofr5S.G6rI4.07HZFltUaWoEze6AIGeoAjMi6', NULL, '2026-02-12 14:59:04', '2026-02-12 14:59:04');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `alternatives_decision_session_id_code_unique` (`decision_session_id`,`code`);

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
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `alternative_evaluations`
--
ALTER TABLE `alternative_evaluations`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `criteria_pairwise`
--
ALTER TABLE `criteria_pairwise`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `criteria_scoring_parameters`
--
ALTER TABLE `criteria_scoring_parameters`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `criteria_scoring_rules`
--
ALTER TABLE `criteria_scoring_rules`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `criteria_weights`
--
ALTER TABLE `criteria_weights`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `decision_sessions`
--
ALTER TABLE `decision_sessions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `decision_session_criteria`
--
ALTER TABLE `decision_session_criteria`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `decision_session_dm`
--
ALTER TABLE `decision_session_dm`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
