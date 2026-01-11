-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Jan 07, 2026 at 04:19 PM
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
-- Database: `conference_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `academic_ranks`
--

CREATE TABLE `academic_ranks` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `academic_ranks`
--

INSERT INTO `academic_ranks` (`id`, `name_fa`, `name_en`, `created_at`, `updated_at`) VALUES
(1, 'پوهاند', 'Pohand', NULL, NULL),
(2, 'پوهنوال', 'Pohanwal', NULL, NULL),
(3, 'سایر', 'Others', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `access_roles`
--

CREATE TABLE `access_roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_roles`
--

INSERT INTO `access_roles` (`id`, `role_name`, `created_at`, `updated_at`) VALUES
(1, 'Admin', '2025-10-08 04:24:21', '2025-11-26 11:53:55'),
(2, 'Author', '2025-10-08 04:25:14', '2025-11-13 02:00:37'),
(3, 'Reviewer', '2025-10-08 04:26:11', '2025-11-13 02:01:18');

-- --------------------------------------------------------

--
-- Table structure for table `access_role_user`
--

CREATE TABLE `access_role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `access_role_user`
--

INSERT INTO `access_role_user` (`id`, `user_id`, `role_id`, `created_at`, `updated_at`) VALUES
(3, 1, 1, NULL, NULL),
(4, 1, 2, NULL, NULL),
(6, 1, 3, '2025-12-01 04:31:48', '2025-12-01 04:31:48'),
(8, 16, 2, '2025-12-01 04:36:02', '2025-12-01 04:36:02'),
(12, 17, 2, '2025-12-01 06:11:00', '2025-12-01 06:11:00'),
(13, 18, 3, '2025-12-04 04:16:06', '2025-12-04 04:16:06'),
(14, 19, 3, '2025-12-04 04:16:38', '2025-12-04 04:16:38'),
(15, 20, 3, '2025-12-04 04:19:43', '2025-12-04 04:19:43'),
(16, 21, 3, '2025-12-04 04:21:55', '2025-12-04 04:21:55'),
(17, 22, 3, '2025-12-04 04:22:32', '2025-12-04 04:22:32'),
(19, 3, 2, '2025-12-04 04:23:44', '2025-12-04 04:23:44'),
(20, 23, 2, '2025-12-06 06:05:40', '2025-12-06 06:05:40'),
(21, 2, 1, '2025-12-11 06:16:08', '2025-12-11 06:16:08'),
(22, 24, 2, '2025-12-21 02:23:35', '2025-12-21 02:23:35'),
(23, 25, 2, '2025-12-21 02:32:50', '2025-12-21 02:32:50'),
(24, 26, 2, '2026-01-01 06:21:31', '2026-01-01 06:21:31'),
(25, 27, 2, '2026-01-01 06:30:28', '2026-01-01 06:30:28'),
(27, 28, 3, '2026-01-07 06:30:42', '2026-01-07 06:30:42'),
(28, 29, 3, '2026-01-07 06:32:48', '2026-01-07 06:32:48'),
(29, 30, 3, '2026-01-07 06:33:40', '2026-01-07 06:33:40'),
(30, 31, 3, '2026-01-07 06:34:57', '2026-01-07 06:34:57');

-- --------------------------------------------------------

--
-- Table structure for table `actions`
--

CREATE TABLE `actions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(32) NOT NULL,
  `name_en` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `actions`
--

INSERT INTO `actions` (`id`, `name`, `name_en`, `created_at`, `updated_at`) VALUES
(1, 'نمایش دادن', 'View', NULL, NULL),
(2, 'اضافه کردن', 'Add', NULL, NULL),
(3, 'ویرایش کردن', 'Edit', NULL, NULL),
(4, 'حذف کردن', 'Delete', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `author_types`
--

CREATE TABLE `author_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_name_fa` varchar(32) NOT NULL,
  `type_name_en` varchar(32) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `author_types`
--

INSERT INTO `author_types` (`id`, `type_name_fa`, `type_name_en`, `created_at`, `updated_at`) VALUES
(1, 'نویسنده', 'Author', NULL, NULL),
(2, 'مترجم', 'Translator', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('conference_system_cache_94d92f976fd06fd3e8cf53ec4e03d646', 'i:2;', 1767767334),
('conference_system_cache_94d92f976fd06fd3e8cf53ec4e03d646:timer', 'i:1767767334;', 1767767334);

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `countries`
--

CREATE TABLE `countries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_name_fa` varchar(64) NOT NULL,
  `country_name_en` varchar(64) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `countries`
--

INSERT INTO `countries` (`id`, `country_name_fa`, `country_name_en`, `created_at`, `updated_at`) VALUES
(1, 'افغانستان', 'Afghanistan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(2, 'ایران', 'Iran', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(3, 'پاکستان', 'Pakistan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(4, 'آرژانتین', 'Argentina', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(5, 'آرمانستان', 'Armenia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(6, 'آلبانی', 'Albania', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(7, 'آلمان', 'Germany', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(8, 'آفریقای جنوبی', 'South Africa', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(9, 'آمریکا', 'United States', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(10, 'آندورا', 'Andorra', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(11, 'آنگولا', 'Angola', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(12, 'آنتیگا و باربودا', 'Antigua and Barbuda', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(13, 'آروبا', 'Aruba', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(14, 'اتریش', 'Austria', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(15, 'اِسواتینی', 'Eswatini', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(16, 'استرالیا', 'Australia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(17, 'استونی', 'Estonia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(18, 'السالوادور', 'El Salvador', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(19, 'اکوادور', 'Ecuador', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(20, 'الجزایر', 'Algeria', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(21, 'ایتالیا', 'Italy', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(22, 'باربادوس', 'Barbados', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(23, 'بحرین', 'Bahrain', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(24, 'برزیل', 'Brazil', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(25, 'برونئی', 'Brunei', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(26, 'بلاروس', 'Belarus', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(27, 'بلژیک', 'Belgium', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(28, 'بلیز', 'Belize', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(29, 'بنگلادش', 'Bangladesh', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(30, 'بورکینافاسو', 'Burkina Faso', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(31, 'بوروندی', 'Burundi', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(32, 'بوتان', 'Bhutan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(33, 'بولیوی', 'Bolivia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(34, 'بوسنی و هرزگوین', 'Bosnia and Herzegovina', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(35, 'بوتسوانا', 'Botswana', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(36, 'پاراگوئه', 'Paraguay', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(37, 'پرو', 'Peru', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(38, 'پاپوآ گینه نو', 'Papua New Guinea', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(39, 'پاناما', 'Panama', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(40, 'تاجیکستان', 'Tajikistan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(41, 'تانزانیا', 'Tanzania', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(42, 'تایلند', 'Thailand', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(43, 'ترکیه', 'Turkey', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(44, 'ترکمنستان', 'Turkmenistan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(45, 'جیبوتی', 'Djibouti', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(46, 'جامائیکا', 'Jamaica', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(47, 'جمهوری چک', 'Czech Republic', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(48, 'چین', 'China', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(49, 'دانمارک', 'Denmark', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(50, 'رواندا', 'Rwanda', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(51, 'روسیه', 'Russia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(52, 'رومانی', 'Romania', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(53, 'زامبیا', 'Zambia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(54, 'ساحل عاج', 'Côte d’Ivoire', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(55, 'سان مارینو', 'San Marino', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(56, 'سنگاپور', 'Singapore', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(57, 'سودان', 'Sudan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(58, 'سودان جنوبی', 'South Sudan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(59, 'سوریه', 'Syria', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(60, 'سویزرلند', 'Switzerland', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(61, 'سوئد', 'Sweden', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(62, 'صربستان', 'Serbia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(63, 'عراق', 'Iraq', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(64, 'فنلاند', 'Finland', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(65, 'فرانسه', 'France', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(66, 'قرقیزستان', 'Kyrgyzstan', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(67, 'قطر', 'Qatar', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(68, 'قبرس', 'Cyprus', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(69, 'کوبا', 'Cuba', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(70, 'کانادا', 'Canada', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(71, 'کره جنوبی', 'South Korea', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(72, 'کره شمالی', 'North Korea', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(73, 'کویت', 'Kuwait', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(74, 'کنیا', 'Kenya', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(75, 'کلمبیا', 'Colombia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(76, 'گابن', 'Gabon', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(77, 'گرجستان', 'Georgia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(78, 'لهستان', 'Poland', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(79, 'لبنان', 'Lebanon', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(80, 'لیتوانی', 'Lithuania', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(81, 'لوکزامبورگ', 'Luxembourg', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(82, 'مالزی', 'Malaysia', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(83, 'مکزیک', 'Mexico', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(84, 'مصر', 'Egypt', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(85, 'موریتانی', 'Mauritania', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(86, 'مونته‌نگرو', 'Montenegro', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(87, 'نیجر', 'Niger', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(88, 'نروژ', 'Norway', '2025-12-21 06:46:20', '2025-12-21 06:46:20'),
(89, 'هائیتی', 'Haiti', '2025-12-21 06:46:20', '2025-12-21 06:46:20');

-- --------------------------------------------------------

--
-- Table structure for table `editorial_decisions`
--

CREATE TABLE `editorial_decisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `editor_id` bigint(20) UNSIGNED NOT NULL,
  `decision` enum('accept','revision_required','reject') DEFAULT NULL,
  `round` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `comments_fa` text DEFAULT NULL,
  `comments_en` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `editorial_decisions`
--

INSERT INTO `editorial_decisions` (`id`, `submission_id`, `editor_id`, `decision`, `round`, `comments_fa`, `comments_en`, `created_at`, `updated_at`) VALUES
(4, 2, 1, 'accept', 1, NULL, NULL, '2025-11-29 12:07:44', '2025-11-29 12:07:44'),
(5, 1, 1, 'accept', 1, NULL, NULL, '2025-11-29 13:09:56', '2025-11-29 13:09:56'),
(6, 8, 1, 'revision_required', 1, 'نظر به بررسی و تصمیم داوران مقاله شما باید اصلاح دوباره شود', 'نظر به بررسی و تصمیم داوران مقاله شما باید اصلاح دوباره شود', '2025-12-01 04:57:39', '2025-12-01 04:57:39'),
(7, 8, 1, 'accept', 2, NULL, NULL, '2025-12-01 04:59:57', '2025-12-01 04:59:57'),
(10, 12, 1, 'accept', 1, 'sfa', 'SSSSSSSSSSSSSS', '2025-12-07 12:37:51', '2025-12-07 12:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `education_degrees`
--

CREATE TABLE `education_degrees` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `education_degrees`
--

INSERT INTO `education_degrees` (`id`, `name_fa`, `name_en`, `created_at`, `updated_at`) VALUES
(1, 'لیسانس', 'Bachelor', NULL, NULL),
(2, 'ماستر', 'Master', NULL, NULL),
(3, 'دوکتور', 'Phd', NULL, NULL),
(4, 'سایر', 'Others', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `gazettes`
--

CREATE TABLE `gazettes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_fa` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `gazette_number` varchar(255) DEFAULT NULL,
  `publish_date` date DEFAULT NULL,
  `status` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gazettes`
--

INSERT INTO `gazettes` (`id`, `title_fa`, `title_en`, `gazette_number`, `publish_date`, `status`, `created_at`, `updated_at`) VALUES
(21, 'د جریدې پرلپسې (۱۴۳۲) ګڼه', 'د جریدې پرلپسې (۱۴۳۲) ګڼه', NULL, '2025-12-14', 1, '2025-12-14 06:39:39', '2025-12-14 11:34:34'),
(22, 'د رسمي جریدې (۱۴۲۴) ګڼه', 'د رسمي جریدې (۱۴۲۴) ګڼه', NULL, '2025-12-15', 1, '2025-12-15 01:14:46', '2025-12-15 01:14:46'),
(23, 'د رسمي جریدې (۱۴۳۴) ګڼه', 'د رسمي جریدې (۱۴۳۴) ګڼه', NULL, '2025-12-15', 1, '2025-12-15 01:23:32', '2025-12-17 06:52:57'),
(39, 'د رسمي جریدې (۱۴۳۸) ګڼه', 'د رسمي جریدې (۱۴۳۸) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:51:56', '2025-12-21 02:51:56'),
(40, 'د رسمي جریدې (۱۴۴۴) ګڼه', 'د رسمي جریدې (۱۴۴۴) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:54:24', '2025-12-21 02:54:24'),
(41, 'د رسمی جریدې (۱۴۴۷) ګڼه', 'د رسمی جریدې (۱۴۴۷) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:55:35', '2025-12-21 02:55:35'),
(42, 'د رسمی جریدې (۱۴۵۰) ګڼه', 'د رسمی جریدې (۱۴۵۰) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:56:06', '2025-12-21 02:56:06'),
(43, 'د رسمي جریدې (۱۴۵۲) ګڼه', 'د رسمي جریدې (۱۴۵۲) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:56:40', '2025-12-21 02:56:40'),
(44, 'د رسمی جریدې (۱۴۵۵) ګڼه', 'د رسمی جریدې (۱۴۵۵) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:57:46', '2025-12-21 02:57:46'),
(45, 'د رسمي جریدې (۱۴۵۸) ګڼه', 'د رسمي جریدې (۱۴۵۸) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:58:57', '2025-12-21 02:58:57'),
(46, 'د رسمي جریدې (۱۴۶۱) ګڼه', 'د رسمي جریدې (۱۴۶۱) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:59:28', '2025-12-21 02:59:28'),
(47, 'د رسمي جریدې (۱۴۶۵) ګڼه', 'د رسمي جریدې (۱۴۶۵) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 02:59:55', '2025-12-21 02:59:55'),
(48, 'د رسمي جریدې (۱۴۶۷) ګڼه', 'د رسمي جریدې (۱۴۶۷) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 03:00:23', '2025-12-21 03:00:23'),
(49, 'د رسمي جریدې (۱۴۶۹) ګڼه', 'د رسمي جریدې (۱۴۶۹) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 03:01:01', '2025-12-21 03:01:01'),
(50, 'د رسمي جریدې (۱۴۷۰) ګڼه', 'د رسمي جریدې (۱۴۷۰) ګڼه', NULL, '2025-12-21', 1, '2025-12-21 03:01:29', '2025-12-21 03:01:29');

-- --------------------------------------------------------

--
-- Table structure for table `gazette_files`
--

CREATE TABLE `gazette_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `gazette_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` text NOT NULL,
  `file_name` text DEFAULT NULL,
  `file_type` enum('pdf','docx','xlsx') DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `gazette_files`
--

INSERT INTO `gazette_files` (`id`, `gazette_id`, `file_path`, `file_name`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(49, 21, 'website/gazette-files/21/fbqT7yY5SQmQWxkqidXa8aJSXmoZnCKmVkjJGL47.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-15 01:10:14', '2025-12-20 03:22:19'),
(50, 21, 'website/gazette-files/21/JYVjZSShkWoBGKxXnQe3MjErLcaZvnVUrKIE1kUp.pdf', '۲ د محاکمو عالي اداره ته لارښوونه..pdf', 'pdf', 1295090, '2025-12-15 01:10:29', '2025-12-15 01:10:29'),
(51, 21, 'website/gazette-files/21/aSRiWPCQPB4MNYZPDA4XdQHo3ptVlHryUjiyCETj.pdf', '۳- د اسلامي امارت ټولو مجاهدینو ته لارښوونه.pdf', 'pdf', 1295090, '2025-12-15 01:10:46', '2025-12-15 01:10:46'),
(52, 21, 'website/gazette-files/21/II2PTbj7HKDZTGUx0IR1x0sTkNhgkqGCtBu3gedE.pdf', '۵- د امربالمعروف او نهې عن المنکر له ادراسه سره د همکارۍ په هکله فرمان.pdf', 'pdf', 1295090, '2025-12-15 01:11:02', '2025-12-15 01:11:02'),
(54, 22, 'website/gazette-files/22/MBJA8Nfa6iEdfzC2VgQuaBDJWCZn2iDRmB2bg7s6.pdf', 'د ځمکو د غصب د مخنیوي او غصب شویو ځمکو د استرداد مقرره.pdf', 'pdf', 1641829, '2025-12-15 01:14:46', '2025-12-15 01:14:46'),
(55, 22, 'website/gazette-files/22/4abBbtgZrVCvIK0t9pqPiZ1ZQGC9NUPRRqDaf1CZ.pdf', 'د نادرکه شویو مجاهدینو او نورو کسانو په هکله د عالیقدر امیرالمؤمنین حفظه الله.pdf', 'pdf', 1641829, '2025-12-15 01:14:46', '2025-12-15 01:14:46'),
(56, 23, 'website/gazette-files/23/vK62xLeGeKFrm6uaRAFXT52QyTvpOXb3zPcLuMMF.pdf', 'په وزارتونو او امارتي ادارو کې د مراجعینو لپاره د آسانتیا د برابرولو په هکله فرمان .pdf', 'pdf', 1495877, '2025-12-15 01:23:32', '2025-12-15 03:04:16'),
(61, 23, 'website/gazette-files/23/C75H54vSmqGkhS0GcXPAZ8NVfo9W7XBCS7EgUcWl.pdf', 'د تقنیني سندونو د نهایي کتنې مس تقل کمېسیون د ایجاد په هکله فرمان .pdf', 'pdf', 1495877, '2025-12-15 03:04:16', '2025-12-15 03:04:16'),
(62, 21, 'website/gazette-files/21/lZWMbA5KWEOXZ2Z4RVGpwI0PjEPx59ijgrofzzz6.pdf', '۶- د دستوري اشخاصو د مخنیوي په هکله فرمان.pdf', 'pdf', 1295090, '2025-12-18 04:57:18', '2025-12-18 04:57:18'),
(63, 23, 'website/gazette-files/23/EHl3F0Fq3mVvdYGIUieL96Vb3bMLX5vjsGehkgxy.pdf', '۲ د محاکمو عالي اداره ته لارښوونه..pdf', 'pdf', 1295090, '2025-12-20 00:43:41', '2025-12-20 00:43:41'),
(64, 23, 'website/gazette-files/23/sVJO75Wme1ah43yhB8S5rqpRmqlbv4pOOBCKZUor.pdf', '۸- په مطبوعاتو ، مخابراتو او لیکنو کې د سپکو سپورو د نه ویلو په هکله فرمان.pdf', 'pdf', 1295090, '2025-12-20 01:02:02', '2025-12-20 01:02:02'),
(70, 21, 'website/gazette-files/21/aao9a91LtRXHjZ0WhplxvRr97TSmpPqQ9y3uFDUN.pdf', '۱۹- د عمومي محاذونو د لغو کېدلو په هکله فرمان .pdf', 'pdf', 1295090, '2025-12-20 03:22:37', '2025-12-20 03:22:37'),
(72, 21, 'website/gazette-files/21/OXl0ETf1Iy5oRMrtUJ2VCApFtfi5XR6awUPNSJHE.pdf', '۱۰- له درې ګونو محکمو وروسته د حدودو او قصاص....pdf', 'pdf', 1295090, '2025-12-20 03:23:28', '2025-12-20 03:23:28'),
(73, 21, 'website/gazette-files/21/zIoeL9ed9AWDUxwjMKvd3jFAxfhbnhRIzZ6Eno8b.pdf', '۲۵- د ریاست الوزراء د معاونیتونو پورې اړوند.......pdf', 'pdf', 1295090, '2025-12-20 03:25:28', '2025-12-20 03:25:28'),
(75, 39, 'website/gazette-files/39/hPt1uuU7Hib3Lz8PGYN5MZmpcRqCP2bh76dKZrd0.pdf', 'د ځمکو له غصب څخه د مخنیوي او د غصب شویو ځمکو د استرداد قانون.pdf', 'pdf', 2735156, '2025-12-21 02:51:56', '2025-12-21 02:51:56'),
(76, 39, 'website/gazette-files/39/uWpzlzpWikaN02vO0EpTDJ5MdhirwJdbpuMZ6uiu.pdf', 'د څو قطعو امارتي غصب شویو ځمکو د ا جارې په هکله....pdf', 'pdf', 2735156, '2025-12-21 02:53:14', '2025-12-21 02:53:14'),
(77, 40, 'website/gazette-files/40/RjsFQmEN3iujS658rTjm4k5LCl8qAioXB9iUsSbQ.pdf', 'د ځمکو له غصب څخه د مخنیوي او د غصب شویو ځمکو د استرداد قانون.pdf', 'pdf', 2735156, '2025-12-21 02:54:24', '2025-12-21 02:54:24'),
(78, 40, 'website/gazette-files/40/3P91iwRiE8ObUOHebnOQisnYEeWVRXhF3RUQNOqJ.pdf', 'د څو قطعو امارتي غصب شویو ځمکو د ا جارې په هکله....pdf', 'pdf', 2735156, '2025-12-21 02:54:24', '2025-12-21 02:54:24'),
(79, 41, 'website/gazette-files/41/D1f63BO2f7AW3a0ZTHDiwTovYUA2L4ECEsdBWouk.pdf', 'په راڼه ډول د امارتي عوایدو د راټولولو او په لګښتونو کې د سپما په هکله.pdf', 'pdf', 1609988, '2025-12-21 02:55:35', '2025-12-21 02:55:35'),
(80, 41, 'website/gazette-files/41/ETGgxuyCxHj7wRJ2bGilisFwjimQWukk6XWeXEts.pdf', 'د سوالګرو د راټولولو او له سوالګرۍ څخه د مخنیوي قانون.pdf', 'pdf', 1609988, '2025-12-21 02:55:35', '2025-12-21 02:55:35'),
(81, 41, 'website/gazette-files/41/cKMQlE7kFDt7w6L8OcdXa4jhQljJs57aAmVb3Lc8.pdf', 'له دوکاندارانو  (اصنافو) څخه د لوحې د فیس راټولولو د ممنوعی په هکله.pdf', 'pdf', 1609988, '2025-12-21 02:55:35', '2025-12-21 02:55:35'),
(82, 42, 'website/gazette-files/42/O5QJyywJpsTS0EBhVqAnRIsao8mqLN9a9bA4DwW0.pdf', 'د اصنافو (دوکاندارانو او کسبکارانو) د مالیې د تخفیف په هکله د عالیقدر.pdf', 'pdf', 1390302, '2025-12-21 02:56:06', '2025-12-21 02:56:06'),
(83, 42, 'website/gazette-files/42/bm3eCQlMba2UrfxJhh7jhAFsiqqNhUguRuy36ihb.pdf', 'د صرافانو او پولي خدمتونو د څانګې قانون.pdf', 'pdf', 1390302, '2025-12-21 02:56:06', '2025-12-21 02:56:06'),
(84, 43, 'website/gazette-files/43/kjLAsizSalbvcVThj0kNdMxq7cNa3kXOb4PAO3vt.pdf', 'د امربالمعروف او نهې عن المنکر قانون - Copy.pdf', 'pdf', 2448846, '2025-12-21 02:56:40', '2025-12-21 02:56:40'),
(85, 43, 'website/gazette-files/43/IL5x9ycIGaXQPI2eJ5tBHP9im14O2qDJa0FPQjvW.pdf', 'د لمانځه د قامې په هکله فرمان.pdf', 'pdf', 2448846, '2025-12-21 02:56:40', '2025-12-21 02:56:40'),
(86, 44, 'website/gazette-files/44/4vvFBpjqaekjUu8ynH7c1t6GrRQtnI2f5u1V4fCr.pdf', 'د صنعي ساحو قانون.pdf', 'pdf', 3045159, '2025-12-21 02:57:46', '2025-12-21 02:57:46'),
(87, 44, 'website/gazette-files/44/KTcrePeWcz8TfMNLRcXKCaON7XATB5R1ympJkH7Q.pdf', 'د محاکمو د فیصلو په هکله فرمان.pdf', 'pdf', 3045159, '2025-12-21 02:57:46', '2025-12-21 02:57:46'),
(88, 44, 'website/gazette-files/44/STl7bVfqVEP84xeWYMDI41tTyViBQDQls0qHLBfx.pdf', 'د نظام اړوند امورو د نشر په هکله فرمان.pdf', 'pdf', 3045159, '2025-12-21 02:57:46', '2025-12-21 02:57:46'),
(89, 44, 'website/gazette-files/44/v9O86fbOv0xo7S3gwi46sgRrU2r4o5kmIQXkkuu1.pdf', 'له غاصبینو څخه د استرداد شوو امارتي ځمکو د پلورنې د منعې په هکله فرمان.pdf', 'pdf', 3045159, '2025-12-21 02:57:46', '2025-12-21 02:57:46'),
(90, 45, 'website/gazette-files/45/hDeXPDGf8kOCw03gemJCSmN1mlxs5jJXuS6wN5OH.pdf', 'د امارتي ځمکو د اجارې قانون.pdf', 'pdf', 2805932, '2025-12-21 02:58:57', '2025-12-21 02:58:57'),
(91, 45, 'website/gazette-files/45/INZdkBaV5sqy92ZEjpkuYLHHKjeVCsSES3LQig0I.pdf', 'راستنېدونکو مهاجرینو ته د ځمکو د وېش د هیئت طرزالعمل .pdf', 'pdf', 2805932, '2025-12-21 02:58:57', '2025-12-21 02:58:57'),
(92, 46, 'website/gazette-files/46/zf4ewJHMwkp2r7Q1GAWm9b0IofSVvSBbG5tZHQDK.pdf', 'د انساني قاچاق د مخنیوي په هکله فرمان.pdf', 'pdf', 2114307, '2025-12-21 02:59:28', '2025-12-21 02:59:28'),
(93, 46, 'website/gazette-files/46/8aO3u0mu4YeGSoHRvK3bt40D95fz6OOWvuj9yU9L.pdf', 'د بزګرانو د اضافي غنمو د پیرلو طرزالعمل.pdf', 'pdf', 2114307, '2025-12-21 02:59:28', '2025-12-21 02:59:28'),
(94, 46, 'website/gazette-files/46/ZX56l8foJrkTtJBQ8SnHCA2UJtdC3MOXb54J6u8t.pdf', 'د تقاعد دحقوقو په هکله فرمان.pdf', 'pdf', 2114307, '2025-12-21 02:59:28', '2025-12-21 02:59:28'),
(95, 46, 'website/gazette-files/46/AyiklbynFdTI1ZUYePMFVAZn2TOQBJjs3CxxYChS.pdf', 'د مدرسو د تاسیس، تثبیت، د استاذانو او کارکوونکو د تقرر قانون.pdf', 'pdf', 2114307, '2025-12-21 02:59:28', '2025-12-21 02:59:28'),
(96, 47, 'website/gazette-files/47/jNu3XmWzyuw5rFbRqAslLZmVDS06Gtk8OlMREYKW.pdf', 'د ماراتي شرکتونو د چارو د تنظیم په هکله فرمان.pdf', 'pdf', 1812956, '2025-12-21 02:59:55', '2025-12-21 02:59:55'),
(97, 47, 'website/gazette-files/47/LOkGUNX4176WABtaE9T6SqVl7UAKPVKYR59ERkoM.pdf', 'د مالي محاسبې قانون،.pdf', 'pdf', 1812956, '2025-12-21 02:59:55', '2025-12-21 02:59:55'),
(98, 48, 'website/gazette-files/48/1mCSKnbhR97sd8GtgcOl1y1WyKF2Q9e9ptw86bZH.pdf', 'د حاجیانو د معلیمنو قانون - Copy.pdf', 'pdf', 1660893, '2025-12-21 03:00:23', '2025-12-21 03:00:23'),
(99, 49, 'website/gazette-files/49/KcAj5isx5fKuCSqcLoD9Wa52fKKEqvMtIDUVzEEx.pdf', 'د مشاعرو د تنظیم قانون.pdf', 'pdf', 1529043, '2025-12-21 03:01:01', '2025-12-21 03:01:01'),
(100, 50, 'website/gazette-files/50/Kjx6aOJI9cB2t7tpAbfN3ewBBP81iczQ3sCqGKHd.pdf', 'د ښاري طرحو  (پلانونو) اړوند د لازمو معلوماتو او تفصیلاتو د خپرولو په هکله فرمان.pdf', 'pdf', 1653803, '2025-12-21 03:01:29', '2025-12-21 03:01:29'),
(101, 50, 'website/gazette-files/50/cquGcDTaQDyOnZfvBTj65sCwqbZGAjOitLq6trvD.pdf', 'د وزارتونو او امارتي ادارو د عقدونو قانون.pdf', 'pdf', 1653803, '2025-12-21 03:01:29', '2025-12-21 03:01:29'),
(102, 41, 'website/gazette-files/41/7q7o39IsaTFAMW27VEgMyBRQ2DxdyW9xJgjglrMo.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 10:25:21', '2025-12-22 10:25:21'),
(103, 41, 'website/gazette-files/41/0w3epgsMMxJP5yT7KDtAw6R4olDV4mwvPgcvzMch.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 10:26:30', '2025-12-22 10:26:30'),
(104, 41, 'website/gazette-files/41/UihBGSQefHcFshHPOIaUvMPwFfrhhRBNXH2BO6FQ.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:26:27', '2025-12-22 23:26:27'),
(105, 41, 'website/gazette-files/41/P1gAPq9xEVKGiuipto3Sj3AbJVQwIXnvPFz9UHxx.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:31:47', '2025-12-22 23:31:47'),
(106, 41, 'website/gazette-files/41/m0BeLvzzxCwt9NM5h5LUhpYTGwNqQOvcQKnDRkWs.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:38:51', '2025-12-22 23:38:51'),
(107, 41, 'website/gazette-files/41/c9CGX71yn514SjMZIxnDOfQxteUKC8MuGkxUs62N.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:40:46', '2025-12-22 23:40:46'),
(108, 41, 'website/gazette-files/41/HL3LtZKLRJjN55yPM0qVBYv7On4H45CyIbugpsJK.pdf', '۲ د محاکمو عالي اداره ته لارښوونه..pdf', 'pdf', 1295090, '2025-12-22 23:40:58', '2025-12-22 23:40:58'),
(109, 41, 'website/gazette-files/41/TnjeqYzqV25uxQmrqdgxUpX1gs4zVdftNwN9Cvoe.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:43:03', '2025-12-22 23:43:03'),
(110, 41, 'website/gazette-files/41/1Asv8kKLU4D4VtyZdhjFSNPqeHffs7ysxKVX2xzs.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:43:29', '2025-12-22 23:43:29'),
(111, 41, 'website/gazette-files/41/PqpmtIHlmO08hfpWd9II3XuLGuEVA8dX7Tm7Iryz.pdf', '۱- د جنګي اسیرانو په اړه حکم.pdf', 'pdf', 1295090, '2025-12-22 23:43:45', '2025-12-22 23:43:45');

-- --------------------------------------------------------

--
-- Table structure for table `indexes`
--

CREATE TABLE `indexes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(64) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `indexes`
--

INSERT INTO `indexes` (`id`, `name`, `url`, `image`, `created_at`, `updated_at`) VALUES
(3, 'گوگل اسکالر', NULL, 'website/indexes/3/HQNkVAxbXEHyhMLSOBw3IKRPBAKhEfY3ezGXoKWt.jpg', '2025-12-03 04:29:25', '2025-12-15 02:26:43'),
(4, 'دسترسی آزاد', NULL, 'website/indexes/4/JklRs8HK1SLu1IyyI9hcY6hEFbXZupDuceIm4Y0h.jpg', '2025-12-03 06:13:01', '2025-12-15 02:26:22'),
(5, 'ROAD', NULL, 'website/indexes/5/XLYuBLOSOsgnmbzWtyabaBOfcD5Ref3KWvQfeDlj.jpg', '2025-12-03 06:13:21', '2025-12-15 02:26:08'),
(6, 'ISSN', NULL, 'website/indexes/6/f9fZSMEy5WSMjTdgvMB3lHMguW5vWlxGbgFGGsGX.png', '2025-12-03 06:13:43', '2025-12-15 02:25:45');

-- --------------------------------------------------------

--
-- Table structure for table `issues`
--

CREATE TABLE `issues` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `volume` varchar(255) DEFAULT NULL,
  `number` varchar(255) DEFAULT NULL,
  `title_fa` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `status` enum('published','unpublished') NOT NULL DEFAULT 'unpublished',
  `published_at` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `issues`
--

INSERT INTO `issues` (`id`, `volume`, `number`, `title_fa`, `title_en`, `cover_image`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(2, '3', '2', 'سال سوم, شماره دوم, خزان و زمستان 1403', 'Valume 3, Issue 2, Aumtumn 2024 & Winter 2025', 'issues/2/FHkCQ5JTVkfsPLB059qvRu2xoSn6p3hez2KizcBP.jpg', 'published', '2025-11-26', '2025-11-26 07:17:49', '2025-12-03 00:02:43');

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `keywords`
--

CREATE TABLE `keywords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `keyword_fa` varchar(255) DEFAULT NULL,
  `keyword_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `keywords`
--

INSERT INTO `keywords` (`id`, `keyword_fa`, `keyword_en`, `created_at`, `updated_at`) VALUES
(1, NULL, 'H', '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(2, NULL, 'M', '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(3, NULL, 'K', '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(4, NULL, 'C', '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(5, 'نوسانات', NULL, '2025-11-26 05:36:42', '2025-11-26 05:36:42'),
(6, 'نرخ', NULL, '2025-11-26 05:36:42', '2025-11-26 05:36:42'),
(7, 'نرخ ارز', NULL, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(8, 'رشد اقتصادی', NULL, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(9, 'صادرات', NULL, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(10, NULL, 'C32', '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(11, NULL, 'E52', '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(12, NULL, 'F43', '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(13, 'df', NULL, '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(14, NULL, 'df', '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(15, NULL, 'dsf', '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(16, 'رده شناختی', NULL, '2025-12-01 04:43:26', '2025-12-01 04:43:26'),
(17, 'نظام', NULL, '2025-12-01 04:43:26', '2025-12-01 04:43:26'),
(18, 'حواله', NULL, '2025-12-01 06:12:34', '2025-12-01 06:12:34'),
(19, 'تورم', NULL, '2025-12-01 06:12:34', '2025-12-01 06:12:34'),
(20, 'توسعه', NULL, '2025-12-01 06:12:34', '2025-12-01 06:12:34'),
(21, 'هدف', NULL, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(22, 'پژوهش حاضر', NULL, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(23, 'دسته بندی', NULL, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(24, 'یادگیری', NULL, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(25, 'اشعار فارسی', NULL, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(26, 'تلمیح', NULL, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(27, 'ماشین', NULL, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(28, 'عملکرد', NULL, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(29, 'اثرات', NULL, '2025-12-16 23:48:24', '2025-12-16 23:48:24'),
(30, 'زبان ترکی', NULL, '2025-12-16 23:49:01', '2025-12-16 23:49:01'),
(31, 'زبان آذری', NULL, '2025-12-16 23:49:01', '2025-12-16 23:49:01'),
(32, 'ewrew', NULL, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(33, 'erew', NULL, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(34, 'ewqr', NULL, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(35, 'سلام', NULL, '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(36, 'علیکم', NULL, '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(37, 'چطور', NULL, '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(38, 'استی', NULL, '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(39, NULL, 'hellow', '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(40, NULL, 'Hi', '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(41, NULL, 'Hii', '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(42, NULL, 'hidds', '2025-12-31 12:45:11', '2025-12-31 12:45:11'),
(43, 'فناوری', NULL, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(44, 'عصر', NULL, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(45, 'بهبود', NULL, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(46, NULL, 'A', '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(47, NULL, 'B', '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(48, NULL, 'D', '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(49, 'زندگی', NULL, '2026-01-05 02:08:50', '2026-01-05 02:08:50'),
(50, 'آموزش', NULL, '2026-01-05 02:08:50', '2026-01-05 02:08:50'),
(51, NULL, 'B]', '2026-01-05 02:08:50', '2026-01-05 02:08:50'),
(52, 'هوشمند', NULL, '2026-01-05 02:14:19', '2026-01-05 02:14:19'),
(53, 'آموزشی', NULL, '2026-01-05 02:14:19', '2026-01-05 02:14:19');

-- --------------------------------------------------------

--
-- Table structure for table `leadership_board`
--

CREATE TABLE `leadership_board` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_fa` varchar(255) NOT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `content_fa` varchar(255) DEFAULT NULL,
  `content_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `leadership_board`
--

INSERT INTO `leadership_board` (`id`, `title_fa`, `title_en`, `content_fa`, `content_en`, `created_at`, `updated_at`) VALUES
(2, 'رئیس بورد', 'د بورد مشر', 'شیخ مولوي ندامحمد ندیم', 'شیخ مولوي ندامحمد ندیم', '2025-12-11 00:51:59', '2025-12-11 01:15:56'),
(3, 'معاون', 'مرستیال', 'دکتور حمیدالله مزمل', 'دکتور حمیدالله مزمل', '2025-12-11 00:52:33', '2025-12-13 07:13:17'),
(4, 'عضو', 'غړی', 'شیخ مولوی محمد عابد حقانی', 'شیخ مولوي محمد عابد حقاني', '2025-12-11 00:52:59', '2025-12-13 07:14:44'),
(5, 'عضو', 'غړی', 'عبدالباری خپلواک', 'عبدالباري خپلواک', '2025-12-11 00:53:26', '2025-12-13 07:16:04'),
(6, 'عضو', 'غړی', 'دکتور عنایت الرحمن مایار', 'دکتور عنایت الرحمن مایار', '2025-12-13 07:16:42', '2025-12-13 07:16:42'),
(7, 'عضو', 'غړی', 'انجنیر ضیاءالرحمن مولوی زاده', 'انجنیر ضیاءالرحمن مولوي زاده', '2025-12-13 07:17:30', '2025-12-13 07:17:30'),
(8, 'برگزار کننده', 'جوړوونکی', 'پوهنتون وردگ', 'وردګ پوهنتون', '2025-12-13 07:18:17', '2025-12-13 07:21:13');

-- --------------------------------------------------------

--
-- Table structure for table `menus`
--

CREATE TABLE `menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(128) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `name_pa` varchar(255) DEFAULT NULL,
  `url` varchar(255) DEFAULT NULL,
  `icon` varchar(255) DEFAULT NULL,
  `category` tinyint(4) NOT NULL COMMENT '1=no operation, 2=has operation',
  `status` tinyint(4) NOT NULL COMMENT '1=activ, 0=inactive',
  `order` mediumint(9) DEFAULT NULL,
  `parent_id` mediumint(9) DEFAULT NULL,
  `grand_parent_id` mediumint(9) DEFAULT NULL,
  `type_id` smallint(6) NOT NULL,
  `section_id` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menus`
--

INSERT INTO `menus` (`id`, `name_fa`, `name_en`, `name_pa`, `url`, `icon`, `category`, `status`, `order`, `parent_id`, `grand_parent_id`, `type_id`, `section_id`, `created_at`, `updated_at`) VALUES
(1, 'داشبورد', 'Dashboard', 'ډشبورډ', 'dashboard', 'menu-icon tf-icons bx bx-home-circle', 1, 1, 1, NULL, NULL, 1, 1, '2025-10-18 00:27:42', '2026-01-04 23:37:02'),
(2, 'تنظیمات', 'Settings', 'تنظیمات', NULL, 'menu-icon tf-icons bx bx-user-check', 1, 1, 100, NULL, NULL, 1, 2, '2025-10-18 00:28:17', '2026-01-04 23:38:27'),
(3, 'سطح دسترسی', 'Access Role', 'د لاسرسي کچه', 'access-roles', NULL, 2, 0, 1, 2, NULL, 2, 2, '2025-10-18 00:29:51', '2026-01-04 23:38:57'),
(4, 'منوها', 'Menus', 'مېنوګانې', 'menus', NULL, 2, 1, 2, 2, NULL, 2, 2, '2025-10-18 00:36:12', '2026-01-04 23:39:07'),
(5, 'صلاحیت ها', 'Permissions', 'صلاحیتونه', 'permissions', NULL, 1, 1, 3, 2, NULL, 2, 2, '2025-10-18 00:36:56', '2026-01-04 23:39:30'),
(6, 'کاربران', 'Users', 'کاروونکي', 'users', NULL, 2, 1, 4, 2, NULL, 2, 2, '2025-10-18 00:37:30', '2026-01-04 23:39:47'),
(7, 'ارسال‌ها', 'Submissions', 'لیږل شوي', NULL, 'menu-icon tf-icons bx bx-detail', 1, 1, 2, NULL, NULL, 1, 3, '2025-10-22 01:56:45', '2026-01-05 05:39:58'),
(8, 'ارسال‌مقاله', 'Make a submission', 'مقاله لېږل', 'make-submission', NULL, 2, 1, 1, 7, NULL, 2, 3, '2025-10-22 01:59:09', '2026-01-04 23:40:10'),
(9, 'لیست ارسال‌ها', 'Submission List', 'د ارسالونو لېست', 'submission-list', NULL, 2, 1, 2, 7, NULL, 2, 3, '2025-11-04 23:58:44', '2026-01-05 05:43:21'),
(11, 'داوری‌های من', 'My Assignmenst', 'زما ارزونې', NULL, 'menu-icon tf-icons bx bx-food-menu', 1, 1, 3, NULL, NULL, 1, 3, '2025-11-13 02:06:08', '2026-01-04 23:38:03'),
(12, 'لیست مقالات', 'Article List', 'د مقالو لېست', 'assignments.reviewer.all', NULL, 2, 1, 1, 11, NULL, 2, 3, '2025-11-13 02:07:24', '2026-01-04 23:40:46'),
(13, 'تکمیل شده', 'Completed', 'بشپړ شوی', NULL, NULL, 2, 0, 2, 11, NULL, 2, 3, '2025-11-13 02:08:47', '2026-01-04 23:41:04'),
(14, 'رد شده', 'Rejected', 'رد شوی', NULL, NULL, 2, 0, 3, 11, NULL, 2, 3, '2025-11-13 02:10:04', '2026-01-04 23:41:23'),
(15, 'دوره‌های انتشار', 'Issues', 'د خپرونو دورې', 'issue-list', 'menu-icon tf-icons bx bx-calendar', 2, 0, 1, NULL, NULL, 1, 3, '2025-11-23 02:22:19', '2026-01-04 23:37:18'),
(16, 'وبسایت', 'Website', 'وېبپاڼه', NULL, 'menu-icon tf-icons bx bx-globe', 1, 1, 4, NULL, NULL, 1, 4, '2025-11-25 01:15:04', '2026-01-04 23:38:14'),
(17, 'صفحات', 'Pages', 'پاڼې', 'web-page-list', NULL, 2, 1, 1, 16, NULL, 2, 4, '2025-11-25 01:16:54', '2026-01-04 23:41:34'),
(18, 'منو', 'Menu', 'مېنو', 'web-menu-list', NULL, 2, 1, 2, 16, NULL, 2, 4, '2025-11-26 01:49:29', '2026-01-04 23:41:47'),
(19, 'حساب من', 'My Account', 'زما حساب', 'my-account', 'menu-icon tf-icons bx bx-user', 2, 1, 200, NULL, NULL, 1, 2, '2025-12-01 01:41:04', '2026-01-04 23:38:43'),
(20, 'نمایه‌ها', 'Indexes', 'فهرستونه', 'index-list', NULL, 2, 1, 3, 16, NULL, 2, 4, '2025-12-03 01:07:57', '2026-01-04 23:42:03'),
(21, 'اخبار', 'News', 'خبرونه', 'post-list', NULL, 2, 1, 4, 16, NULL, 2, 4, '2025-12-03 02:56:52', '2026-01-04 23:42:19'),
(22, 'بورد رهبری', 'Leadership Board', 'رهبری بورد', 'leadership-board', NULL, 2, 1, 5, 16, NULL, 2, 1, '2025-12-11 00:19:20', '2026-01-04 23:42:49'),
(23, 'جریده', 'Gazattes', 'جریده', 'gazettes', NULL, 2, 1, 6, 16, NULL, 2, 4, '2025-12-14 01:14:21', '2026-01-04 23:43:06'),
(24, 'بورد علمی', 'Scientific Board', 'علمي بورد', 'sientific-board', NULL, 2, 1, 7, 16, NULL, 2, 4, '2025-12-21 05:15:22', '2026-01-04 23:44:05');

-- --------------------------------------------------------

--
-- Table structure for table `menu_sections`
--

CREATE TABLE `menu_sections` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `section_name_fa` varchar(128) NOT NULL,
  `section_name_pa` varchar(128) NOT NULL,
  `order` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `section_name_en` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_sections`
--

INSERT INTO `menu_sections` (`id`, `section_name_fa`, `section_name_pa`, `order`, `created_at`, `updated_at`, `section_name_en`) VALUES
(1, 'بخش عمومی', 'عمومي برخه', 1, NULL, NULL, 'General Section'),
(2, 'بخش تنظیمات', 'د تنظیماتو برخه', 4, NULL, NULL, 'Settings Section'),
(3, 'بخش مقالات', 'د مقالو برخه', 2, NULL, NULL, 'Submissions Section'),
(4, 'بخش وبسایت', 'د وېبپاڼې برخه', 3, NULL, NULL, 'Website Section');

-- --------------------------------------------------------

--
-- Table structure for table `menu_types`
--

CREATE TABLE `menu_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_name_fa` varchar(128) NOT NULL,
  `type_name_en` varchar(128) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `type_name_pa` varchar(128) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_types`
--

INSERT INTO `menu_types` (`id`, `type_name_fa`, `type_name_en`, `created_at`, `updated_at`, `type_name_pa`) VALUES
(1, 'منوی اصلی', 'Main Menu', NULL, NULL, 'اصلي مېنو'),
(2, 'منوی فرعی', 'Sub Menu', NULL, NULL, 'فرعي مېنو'),
(3, 'زیرمنوی فرعی', 'Sub Menu Sub', NULL, NULL, 'فرعي فرعي مېنو');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2025_09_02_075243_add_two_factor_columns_to_users_table', 1),
(6, '2025_10_08_043613_create_access_roles_table', 2),
(8, '2025_10_13_034426_create_menus_table', 3),
(10, '2025_10_13_040849_create_menu_types_table', 4),
(11, '2025_10_13_071313_create_menu_sections_table', 5),
(13, '2025_10_16_104640_create_system_logs_table', 6),
(14, '2025_10_16_110209_create_permissions_table', 7),
(15, '2025_10_16_111052_create_actions_table', 8),
(179, '2025_10_22_051240_create_countries_table', 8),
(180, '2025_10_22_051241_create_author_types_table', 8),
(183, '2025_10_22_051753_create_submission_files_table', 9),
(184, '2025_10_22_051902_create_reviews_table', 9),
(188, '2025_10_22_052436_create_publications_table', 9),
(206, '2025_10_22_052207_create_editorial_decisions_table', 10),
(207, '2025_10_22_052322_create_review_decisions_table', 10),
(208, '2025_10_22_052529_create_notifications_table', 10),
(209, '2025_10_22_052650_create_submission_logs_table', 10),
(210, '2025_10_22_052821_create_review_invites_table', 10),
(211, '2025_10_22_052922_create_submission_tags_table', 10),
(212, '2025_10_22_060356_create_keywords_table', 10),
(213, '2025_10_22_060357_create_submission_keyword_table', 10),
(214, '2025_11_23_062926_create_issues_table', 10),
(215, '2025_10_22_052042_create_review_files_table', 11),
(216, '2025_10_22_051241_create_submissions_table', 12),
(221, '2025_11_25_050110_create_web_pages_table', 13),
(222, '2025_11_26_060215_create_web_menus_table', 13),
(227, '2025_11_26_130403_create_access_role_user_table', 14),
(232, '2025_12_03_052241_create_indexes_table', 15),
(253, '2025_11_30_102020_add_fields_to_users_table', 17),
(254, '2025_12_03_065605_create_posts_table', 17),
(255, '2025_12_06_072513_create_education_degrees_table', 17),
(256, '2025_12_06_072543_create_academic_ranks_table', 17),
(257, '2025_12_06_100021_create_provinces_table', 18),
(258, '2025_10_22_051651_create_submission_authors_table', 19),
(259, '2025_12_11_042826_create_leadership_board_table', 20),
(264, '2025_12_14_045240_create_gazattes_table', 21),
(265, '2025_12_17_050844_create_submission_views_table', 22),
(266, '2025_12_20_110351_create_web_page_files', 23),
(267, '2025_12_21_091044_create_scientific_board_table', 24),
(268, '2026_01_03_043337_add_name_pa_to_menus_table', 25),
(269, '2026_01_03_043914_add_section_name_pa_to_menu_sections_table', 26),
(271, '2026_01_04_110339_add_type_name_pa_to_menu_types_table', 27),
(272, '2026_01_05_071438_add_soft_delete_to_reviews_table', 28);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `message_fa` text DEFAULT NULL,
  `message_en` text DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `password_reset_tokens`
--

INSERT INTO `password_reset_tokens` (`email`, `token`, `created_at`) VALUES
('zakinazari10@gmail.com', '$2y$12$YcM9027DtNjw6lJsi6HnuuvN5s3Sl6NaHXAOUdk6GMvc1IKz92A2K', '2025-11-12 10:53:29');

-- --------------------------------------------------------

--
-- Table structure for table `permissions`
--

CREATE TABLE `permissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_id` int(11) NOT NULL,
  `role_id` int(11) NOT NULL,
  `action_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `permissions`
--

INSERT INTO `permissions` (`id`, `menu_id`, `role_id`, `action_id`, `created_at`, `updated_at`) VALUES
(33, 2, 3, 3, '2025-10-19 01:45:29', '2025-10-19 01:45:29'),
(34, 1, 1, 1, '2025-10-19 01:53:02', '2025-10-19 01:53:02'),
(35, 2, 1, 3, '2025-10-19 01:53:08', '2025-10-19 01:53:08'),
(37, 5, 1, 1, '2025-10-20 01:46:39', '2025-10-20 01:46:39'),
(38, 4, 1, 1, '2025-10-20 02:06:07', '2025-10-20 02:06:07'),
(39, 4, 1, 2, '2025-10-20 02:06:07', '2025-10-20 02:06:07'),
(40, 4, 1, 3, '2025-10-20 02:06:07', '2025-10-20 02:06:07'),
(41, 4, 1, 4, '2025-10-20 02:06:07', '2025-10-20 02:06:07'),
(46, 2, 1, 1, '2025-10-20 02:46:43', '2025-10-20 02:46:43'),
(47, 2, 1, 2, '2025-10-20 02:46:43', '2025-10-20 02:46:43'),
(48, 2, 1, 4, '2025-10-20 02:46:43', '2025-10-20 02:46:43'),
(73, 11, 3, 1, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(74, 11, 3, 2, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(75, 11, 3, 3, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(76, 11, 3, 4, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(77, 12, 3, 1, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(78, 12, 3, 2, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(79, 12, 3, 3, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(80, 12, 3, 4, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(81, 13, 3, 1, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(82, 13, 3, 2, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(83, 13, 3, 3, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(84, 13, 3, 4, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(85, 14, 3, 1, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(86, 14, 3, 2, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(87, 14, 3, 3, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(88, 14, 3, 4, '2025-11-13 02:11:47', '2025-11-13 02:11:47'),
(89, 1, 3, 1, '2025-11-13 02:11:53', '2025-11-13 02:11:53'),
(90, 1, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(91, 1, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(92, 1, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(93, 11, 1, 1, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(94, 11, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(95, 11, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(96, 11, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(97, 5, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(98, 5, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(99, 5, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(104, 12, 1, 1, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(105, 12, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(106, 12, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(107, 12, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(108, 13, 1, 1, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(109, 13, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(110, 13, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(111, 13, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(112, 14, 1, 1, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(113, 14, 1, 2, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(114, 14, 1, 3, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(115, 14, 1, 4, '2025-11-16 04:26:12', '2025-11-16 04:26:12'),
(116, 15, 1, 1, '2025-11-23 02:26:34', '2025-11-23 02:26:34'),
(117, 15, 1, 2, '2025-11-23 03:11:03', '2025-11-23 03:11:03'),
(118, 15, 1, 3, '2025-11-23 03:11:03', '2025-11-23 03:11:03'),
(119, 15, 1, 4, '2025-11-23 03:11:03', '2025-11-23 03:11:03'),
(120, 16, 1, 1, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(121, 16, 1, 2, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(122, 16, 1, 3, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(123, 16, 1, 4, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(124, 17, 1, 1, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(125, 17, 1, 2, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(126, 17, 1, 3, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(127, 17, 1, 4, '2025-11-25 01:26:58', '2025-11-25 01:26:58'),
(128, 18, 1, 1, '2025-11-26 01:51:48', '2025-11-26 01:51:48'),
(129, 18, 1, 2, '2025-11-26 01:51:48', '2025-11-26 01:51:48'),
(130, 18, 1, 3, '2025-11-26 01:51:48', '2025-11-26 01:51:48'),
(131, 18, 1, 4, '2025-11-26 01:51:48', '2025-11-26 01:51:48'),
(144, 1, 2, 1, '2025-11-27 04:59:03', '2025-11-27 04:59:03'),
(161, 6, 1, 1, '2025-11-27 05:03:05', '2025-11-27 05:03:05'),
(162, 6, 1, 2, '2025-11-27 05:03:05', '2025-11-27 05:03:05'),
(163, 6, 1, 3, '2025-11-27 05:03:05', '2025-11-27 05:03:05'),
(164, 6, 1, 4, '2025-11-27 05:03:05', '2025-11-27 05:03:05'),
(177, 7, 2, 1, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(178, 7, 2, 2, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(179, 7, 2, 3, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(180, 7, 2, 4, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(181, 8, 2, 1, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(182, 8, 2, 2, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(183, 8, 2, 3, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(184, 8, 2, 4, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(185, 9, 2, 1, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(186, 9, 2, 2, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(187, 9, 2, 3, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(188, 9, 2, 4, '2025-11-27 05:16:55', '2025-11-27 05:16:55'),
(189, 7, 1, 1, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(190, 7, 1, 2, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(191, 7, 1, 3, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(192, 7, 1, 4, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(193, 3, 1, 1, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(194, 3, 1, 2, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(195, 3, 1, 3, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(196, 3, 1, 4, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(197, 8, 1, 1, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(198, 8, 1, 2, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(199, 8, 1, 3, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(200, 8, 1, 4, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(201, 9, 1, 1, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(202, 9, 1, 2, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(203, 9, 1, 3, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(204, 9, 1, 4, '2025-12-01 00:20:58', '2025-12-01 00:20:58'),
(205, 19, 1, 1, '2025-12-01 01:46:37', '2025-12-01 01:46:37'),
(206, 19, 1, 2, '2025-12-01 01:46:37', '2025-12-01 01:46:37'),
(207, 19, 1, 3, '2025-12-01 01:46:37', '2025-12-01 01:46:37'),
(208, 19, 1, 4, '2025-12-01 01:46:37', '2025-12-01 01:46:37'),
(209, 19, 2, 1, '2025-12-01 04:37:47', '2025-12-01 04:37:47'),
(210, 19, 2, 2, '2025-12-01 04:37:47', '2025-12-01 04:37:47'),
(211, 19, 2, 3, '2025-12-01 04:37:47', '2025-12-01 04:37:47'),
(212, 19, 2, 4, '2025-12-01 04:37:47', '2025-12-01 04:37:47'),
(213, 19, 3, 1, '2025-12-01 04:52:56', '2025-12-01 04:52:56'),
(214, 19, 3, 2, '2025-12-01 04:52:56', '2025-12-01 04:52:56'),
(215, 19, 3, 3, '2025-12-01 04:52:56', '2025-12-01 04:52:56'),
(216, 19, 3, 4, '2025-12-01 04:52:56', '2025-12-01 04:52:56'),
(217, 20, 1, 1, '2025-12-03 01:08:15', '2025-12-03 01:08:15'),
(218, 20, 1, 2, '2025-12-03 01:08:15', '2025-12-03 01:08:15'),
(219, 20, 1, 3, '2025-12-03 01:08:15', '2025-12-03 01:08:15'),
(220, 20, 1, 4, '2025-12-03 01:08:15', '2025-12-03 01:08:15'),
(221, 21, 1, 1, '2025-12-03 03:02:08', '2025-12-03 03:02:08'),
(222, 21, 1, 2, '2025-12-03 03:02:08', '2025-12-03 03:02:08'),
(223, 21, 1, 3, '2025-12-03 03:02:08', '2025-12-03 03:02:08'),
(224, 21, 1, 4, '2025-12-03 03:02:08', '2025-12-03 03:02:08'),
(225, 22, 1, 1, '2025-12-11 00:20:04', '2025-12-11 00:20:04'),
(226, 22, 1, 2, '2025-12-11 00:20:04', '2025-12-11 00:20:04'),
(227, 22, 1, 3, '2025-12-11 00:20:04', '2025-12-11 00:20:04'),
(228, 22, 1, 4, '2025-12-11 00:20:04', '2025-12-11 00:20:04'),
(229, 23, 1, 1, '2025-12-14 01:15:19', '2025-12-14 01:15:19'),
(230, 23, 1, 2, '2025-12-14 01:15:19', '2025-12-14 01:15:19'),
(231, 23, 1, 3, '2025-12-14 01:15:19', '2025-12-14 01:15:19'),
(232, 23, 1, 4, '2025-12-14 01:15:19', '2025-12-14 01:15:19'),
(233, 24, 1, 1, '2025-12-21 05:17:11', '2025-12-21 05:17:11'),
(234, 24, 1, 2, '2025-12-21 05:17:11', '2025-12-21 05:17:11'),
(235, 24, 1, 3, '2025-12-21 05:17:11', '2025-12-21 05:17:11'),
(236, 24, 1, 4, '2025-12-21 05:17:11', '2025-12-21 05:17:11');

-- --------------------------------------------------------

--
-- Table structure for table `posts`
--

CREATE TABLE `posts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `title_fa` varchar(255) NOT NULL,
  `title_en` varchar(255) NOT NULL,
  `content_fa` text DEFAULT NULL,
  `content_en` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `status` enum('draft','published') NOT NULL DEFAULT 'draft',
  `published_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `posts`
--

INSERT INTO `posts` (`id`, `title_fa`, `title_en`, `content_fa`, `content_en`, `image`, `user_id`, `status`, `published_at`, `created_at`, `updated_at`) VALUES
(1, 'آغاز امتحانات سمستر خزانی سال ۱۴۰۴', 'آغاز امتحانات سمستر خزانی سال ۱۴۰۴', '<p><br></p>', '<p><br></p>', 'website/posts/1/WWt3QQ84QTHOaVeTo6ThKrIarSzBFC7Mph0Tv43D.jpg', 1, 'published', '2025-12-06 19:30:00', '2025-12-07 04:57:59', '2025-12-17 06:51:19'),
(2, 'علمي سيمينار د \"ديني او ملي ارزښتونو ته پاملرنه\" تر عنوان لاندې وړاندي شو', 'علمي سيمينار د \"ديني او ملي ارزښتونو ته پاملرنه\" تر عنوان لاندې وړاندي شو', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>یادگیری بصری با تئوری</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>برنامه منطقی پایه لیتون</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>رفع خطا و پیاده سازی</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p>', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>یادگیری بصری با تئوری</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>برنامه منطقی پایه لیتون</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><h4><strong>رفع خطا و پیاده سازی</strong></h4><p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p>', 'website/posts/2/7Vd51RmTPhS1Y8hZxTMtJyc6Rbl8xpVVuUIIZc4V.jpg', 1, 'published', '2025-12-06 19:30:00', '2025-12-07 04:58:51', '2025-12-17 06:46:38'),
(3, 'جلسه آنلاین مدیریت پروژه', 'جلسه آنلاین مدیریت پروژه', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p>', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p>', 'website/posts/3/oI6qiytPOZEG5Td2eA0Rgvhcs2mbqsFQgza70CZY.jpg', 1, 'published', '2025-12-21 19:30:00', '2025-12-22 04:58:15', '2025-12-22 04:58:53'),
(4, 'لینک ورود به کارگاه مهارت‌های ارتباطی', 'لینک ورود به کارگاه مهارت‌های ارتباطی', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p><br></p>', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p><br></p>', 'website/posts/4/fDEjofJuyk4hHDI044yYVukKw05neuIoL04AojhW.jpg', 1, 'published', '2025-12-21 19:30:00', '2025-12-22 05:00:13', '2025-12-22 05:00:13'),
(5, 'تجهیزات لازم برای جلسه گروهی پروژه تحقیقاتی', 'تجهیزات لازم برای جلسه گروهی پروژه تحقیقاتی', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p><br></p>', '<p>لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است. لورم ایپسوم به سادگی ساختار چاپ و متن را در بر می گیرد. لورم ایپسوم به مدت 40 سال استاندارد صنعت بوده است.</p><p><br></p>', 'website/posts/5/Nr5fso8LasfZ6SsYP3104kPzOIp4VjQfLf05pFYa.jpg', 1, 'published', '2026-01-06 06:06:47', '2025-12-22 05:01:29', '2026-01-07 06:06:47');

-- --------------------------------------------------------

--
-- Table structure for table `provinces`
--

CREATE TABLE `provinces` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `provinces`
--

INSERT INTO `provinces` (`id`, `name_fa`, `name_en`, `created_at`, `updated_at`) VALUES
(1, 'ارزگان', 'Uruzgan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(2, 'بادغیس', 'Badghis', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(3, 'بامیان', 'Bamyan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(4, 'بغلان', 'Baghlan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(5, 'بدخشان', 'Badakhshan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(6, 'جوزجان', 'Jowzjan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(7, 'سرپل', 'Sar-e Pol', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(8, 'کابل', 'Kabul', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(9, 'کاپیسا', 'Kapisa', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(10, 'کندز', 'Kunduz', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(11, 'کنر', 'Kunar', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(12, 'لغمان', 'Laghman', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(13, 'لوگر', 'Logar', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(14, 'میدان وردک', 'Maidan Wardak', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(15, 'ننگرهار', 'Nangarhar', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(16, 'نیمروز', 'Nimroz', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(17, 'نورستان', 'Nuristan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(18, 'هلمند', 'Helmand', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(19, 'هرات', 'Herat', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(20, 'فراه', 'Farah', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(21, 'فاریاب', 'Faryab', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(22, 'غزنی', 'Ghazni', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(23, 'پکتیکا', 'Paktika', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(24, 'پکتیا', 'Paktia', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(25, 'پروان', 'Parwan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(26, 'خوست', 'Khost', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(27, 'دایکندی', 'Daikundi', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(28, 'لغمان', 'Laghman', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(29, 'بادغیس', 'Badghis', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(30, 'جوزجان', 'Jowzjan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(31, 'سمنگان', 'Samangan', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(32, 'کندهار', 'Kandahar', '2025-12-21 06:27:59', '2025-12-21 06:27:59'),
(33, 'پروان', 'Parwan', '2025-12-21 06:27:59', '2025-12-21 06:27:59');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `round` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `status` enum('pending','accepted','completed','declined') NOT NULL DEFAULT 'pending',
  `assigned_at` timestamp NULL DEFAULT NULL,
  `completed_at` timestamp NULL DEFAULT NULL,
  `editor_message_fa` text DEFAULT NULL,
  `editor_message_en` text DEFAULT NULL,
  `decline_reason_fa` text DEFAULT NULL,
  `decline_reason_en` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`id`, `submission_id`, `reviewer_id`, `round`, `status`, `assigned_at`, `completed_at`, `editor_message_fa`, `editor_message_en`, `decline_reason_fa`, `decline_reason_en`, `created_at`, `updated_at`, `deleted_at`) VALUES
(21, 2, 3, 1, 'completed', '2025-11-28 01:49:12', NULL, 'Reading is easier, too, in the new Reading view. You can collapse parts of the document and focus on the text you want. If you need to stop reading before you reach the end, Word remembers where you left off - even on another device.', 'Reading is easier, too, in the new Reading view. You can collapse parts of the document and focus on the text you want. If you need to stop reading before you reach the end, Word remembers where you left off - even on another device.', NULL, NULL, '2025-11-28 01:49:12', '2025-11-28 04:13:13', NULL),
(22, 8, 2, 1, 'completed', '2025-12-01 04:51:39', NULL, 'شما به عنوان داور انتخاب شده اید', 'شما به عنوان داور انتخاب شده اید', NULL, NULL, '2025-12-01 04:51:39', '2025-12-01 04:55:29', NULL),
(24, 12, 1, 1, 'declined', '2025-12-07 11:58:06', NULL, NULL, NULL, NULL, NULL, '2025-12-07 11:58:06', '2025-12-07 12:39:44', NULL),
(26, 11, 19, 1, 'pending', '2025-12-09 23:55:33', NULL, NULL, NULL, NULL, NULL, '2025-12-09 23:55:33', '2025-12-09 23:55:33', NULL),
(27, 11, 1, 1, 'pending', '2025-12-10 01:11:46', NULL, NULL, NULL, NULL, NULL, '2025-12-10 01:11:46', '2025-12-10 01:11:46', NULL),
(28, 11, 20, 1, 'pending', '2025-12-10 01:19:08', NULL, NULL, NULL, NULL, NULL, '2025-12-10 01:19:08', '2025-12-10 01:19:08', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `review_decisions`
--

CREATE TABLE `review_decisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `recommendation` enum('accept','reject','revision_required') NOT NULL,
  `comments_fa` text DEFAULT NULL,
  `comments_en` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_decisions`
--

INSERT INTO `review_decisions` (`id`, `review_id`, `recommendation`, `comments_fa`, `comments_en`, `created_at`, `updated_at`) VALUES
(2, 21, 'accept', 'اقتصادی میشود. براساس نتایج، برای رقابت هذیریکالاهای صادراتی در بازارهای جهانی و کاهر وابستگی اقتصاد به واردات، هیشنهاد میگردد در جهتبرنامه های توسع های کشور از استراتشی تقویت جایگزینی واردات استفاده شود.  ', 'اقتصادی میشود. براساس نتایج، برای رقابت هذیریکالاهای صادراتی در بازارهای جهانی و کاهر وابستگی اقتصاد به واردات، هیشنهاد میگردد در جهتبرنامه های توسع های کشور از استراتشی تقویت جایگزینی واردات استفاده شود.  ', '2025-11-28 04:13:13', '2025-11-28 04:13:13'),
(3, 22, 'revision_required', 'بررسی دوباره صورت گیرد', 'بررسی دوباره صورت گیرد', '2025-12-01 04:55:29', '2025-12-01 04:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `review_files`
--

CREATE TABLE `review_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `review_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `review_files`
--

INSERT INTO `review_files` (`id`, `review_id`, `file_path`, `original_name`, `mime`, `size`, `created_at`, `updated_at`) VALUES
(2, 21, 'reviewer-files/21/kMjgPEf8wGzOC1y3rVfJJ1NE3ahVMS15ABzfv53r.pdf', 'Zaki Nazari _ resume.pdf', 'application/octet-stream', 1855942, '2025-11-28 04:13:13', '2025-11-28 04:13:13'),
(3, 22, 'reviewer-files/22/wmknwHzc4KCSgx2zVeXDSd2fGhlU2jUCwKYSmNsT.pdf', 'فارمت_تحلیل_گزارش_ارزیابی_خودی_1404 (1) (1).pdf', 'application/octet-stream', 315195, '2025-12-01 04:55:29', '2025-12-01 04:55:29');

-- --------------------------------------------------------

--
-- Table structure for table `review_invites`
--

CREATE TABLE `review_invites` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `reviewer_id` bigint(20) UNSIGNED NOT NULL,
  `status` enum('pending','accepted','declined') NOT NULL DEFAULT 'pending',
  `invited_at` timestamp NULL DEFAULT NULL,
  `responded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `scientific_board`
--

CREATE TABLE `scientific_board` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scientific_board`
--

INSERT INTO `scientific_board` (`id`, `name_fa`, `name_en`, `created_at`, `updated_at`) VALUES
(3, 'پوهنتون وردگ', 'وردگ پوهنتون', '2025-12-22 00:30:14', '2025-12-22 00:30:14'),
(4, 'پوهنتون کابل', 'کابل پوهنتون', '2025-12-22 00:32:46', '2025-12-22 00:32:46');

-- --------------------------------------------------------

--
-- Table structure for table `scientific_board_members`
--

CREATE TABLE `scientific_board_members` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `board_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `scientific_board_members`
--

INSERT INTO `scientific_board_members` (`id`, `name_fa`, `name_en`, `board_id`, `created_at`, `updated_at`) VALUES
(4, 'رئیس پوهنتون', 'پوهنتون رئیس', 3, '2025-12-22 00:30:14', '2025-12-30 01:33:06'),
(5, 'معاون پوهنتون', 'پوهنتون مرستیال', 3, '2025-12-22 00:30:14', '2025-12-30 01:33:06'),
(6, 'رئیس پوهنتون', 'پوهنتون رئیس', 4, '2025-12-22 00:32:46', '2025-12-22 02:20:01'),
(7, 'معاون پوهنتون', 'پوهنتون مرستیال', 4, '2025-12-22 00:32:46', '2025-12-22 02:20:01'),
(8, 'زاهدالله زاهد', 'زاهدالله زاهد', 3, '2025-12-30 01:33:06', '2025-12-30 01:33:06');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('fbqZmkiJaMrOM0XiXVVoBKXYbF7hOBldFBDpNQnm', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo2OntzOjY6Il90b2tlbiI7czo0MDoiS3pkRWt3dUs5YzZsOE9TaUhXcVEzMThER3F5TWRsNnB6Znc2RkxvcCI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MjE6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMCI7czo1OiJyb3V0ZSI7czo0OiJob21lIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czoxNToiZnJvbnRlbmRfbG9jYWxlIjtzOjI6ImZhIjtzOjUwOiJsb2dpbl93ZWJfNTliYTM2YWRkYzJiMmY5NDAxNTgwZjAxNGM3ZjU4ZWE0ZTMwOTg5ZCI7aToyO3M6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6MzQ6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wb3N0LWxpc3QvMjEiO319', 1767766729),
('R8E6Blj7DSPooZGc9OtsDgwbthNFFW5DBBBujddy', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiakpvTVc1MHJVZ1JTbVY3OE5yNVlWYkJqanpBVGg5V3VGRVZmZUxFVSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6MzU6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9wZXJtaXNzaW9ucy81IjtzOjU6InJvdXRlIjtzOjExOiJwZXJtaXNzaW9ucyI7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7fQ==', 1767769302);

-- --------------------------------------------------------

--
-- Table structure for table `submissions`
--

CREATE TABLE `submissions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submitter_id` bigint(20) UNSIGNED NOT NULL,
  `title_fa` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `abstract_fa` text DEFAULT NULL,
  `abstract_en` text DEFAULT NULL,
  `comments_to_editor_fa` text DEFAULT NULL,
  `comments_to_editor_en` text DEFAULT NULL,
  `status` enum('submitted','screening','under_review','revision_required','accepted','rejected','published') NOT NULL DEFAULT 'submitted',
  `round` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `published_at` timestamp NULL DEFAULT NULL,
  `issue_id` bigint(20) UNSIGNED DEFAULT NULL,
  `last_activity_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `views` bigint(20) UNSIGNED NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submissions`
--

INSERT INTO `submissions` (`id`, `submitter_id`, `title_fa`, `title_en`, `abstract_fa`, `abstract_en`, `comments_to_editor_fa`, `comments_to_editor_en`, `status`, `round`, `submitted_at`, `published_at`, `issue_id`, `last_activity_at`, `deleted_at`, `created_at`, `updated_at`, `views`) VALUES
(1, 1, 'تأثیر ساختار سنی جمعیت بر رشد اقتصادی کشورهای منتخب جنوب آسیا طی سال‌های (2023 – 2000)', 'تأثیر ساختار سنی جمعیت بر رشد اقتصادی کشورهای منتخب جنوب آسیا طی سال‌های (2023 – 2000)', '<p class=\"ql-align-justify\">واضح است که نرخ ارز از متغیرهای مهم اقتصاد کلان محسبوب میشود و نوسانات آن نیز روی سایرمتغیرهای اقتصادی اثر میگذارد. هدژ هشوهر حاضر، مطاهعهی اثر نوسانات نرخ ارز بر رشد اقتصادی افغانستان با استفاده از اهگوی خودرگرسیون برداری) VAR( طی دوره زمانی 1402-1388 انجام شده است.</p><p class=\"ql-align-justify\">نتایج تحقیق نشان م یدهد، بین متغیر نرخ ارز و رشد اقتصادی رابطه منفی و معنیدار وجود دارد. این امر به دهیل وابستگی اقتصاد کشور و بخر های صنعت، خدمات و کشاورزی به واردات مواد اوهیه و نهادههاست. همچنین بین متغیرهای صادرات و تشکیل سرمایه ثابت ناخاهداخلی با رشد اقتصادیرابطه مربت و معنیدار برقرار است؛ بدین معنی که افزایر نرخ ارز از مسیر تقویت انگیزه صادرات و بزرگشدن حجم سرمای هگذاری موجب تقویت رشد اقتصادی میشود. براساس نتایج، برای رقابت هذیریکالاهای صادراتی در بازارهای جهانی و کاهر وابستگی اقتصاد به واردات، هیشنهاد میگردد در جهتبرنامه های توسع های کشور از استراتشی تقویت جایگزینی واردات استفاده شود.</p>', '<p class=\"ql-align-justify\">It is quite clear that the exchange rate is one of the essential variables in macroeconomics; hence its fluctuations affect other economic’ variables as well. The purpose of the current research is to comprehend the effect of exchange rate fluctuations on Afghanistan’s economic growth using the vector auto -regression (VAR) model, during the 2009-2023 years. The results of research obviously show that there is a negative and significant relationship between the exchange rate variable and the economic development. This is due to the relevancy of the country’s economy, the industrial, service and agricultural sectors to the import of raw material and inputs. On the other hand, there is a positive and significant relationship between the export variable, the formation of fixed gross domestic capital and economic growth, which means that the increase of exchange rate through strengthening exports and enlarging the investment capacities will cause the economic growth. According to the mentioned results, for the competitiveness of export goods in universal markets and for decreasing the economy’s dependency to imports, it is requested to utilize the strengthening import substitution strategy for the development of the country. </p><p><br></p>', 'sdf', 'dsf', 'published', 1, '2025-11-24 03:45:54', '2026-01-05 03:29:26', 2, NULL, '2025-12-01 01:14:02', '2025-11-24 03:45:54', '2026-01-05 03:29:26', 0),
(2, 1, 'اثر نوسانات نرخ ارز بر رشد اقتصادی افغانستان طی دوره زمانی (۱۴0۲-۱۳88)', 'The Effect of exchange rate fluctuations on Afghanistan\'s economic growth', '<p class=\"ql-align-justify\">واضح است که نرخ ارز از متغیرهای مهم اقتصاد کلان محسبوب میشود و نوسانات آن نیز روی سایرمتغیرهای اقتصادی اثر میگذارد. هدژ هشوهر حاضر، مطاهعهی اثر نوسانات نرخ ارز بر رشد اقتصادی افغانستان با استفاده از اهگوی خودرگرسیون برداری) VAR( طی دوره زمانی 1402-1388 انجام شده است.</p><p class=\"ql-align-justify\">نتایج تحقیق نشان م یدهد، بین متغیر نرخ ارز و رشد اقتصادی رابطه منفی و معنیدار وجود دارد. این امر به دهیل وابستگی اقتصاد کشور و بخر های صنعت، خدمات و کشاورزی به واردات مواد اوهیه و نهادههاست. همچنین بین متغیرهای صادرات و تشکیل سرمایه ثابت ناخاهداخلی با رشد اقتصادیرابطه مربت و معنیدار برقرار است؛ بدین معنی که افزایر نرخ ارز از مسیر تقویت انگیزه صادرات و بزرگشدن حجم سرمای هگذاری موجب تقویت رشد اقتصادی میشود. براساس نتایج، برای رقابت هذیریکالاهای صادراتی در بازارهای جهانی و کاهر وابستگی اقتصاد به واردات، هیشنهاد میگردد در جهتبرنامه های توسع های کشور از استراتشی تقویت جایگزینی واردات استفاده شود.</p>', '<p class=\"ql-align-right\">It is quite clear that the exchange rate is one of the essential variables in macroeconomics; hence its fluctuations affect other economic’ variables as well. The purpose of the current research is to comprehend the effect of exchange rate fluctuations on Afghanistan’s economic growth using the vector auto -regression (VAR) model, during the 2009-2023 years. The results of research obviously show that there is a negative and significant relationship between the exchange rate variable and the economic development. This is due to the relevancy of the country’s economy, the industrial, service and agricultural sectors to the import of raw material and inputs. On the other hand, there is a positive and significant relationship between the export variable, the formation of fixed gross domestic capital and economic growth, which means that the increase of exchange rate through strengthening exports and enlarging the investment capacities will cause the economic growth. According to the mentioned results, for the competitiveness of export goods in universal markets and for decreasing the economy’s dependency to imports, it is requested to utilize the strengthening import substitution strategy for the development of the country. </p><p><br></p>', 'امتحانی', 'This is a testy text', 'published', 1, '2025-11-27 05:45:18', '2026-01-05 03:29:24', NULL, NULL, '2025-12-01 00:57:52', '2025-11-27 05:45:18', '2026-01-05 08:46:42', 1),
(6, 1, '1', 'asd3', '<p class=\"ql-align-right\">df</p>', '<p>df</p>', NULL, NULL, 'screening', 1, '2025-12-01 04:27:07', NULL, NULL, NULL, '2025-12-01 04:32:12', '2025-12-01 04:27:07', '2025-12-01 04:32:12', 0),
(8, 16, 'نگاهی رده‌شناختی به نظام نوع وجه در زبان ترکی آذری', 'نگاهی رده‌شناختی به نظام نوع وجه در زبان ترکی آذری', '<p class=\"ql-align-justify\"><span style=\"color: rgb(102, 102, 102); background-color: rgb(255, 255, 255);\">دولت‌ها از طریق به‌کارگیری سیاست‌های مختلف اقتصادی، از جمله سیاست مالی، می‌توانند بر وضعیت اقتصادی کشور تأثیر بگذارند. سیاست مالی با بهره‌گیری از دو ابزار اصلی، یعنی مالیات و مخارج دولت، به دولت‌ها این امکان را می‌دهد تا در جهت کنترل شرایط اقتصادی، سیاست‌های انقباضی یا انبساطی را اعمال کنند. در شرایط رکود اقتصادی، سیاست مالی انبساطی می‌تواند ابزاری مؤثر برای مقابله با بحران‌هایی چون بیکاری باشد. این پژوهش با هدف بررسی تأثیر سیاست مالی دولت بر سطح اشتغال در افغانستان در دوره‌ی 2001 تا 2022 انجام شده است. داده‌های مورد استفاده به‌صورت سری زمانی و از منابع معتبر مانند بانک جهانی و وزارت مالیه گردآوری شده و تحلیل آماری آن با بهره‌گیری از مدل رگرسیون خطی چندمتغیره و روش حداقل مربعات معمولی (OLS) در نرم‌افزار EViews انجام گرفته است. پیش از برآورد مدل، آزمون‌های آماری لازم برای داده‌های سری زمانی همچون مانایی، نرمالیتی، هم‌خطی، خودهمبستگی و همسانی واریانس صورت پذیرفته است. نتایج تحقیق نشان می‌دهد که اگرچه در برخی دوره‌ها، به‌ویژه در بخش‌های ساخت‌وساز و خدمات، سیاست‌های مالی موجب افزایش اشتغال شده‌اند، اما در مجموع، تأثیر سیاست مالی بر اشتغال در بازهٔ زمانی مورد مطالعه معنادار نبوده است. چالش‌هایی همچون فساد اداری و فقدان زیرساخت‌های مناسب، مانع اثربخشی کامل این سیاست‌ها شده‌اند. یافته‌های این پژوهش می‌تواند راهنمایی برای سیاست‌گذاران اقتصادی افغانستان در راستای بهبود سیاست‌های مالی و ارتقای سطح اشتغال و توسعه پایدار کشور باشد.</span></p>', '<p class=\"ql-align-justify\"><span style=\"background-color: rgb(248, 248, 248); color: rgb(51, 51, 51);\">پژوهش حاضر در چارچوب دستور نقشگرای نظام‌­مند و بر مبنای رده‌شناسی نقشگرای نظام­‌مند و مشخصاً تعمیم‌­های رده­‌شناختی متیسن  انجام یافته است. این پژوهش کوشیده است با استناد به نمونه‌­هایی برگرفته از اسناد مکتوب متعدد در زبان ترکی آذری از جمله کتاب­‌های دستور و مجموعه داستان­ها و نیز نمونه­‌هایی ساختگی، به توصیف رفتارهای رده­‌شناختی نظام  نوع وجه در دستور بند ترکی آذری با توجه به تعمیم‌­های رده­‌شناختی متیسن در خصوص آن نظام  بپردازد. برخی نتایج به‌دست‌آمده از پژوهش حاضر نشان می­‌دهد که نظام نوع وجه زبان ترکی آذری (1) هر سه  وجه  خبری، پرسشی قطبی و امری را دارا است؛ (2) از پرسشی قطبیِ منفی برای بیان سوگیری مثبت گوینده استفاده می­‌کند؛ (3) در ردۀ زبانیِ زبان­‌های برخوردار از مقولۀ پرسشی پرسشواژه­‌ای جای می­‌گیرد؛ (4) در پرسشی­‌های پرسشواژه‌ای، تنها مشارک­‌ها و افزوده‌­های حاشیه‌­ای را به‌عنوان  عناصر سؤال­‌پذیر مورد پرسش قرار می­‌دهد؛ (5) به ردۀ زبانی زبان­‌های پرسشواژه در جای اصلی تعلق دارد و (6) وجه امری را از سایر وجوه متمایز می­‌سازد.</span></p>', NULL, NULL, 'published', 2, '2025-12-01 04:40:10', '2025-12-17 06:44:49', 2, NULL, NULL, '2025-12-01 04:40:10', '2025-12-17 06:44:49', 1),
(11, 1, 'سشیب', 'سیشب', '<p>فناوری در عصر حاضر به یکی از اساسی‌ترین عناصر زندگی انسان تبدیل شده و تأثیرات عمیقی بر ابعاد مختلف اجتماعی، آموزشی، اقتصادی و فرهنگی گذاشته است. گسترش ابزارهای دیجیتالی و سیستم‌های هوشمند باعث تسهیل ارتباطات، افزایش سرعت دسترسی به اطلاعات و بهبود کارایی در انجام فعالیت‌های روزمره شده است. در حوزه آموزش، فناوری امکان یادگیری از راه دور و دسترسی برابرتر به منابع علمی را فراهم کرده و در بخش اقتصاد نیز زمینه‌ساز توسعه تجارت الکترونیک و ایجاد فرصت‌های شغلی جدید شده است. با این حال، استفاده نادرست یا بیش از حد از فناوری می‌تواند پیامدهای منفی مانند کاهش تعاملات انسانی، وابستگی شدید به فضای مجازی، تهدید حریم خصوصی و افزایش آسیب‌های اجتماعی را به دنبال داشته باشد. از این‌رو، بهره‌گیری آگاهانه و مسئولانه از فناوری اهمیت ویژه‌ای دارد. آموزش مهارت‌های دیجیتالی، ارتقای سواد رسانه‌ای و توجه به اصول اخلاقی در استفاده از تکنالوژی می‌تواند نقش مؤثری در کاهش آسیب‌ها و افزایش مزایا داشته باشد. در مجموع، فناوری ابزاری قدرتمند است که اگر به‌درستی مدیریت شود، می‌تواند مسیر پیشرفت پایدار و متوازن جوامع انسانی را هموار سازد.</p>', '<p class=\"ql-direction-rtl ql-align-right\">فناوری در عصر حاضر به یکی از اساسی‌ترین عناصر زندگی انسان تبدیل شده و تأثیرات عمیقی بر ابعاد مختلف اجتماعی، آموزشی، اقتصادی و فرهنگی گذاشته است. گسترش ابزارهای دیجیتالی و سیستم‌های هوشمند باعث تسهیل ارتباطات، افزایش سرعت دسترسی به اطلاعات و بهبود کارایی در انجام فعالیت‌های روزمره شده است. در حوزه آموزش، فناوری امکان یادگیری از راه دور و دسترسی برابرتر به منابع علمی را فراهم کرده و در بخش اقتصاد نیز زمینه‌ساز توسعه تجارت الکترونیک و ایجاد فرصت‌های شغلی جدید شده است. با این حال، استفاده نادرست یا بیش از حد از فناوری می‌تواند پیامدهای منفی مانند کاهش تعاملات انسانی، وابستگی شدید به فضای مجازی، تهدید حریم خصوصی و افزایش آسیب‌های اجتماعی را به دنبال داشته باشد. از این‌رو، بهره‌گیری آگاهانه و مسئولانه از فناوری اهمیت ویژه‌ای دارد. آموزش مهارت‌های دیجیتالی، ارتقای سواد رسانه‌ای و توجه به اصول اخلاقی در استفاده از تکنالوژی می‌تواند نقش مؤثری در کاهش آسیب‌ها و افزایش مزایا داشته باشد. در مجموع، فناوری ابزاری قدرتمند است که اگر به‌درستی مدیریت شود، می‌تواند مسیر پیشرفت پایدار و متوازن جوامع انسانی را هموار سازد.</p>', NULL, NULL, 'screening', 1, '2025-12-05 01:55:12', NULL, NULL, NULL, '2026-01-05 01:56:54', '2025-12-05 01:55:12', '2026-01-05 01:56:54', 0),
(12, 23, 'مقایسۀ عملکرد الگوریتم‌های پایۀ یادگیری ماشین در دسته‌بندی اشعار فارسی به دو گروه تلمیح‌دار و بدون‌تلمیح', 'مقایسۀ عملکرد الگوریتم‌های پایۀ یادگیری ماشین در دسته‌بندی اشعار فارسی به دو گروه تلمیح‌دار و بدون‌تلمیح', '<p>هدف از پژوهش حاضر بررسی عملکرد چند روش‌ یادگیری ماشین در دسته‌بندی اشعار فارسی به دو گروه تلمیح‌دار و بدون‌تلمیح است. به‌این‌منظور، از روش‌های نظارت‌شدۀ بیز ساده، ماشین بردار پشتیبان، درخت تصمیم، جنگل تصادفی، k نزدیک‌ترین همسایه، رگرسیون لجستیک و الگوریتم پرسپترون چندلایه استفاده شد. پس از جمع‌آوری داده‌های برچسب‌خورده در قالب دو فایل متنی، هرکدام از ابیات به بردار عددی تبدیل شدند. پس از ادغام داده‌ها و تقسیم آنها به دو دستۀ آموزش و آزمون، الگوریتم مدنظر بر روی داده‌های آموزشی پیاد‌ه‌سازی و بر روی داده‌های آزمون، آزمایش گردید تا دقت عملکرد الگوریتم سنجیده شود. خروجی هر الگوریتم، برچسب پیش‌بینی‌شده توسط ماشین برای ابیات موردنظر بود و برای ارزیابی الگوریتم‌‌ها از روش LOOCV استفاده شد. نتایج ارزیابی نشان داد که الگوریتم‌های بیز ساده 09/76%، رگرسیون لجستیک 09/76%، پرسپترون چند لایه 22/75% و ماشین بردار پشتیبان 35/74% نسبت به الگوریتم‌های دیگر عملکرد بهتری دارند. درمجموع و با توجه به سایر معیارها، از جمله معیار اف ـ 1 و زمان اجرا، می‌توان گفت که بهترین عملکرد مربوط به الگوریتم بیز ساده بود.</p>', '<p class=\"ql-align-right\">هدف از پژوهش حاضر بررسی عملکرد چند روش‌ یادگیری ماشین در دسته‌بندی اشعار فارسی به دو گروه تلمیح‌دار و بدون‌تلمیح است. به‌این‌منظور، از روش‌های نظارت‌شدۀ بیز ساده، ماشین بردار پشتیبان، درخت تصمیم، جنگل تصادفی، k نزدیک‌ترین همسایه، رگرسیون لجستیک و الگوریتم پرسپترون چندلایه استفاده شد. پس از جمع‌آوری داده‌های برچسب‌خورده در قالب دو فایل متنی، هرکدام از ابیات به بردار عددی تبدیل شدند. پس از ادغام داده‌ها و تقسیم آنها به دو دستۀ آموزش و آزمون، الگوریتم مدنظر بر روی داده‌های آموزشی پیاد‌ه‌سازی و بر روی داده‌های آزمون، آزمایش گردید تا دقت عملکرد الگوریتم سنجیده شود. خروجی هر الگوریتم، برچسب پیش‌بینی‌شده توسط ماشین برای ابیات موردنظر بود و برای ارزیابی الگوریتم‌‌ها از روش LOOCV استفاده شد. نتایج ارزیابی نشان داد که الگوریتم‌های بیز ساده 09/76%، رگرسیون لجستیک 09/76%، پرسپترون چند لایه 22/75% و ماشین بردار پشتیبان 35/74% نسبت به الگوریتم‌های دیگر عملکرد بهتری دارند. درمجموع و با توجه به سایر معیارها، از جمله معیار اف ـ 1 و زمان اجرا، می‌توان گفت که بهترین عملکرد مربوط به الگوریتم بیز ساده بود.</p>', NULL, NULL, 'accepted', 1, '2025-12-06 23:48:52', NULL, NULL, NULL, NULL, '2025-12-06 23:48:52', '2026-01-05 05:15:47', 5),
(13, 2, 'یب', 'یسب', NULL, NULL, NULL, NULL, 'screening', 1, '2025-12-18 05:44:24', NULL, NULL, NULL, NULL, '2025-12-18 05:44:24', '2026-01-05 01:45:01', 0),
(15, 3, 'er', 'ewr', '', '', NULL, NULL, 'under_review', 1, '2025-12-30 05:36:47', NULL, NULL, NULL, NULL, '2025-12-30 05:36:47', '2026-01-05 01:42:58', 0);

-- --------------------------------------------------------

--
-- Table structure for table `submission_authors`
--

CREATE TABLE `submission_authors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `country_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `type_id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `education_degree_id` int(11) DEFAULT NULL,
  `academic_rank_id` int(11) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `given_name_fa` varchar(255) DEFAULT NULL,
  `given_name_en` varchar(255) DEFAULT NULL,
  `family_name_fa` varchar(255) DEFAULT NULL,
  `family_name_en` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_no` varchar(16) DEFAULT NULL,
  `affiliation_fa` varchar(255) DEFAULT NULL,
  `affiliation_en` varchar(255) DEFAULT NULL,
  `city_fa` varchar(255) DEFAULT NULL,
  `city_en` varchar(255) DEFAULT NULL,
  `department_fa` varchar(255) DEFAULT NULL,
  `department_en` varchar(255) DEFAULT NULL,
  `preferred_research_area_fa` varchar(255) DEFAULT NULL,
  `preferred_research_area_en` varchar(255) DEFAULT NULL,
  `order_index` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_authors`
--

INSERT INTO `submission_authors` (`id`, `country_id`, `user_id`, `type_id`, `submission_id`, `education_degree_id`, `academic_rank_id`, `province_id`, `given_name_fa`, `given_name_en`, `family_name_fa`, `family_name_en`, `email`, `phone_no`, `affiliation_fa`, `affiliation_en`, `city_fa`, `city_en`, `department_fa`, `department_en`, `preferred_research_area_fa`, `preferred_research_area_en`, `order_index`, `created_at`, `updated_at`) VALUES
(1, 1, NULL, 1, 12, 1, 1, 2, 'مهدی', 'Mahdi', 'زاهدی', 'Zahedi', 'mahdi@gmail.com', '0770355318', 'مدیر', 'Manager', 'حسینی', 'Hussaini', 'انجنیری ساختمان', NULL, 'پژوهشی', NULL, 1, '2025-12-07 00:59:52', '2025-12-07 01:10:44'),
(2, 1, NULL, 2, 12, 1, 2, 2, 'حبیب الله', 'Habibullah', 'احمدی', NULL, 'habib@gmail.com', '0770355319', 'پروگرامر دانشگاه', NULL, 'شهر کابل', NULL, 'کمپیوتر', NULL, 'تحقیقات علمی', NULL, 1, '2025-12-07 02:04:27', '2025-12-07 02:04:27'),
(3, 1, 2, 1, 13, NULL, NULL, 2, NULL, NULL, NULL, NULL, 'sina@gmail.com', '0777', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-18 05:44:24', '2025-12-18 05:44:24'),
(5, NULL, 3, 1, 15, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lida@gmail.com', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 1, '2025-12-30 05:36:47', '2025-12-30 05:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `submission_files`
--

CREATE TABLE `submission_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `file_type` enum('manuscript','supplementary','cover_letter','metadata_xml') NOT NULL,
  `original_name` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `round` int(10) UNSIGNED NOT NULL DEFAULT 1,
  `size` bigint(20) UNSIGNED DEFAULT NULL,
  `mime` varchar(255) DEFAULT NULL,
  `uploaded_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_files`
--

INSERT INTO `submission_files` (`id`, `submission_id`, `file_type`, `original_name`, `file_path`, `uploaded_by`, `round`, `size`, `mime`, `uploaded_at`, `created_at`, `updated_at`) VALUES
(26, 1, 'manuscript', 'فارمت_تحلیل_گزارش_ارزیابی_خودی_1404.pdf', 'submissions/1/PDnfM3PkJIigb0oB3L9fxGTkNvBPHglfXSZ6caz4.pdf', 1, 1, 315195, 'application/octet-stream', '2025-12-01 01:13:33', '2025-12-01 01:13:33', '2025-12-01 01:13:33'),
(27, 6, 'manuscript', 'فارمت_تحلیل_گزارش_ارزیابی_خودی_1404.pdf', 'submissions/6/2cvsUgZmbpNkWEOpCs0S8iv2Ip0YEjfmDsbjCk47.pdf', 1, 1, 315195, 'application/octet-stream', '2025-12-01 04:27:44', '2025-12-01 04:27:44', '2025-12-01 04:27:44'),
(28, 8, 'manuscript', 'فارمت_تحلیل_گزارش_ارزیابی_خودی_1404 (1) (1).pdf', 'submissions/8/0dbYzCcKH2atyLZWH3yfdBXqwQWiwgUJB02sjYI4.pdf', 16, 1, 315195, 'application/octet-stream', '2025-12-01 04:43:37', '2025-12-01 04:43:37', '2025-12-01 04:43:37'),
(29, 8, 'manuscript', 'LS_Volume 12_Issue 21.pdf', 'submissions/8/OUxy9gCsSiwLa0igy2Oryw9TUgYyA1Z4eoPwg9kU.pdf', 16, 2, 6158360, 'application/octet-stream', '2025-12-01 04:58:33', '2025-12-01 04:58:33', '2025-12-01 04:58:33'),
(34, 12, 'manuscript', 'فارمت_تحلیل_گزارش_ارزیابی_خودی_1404.pdf', 'submissions/12/UmtLQexToCq4YAfYk1mnr6DYP40tsgVXpvj56iho.pdf', 23, 1, 315195, 'application/octet-stream', '2025-12-06 23:50:12', '2025-12-06 23:50:12', '2025-12-06 23:50:12');

-- --------------------------------------------------------

--
-- Table structure for table `submission_keywords`
--

CREATE TABLE `submission_keywords` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `keyword_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_keywords`
--

INSERT INTO `submission_keywords` (`id`, `submission_id`, `keyword_id`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(2, 1, 2, '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(3, 1, 3, '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(4, 1, 4, '2025-11-24 06:08:00', '2025-11-24 06:08:00'),
(5, 1, 5, '2025-11-26 05:36:42', '2025-11-26 05:36:42'),
(6, 1, 6, '2025-11-26 05:36:42', '2025-11-26 05:36:42'),
(7, 2, 7, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(8, 2, 8, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(9, 2, 9, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(10, 2, 10, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(11, 2, 11, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(12, 2, 12, '2025-11-27 05:50:38', '2025-11-27 05:50:38'),
(13, 6, 13, '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(14, 6, 14, '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(15, 6, 15, '2025-12-01 04:27:33', '2025-12-01 04:27:33'),
(16, 8, 16, '2025-12-01 04:43:26', '2025-12-01 04:43:26'),
(17, 8, 17, '2025-12-01 04:43:26', '2025-12-01 04:43:26'),
(22, 12, 21, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(23, 12, 22, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(24, 12, 23, '2025-12-07 02:12:14', '2025-12-07 02:12:14'),
(25, 11, 21, '2025-12-16 06:50:26', '2025-12-16 06:50:26'),
(26, 8, 19, '2025-12-16 06:55:38', '2025-12-16 06:55:38'),
(27, 12, 24, '2025-12-16 23:47:39', '2025-12-16 23:47:39'),
(33, 8, 30, '2025-12-16 23:49:01', '2025-12-16 23:49:01'),
(34, 8, 31, '2025-12-16 23:49:01', '2025-12-16 23:49:01'),
(41, 15, 32, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(42, 15, 33, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(43, 15, 34, '2025-12-30 05:38:35', '2025-12-30 05:38:35'),
(52, 11, 43, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(53, 11, 44, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(54, 11, 45, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(55, 11, 46, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(56, 11, 47, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(57, 11, 4, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(58, 11, 48, '2026-01-05 01:56:12', '2026-01-05 01:56:12'),
(75, 12, 46, '2026-01-05 05:16:37', '2026-01-05 05:16:37'),
(76, 12, 47, '2026-01-05 05:16:37', '2026-01-05 05:16:37'),
(77, 12, 4, '2026-01-05 05:16:37', '2026-01-05 05:16:37'),
(78, 12, 48, '2026-01-05 05:16:37', '2026-01-05 05:16:37');

-- --------------------------------------------------------

--
-- Table structure for table `submission_logs`
--

CREATE TABLE `submission_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `notes_fa` text DEFAULT NULL,
  `notes_en` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission_tags`
--

CREATE TABLE `submission_tags` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `tag_fa` varchar(255) DEFAULT NULL,
  `tag_en` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `submission_views`
--

CREATE TABLE `submission_views` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `submission_id` bigint(20) UNSIGNED NOT NULL,
  `ip` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `submission_views`
--

INSERT INTO `submission_views` (`id`, `submission_id`, `ip`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 8, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-17 00:42:31', '2025-12-17 00:42:31'),
(3, 12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-20 03:18:20', '2025-12-20 03:18:20'),
(6, 12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-22 04:33:05', '2025-12-22 04:33:05'),
(7, 12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-30 05:32:21', '2025-12-30 05:32:21'),
(9, 12, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2026-01-01 07:04:30', '2026-01-01 07:04:30'),
(11, 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36 Edg/143.0.0.0', '2026-01-05 08:46:42', '2026-01-05 08:46:42');

-- --------------------------------------------------------

--
-- Table structure for table `system_logs`
--

CREATE TABLE `system_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) NOT NULL,
  `st_id` int(11) DEFAULT NULL,
  `s_id` int(11) DEFAULT NULL,
  `section` text DEFAULT NULL,
  `type_id` tinyint(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `name_fa` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `family_name_fa` varchar(255) DEFAULT NULL,
  `family_name_en` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `phone_no` varchar(16) DEFAULT NULL,
  `affiliation_fa` varchar(255) DEFAULT NULL,
  `affiliation_en` varchar(255) DEFAULT NULL,
  `two_factor_secret` text DEFAULT NULL,
  `two_factor_recovery_codes` text DEFAULT NULL,
  `two_factor_confirmed_at` timestamp NULL DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `country_id` int(11) DEFAULT NULL,
  `profile_photo` varchar(255) DEFAULT NULL,
  `province_id` int(11) DEFAULT NULL,
  `city_fa` varchar(255) DEFAULT NULL,
  `city_en` varchar(255) DEFAULT NULL,
  `department_fa` varchar(255) DEFAULT NULL,
  `department_en` varchar(255) DEFAULT NULL,
  `preferred_research_area_fa` varchar(255) DEFAULT NULL,
  `preferred_research_area_en` varchar(255) DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `education_degree_id` int(11) DEFAULT NULL,
  `academic_rank_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `name_fa`, `name_en`, `family_name_fa`, `family_name_en`, `email`, `email_verified_at`, `password`, `phone_no`, `affiliation_fa`, `affiliation_en`, `two_factor_secret`, `two_factor_recovery_codes`, `two_factor_confirmed_at`, `remember_token`, `created_at`, `updated_at`, `country_id`, `profile_photo`, `province_id`, `city_fa`, `city_en`, `department_fa`, `department_en`, `preferred_research_area_fa`, `preferred_research_area_en`, `deleted_at`, `education_degree_id`, `academic_rank_id`) VALUES
(1, 'zakinazari', 'ذکی ', 'Zaki ', 'نظری', 'Nazari', 'zakinazari10@gmail.com', NULL, '$2y$12$Qa7sGZm3XC.lFOSwwEb4D.PvovAeER0NmRhQBc2KIunMjPdhiELKO', '0770618198', 'مدیر', 'Manager', NULL, NULL, NULL, 'jCrcjnZQ7y0ikPReegiuDZ69eLGiyHhHKoxJbghcMm2kPwf3XiLkTodB5M9z', '2025-10-04 23:42:30', '2026-01-07 06:27:29', 1, 'profile_photo/1/VKT1C8vGLTWpxJi6KWosyClMV59WE0bih52cDRGn.png', 2, 'کابل', 'Kabul', 'کمپیوتر ساینس', 'Computer Science', 'پژوهشی', 'Research', NULL, 2, 1),
(2, 'sina', NULL, NULL, NULL, NULL, 'sina@gmail.com', NULL, '$2y$12$Wnsvn94seqi0St8UBTU/s.HPOgbmVsJd96EpF0NN22iZjedLLzPMG', '0777', NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-13 01:58:59', '2026-01-07 06:13:25', 1, 'profile_photo/2/MzwhJbkwsUWuxvJLv4rKZZqju9H2XmzQ2cxoP8c3.png', 2, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:13:25', NULL, NULL),
(3, 'lida', NULL, NULL, NULL, NULL, 'lida@gmail.com', NULL, '$2y$12$gTT2ekIrUV.bx6kfJScvcOaDzDUjmbks1r6MI9.Fb0mfPq0IMqCQe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-11-17 06:11:37', '2026-01-07 06:39:08', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:39:08', NULL, NULL),
(16, 'ahmad', NULL, NULL, NULL, NULL, 'ahmadi@gmail.com', NULL, '$2y$12$1FoOLqcboqhCKwpBQUKzCOKDIvjm2UMm5EFevkm7MGsZG5Fjm1KPC', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-01 04:36:02', '2026-01-07 06:13:01', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:13:01', NULL, NULL),
(17, 'hamid', NULL, NULL, NULL, NULL, 'hamid@gmail.com', NULL, '$2y$12$vNngd2GGV238Rx9.H551ceIjjTikz1VXB2pa70bgZjwQA.IU//N82', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-01 06:11:00', '2026-01-07 06:13:06', 1, 'profile_photo/17/lUeH6qYdAuxB89cP5ylFRzquCRw3A5TI1LxoXvi5.jpg', 1, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:13:06', NULL, NULL),
(18, 'پوهنتون وردگ', NULL, NULL, NULL, NULL, 'National.Conference@wu.edu.af', NULL, '$2y$12$QzG8jh43l85h7tSO9N00G.4IA/4pL6RP92swBgwJNT4AMnOwOXLb2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 04:16:06', '2026-01-07 06:28:41', NULL, 'profile_photo/18/lzYzDxw252SrMQyKUpSZdhpvdG4169yFqkKkYT5l.png', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(19, 'پوهنتون کابل', NULL, NULL, NULL, NULL, 'nasrafghani@gmail.com', NULL, '$2y$12$QLM5mnveWH0bvvav/mwfV.QtDWoCFls8d2XVMsYGxjZHNncjJR20G', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 04:16:38', '2026-01-07 06:29:12', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(20, 'پوهنتون تعلیم و تربیه کابل', NULL, NULL, NULL, NULL, 'mujeebullah.ahmadzai@gmail.com', NULL, '$2y$12$e3fpBSGylkZCZiy4DQXg9eXc2uywOeGEJ2HxHIaHxiPtSxGg2o/Ei', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 04:19:43', '2026-01-07 06:35:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(21, 'د کابل ښوونی اور روزنی پوهنتون', NULL, NULL, NULL, NULL, 'fake@gmail.com', NULL, '$2y$12$MgEbdHW7NUc0z3pbfEEqre/ENULs9hKyyMMiJoHCuc71EmsWWNnIe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 04:21:55', '2026-01-07 06:44:41', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(22, 'پوهنتون کندهار', NULL, NULL, NULL, NULL, 'vicechancellor@kdru.edu.af', NULL, '$2y$12$EKX0vw46tgkHxNIuDjm3XOYzMeFql4XevAJOcsN2boJ0yncLFMITe', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2025-12-04 04:22:32', '2026-01-07 06:38:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(23, 'mahdizahedi', 'مهدی', '', 'زاهدی', '', 'mahdi@gmail.com', NULL, '$2y$12$X2vecYaPX5QY3EWA986sGOCZo/kIIUWA5Q1H.oDM5tzDRKTELmtw2', '0770355318', 'مدیر', '', NULL, NULL, NULL, NULL, '2025-12-06 06:05:40', '2026-01-07 06:17:43', 1, NULL, 1, 'حسینی', NULL, 'انجنیری', NULL, 'پژوهشی', NULL, '2026-01-07 06:17:43', 1, 1),
(24, 'r', 'ق', '', '', '', 's@gmail.com', NULL, '$2y$12$ngNZwy7hqZo5Q3tG7fpfOu4Egw3xFcDSpOIa5LxcBryUWh2iULsZ6', '02132454', 'ق', '', NULL, NULL, NULL, NULL, '2025-12-21 02:23:35', '2026-01-07 06:13:31', 1, NULL, 1, NULL, NULL, 'ص', NULL, 'ص', NULL, '2026-01-07 06:13:31', 1, 1),
(25, 'd', 'f', '', 'f', '', 'df@gmail.com', NULL, '$2y$12$ZeZLrlnPdzDM03FBQ/2cEu/uLmcGXzNO7LmwI4CrdoFZRPXaB7Z0a', 'd', '', '', NULL, NULL, NULL, NULL, '2025-12-21 02:32:50', '2026-01-07 06:11:36', 1, NULL, 9, NULL, NULL, 'd', NULL, 'd', NULL, '2026-01-07 06:11:36', 3, 1),
(26, 'testyyy', 'rw', '', '', '', 'a@gmail.com', NULL, '$2y$12$H5IKLKAaWI.XYV/HI4h6NetFIGzm04Etm0oGmnXS9yziisdRe1baq', '08845687', 'd', '', NULL, NULL, NULL, NULL, '2026-01-01 06:21:31', '2026-01-07 06:11:32', 10, NULL, 14, NULL, NULL, 'd', NULL, 'f', NULL, '2026-01-07 06:11:32', 1, 1),
(27, 'df', 'یبس', '', '', '', 'sdf@gmail.com', NULL, '$2y$12$5b6baQkccwnPlQcBUcrg8eMB4ESd8a9al14eKMVw6Yi4lBANmB19G', 'df', '', '', NULL, NULL, NULL, NULL, '2026-01-01 06:30:28', '2026-01-07 06:11:28', 11, NULL, 6, NULL, NULL, 'سی', NULL, 'سیب', NULL, '2026-01-07 06:11:28', 1, 1),
(28, 'پوهنتون جوزجان', NULL, NULL, NULL, NULL, 'jwuz.university@ju.edu.af', NULL, '$2y$12$s.u8lNEzJ8LWH4Ta6X5z.uxvffP0zPRoCI.wVvkfh270ULc965tky', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:30:42', '2026-01-07 06:30:42', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(29, 'پوهنتون افغان سید جمال الدین ولایت کنر', NULL, NULL, NULL, NULL, 'Hashimi.saidasghar1@gmail.com', NULL, '$2y$12$bCqrOYkU3m4tZpw0CPkh0u2EDldxiJjvWsW6AZNOwNBWwf0JEseXK', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:32:48', '2026-01-07 06:32:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(30, 'پوهنتون پکتیا', NULL, NULL, NULL, NULL, 'wahabhekmat@pu.edu.af', NULL, '$2y$12$sD13zqafMf0Xwk1y7CouWOQEJrySFKJnRVtfpST7rh7jrJux2f4B6', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:33:40', '2026-01-07 06:33:40', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL),
(31, 'پوهنتون غزنی', NULL, NULL, NULL, NULL, 'ihsanullahakramzoi@gu.edu.af', NULL, '$2y$12$475w0Vi8InuCz3B0gA6eue1DoPTt.QF229yKiuBc6fs7aQ40HixGm', NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-07 06:34:57', '2026-01-07 06:34:57', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `web_menus`
--

CREATE TABLE `web_menus` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) NOT NULL,
  `name_en` varchar(255) NOT NULL,
  `status` tinyint(4) NOT NULL COMMENT '1=activ, 2=inactive',
  `order` mediumint(9) DEFAULT NULL,
  `parent_id` mediumint(9) DEFAULT NULL,
  `grand_parent_id` mediumint(9) DEFAULT NULL,
  `type_id` smallint(6) NOT NULL,
  `page_id` mediumint(9) DEFAULT NULL,
  `slug` varchar(64) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_menus`
--

INSERT INTO `web_menus` (`id`, `name_fa`, `name_en`, `status`, `order`, `parent_id`, `grand_parent_id`, `type_id`, `page_id`, `slug`, `created_at`, `updated_at`) VALUES
(1, 'صفحه اصلی', 'اصلي پاڼه', 1, 1, NULL, NULL, 1, 4, 'home', '2025-11-29 02:56:14', '2025-12-04 02:36:02'),
(2, 'درباره کنفرانس', 'د کنفرانس په اړه', 1, 2, NULL, NULL, 1, 1, NULL, '2025-11-29 02:58:59', '2025-12-07 05:59:12'),
(3, 'مقاله‌ها', 'مقالې', 1, 3, NULL, NULL, 1, NULL, NULL, '2025-11-29 03:06:08', '2025-12-16 00:03:27'),
(4, 'اعضای بورد علمي', 'د علمي بورد غړي', 1, 4, NULL, NULL, 1, NULL, 'scientific_board', '2025-11-29 03:06:58', '2025-12-06 00:35:57'),
(5, 'فرامین', 'فرمانونه', 1, 5, NULL, NULL, 1, 3, NULL, '2025-11-29 03:07:59', '2025-12-07 05:57:58'),
(6, 'راهنمایی نویسنده گان', 'د لیکوالانو لارښود', 1, 6, NULL, NULL, 1, NULL, NULL, '2025-11-29 03:08:19', '2025-12-07 06:06:00'),
(7, 'اطلاعیه ها', 'خبرتیاوې', 1, 7, NULL, NULL, 1, NULL, 'post', '2025-11-29 03:08:58', '2025-12-07 05:56:37'),
(8, 'ارسال مقاله', 'مقاله لېږل', 1, 8, NULL, NULL, 1, 2, NULL, '2025-11-29 03:09:54', '2025-12-07 05:54:40'),
(9, 'معرفي کنفرانس', 'د کنفرانس پېژندنه', 1, 1, 2, NULL, 2, 5, NULL, '2025-11-29 05:22:59', '2025-12-13 05:59:58'),
(10, 'ضرورت و اهمیت کنفرانس', 'د کنفرانس اړتیا او ارزښت', 1, 2, 2, NULL, 2, 6, NULL, '2025-11-29 05:30:18', '2025-12-20 03:38:00'),
(11, 'اهداف کنفرانس', 'د کنفرانس موخې', 1, 3, 2, NULL, 2, 7, NULL, '2025-12-06 00:16:32', '2025-12-13 06:03:50'),
(12, 'محور های کنفرانس', 'د کنفرانس محورونه', 1, 4, 2, NULL, 2, 8, NULL, '2025-12-06 00:17:25', '2025-12-13 06:16:04'),
(13, 'نتایج متوقعه کنفرانس', 'د کنفرانس متوقعه پایلې', 1, 5, 2, NULL, 2, 10, NULL, '2025-12-06 00:17:58', '2025-12-13 06:35:32'),
(14, 'امتیازهای اشتراک در کنفرانس', 'کنفرانس کې د ګډون امتیازونه', 1, 6, 2, NULL, 2, 9, NULL, '2025-12-06 00:18:31', '2025-12-13 06:32:19'),
(15, 'پلان زماني کنفرانس ', 'د کنفرانس مهالنی پلان', 1, 7, 2, NULL, 2, NULL, NULL, '2025-12-06 00:19:13', '2025-12-06 00:19:13'),
(16, 'خلاصه‌های پذیرفته شده', 'منل شوي لنډیزونه', 1, 1, 3, NULL, 2, 15, NULL, '2025-12-06 00:27:25', '2025-12-20 11:02:10'),
(17, 'مقاله‌های پذیرفته شده', 'منل شوې مقالې', 1, 2, 3, NULL, 2, 16, NULL, '2025-12-06 00:28:45', '2025-12-20 11:02:21'),
(18, 'راهنمای نگارش مقاله', 'د مقالې لیکلو لارښود', 1, 3, 6, NULL, 2, 14, NULL, '2025-12-06 00:29:31', '2025-12-20 10:21:25'),
(19, 'راهنمای ارسال مقاله', 'د مقالې لېږلو لارښود', 1, 4, 6, NULL, 2, 19, NULL, '2025-12-06 00:31:06', '2025-12-20 11:49:42'),
(20, 'همه‌ی مقاله‌ها', 'ټولې مقالې', 1, 5, 3, NULL, 2, NULL, 'articles', '2025-12-06 00:34:11', '2025-12-16 00:09:16'),
(21, 'تأثیرات کنفرانس', 'د کنفرانس اغېزې', 1, 2, 2, NULL, 2, 11, NULL, '2025-12-13 06:38:59', '2025-12-13 06:42:21'),
(22, 'شرکت‌کنندگان کنفرانس', 'د کنفرانس ګډونوال', 1, 5, 2, NULL, 2, 12, NULL, '2025-12-13 06:48:26', '2025-12-13 06:51:49'),
(23, 'فهرست فرامین', 'د فرمانونو فهرست', 1, 1, 5, NULL, 2, 17, '', '2025-12-13 07:09:37', '2025-12-20 11:16:36'),
(24, 'فهرست احکام', 'د احکامو فهرست', 1, 2, 5, NULL, 2, 18, NULL, '2025-12-13 07:10:01', '2025-12-20 11:18:58'),
(25, 'جریده‌های رسمی', 'رسمي جریدې', 1, 3, 5, NULL, 2, NULL, 'gazette', '2025-12-13 07:11:05', '2025-12-13 07:11:05');

-- --------------------------------------------------------

--
-- Table structure for table `web_pages`
--

CREATE TABLE `web_pages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name_fa` varchar(255) DEFAULT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `title_fa` varchar(255) DEFAULT NULL,
  `title_en` varchar(255) DEFAULT NULL,
  `content_fa` text DEFAULT NULL,
  `content_en` text DEFAULT NULL,
  `cover_image` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_pages`
--

INSERT INTO `web_pages` (`id`, `name_fa`, `name_en`, `title_fa`, `title_en`, `content_fa`, `content_en`, `cover_image`, `created_at`, `updated_at`) VALUES
(1, 'درباره‌ی مجله', 'د مجلې په اړه', 'درباره‌ی مجله', 'د مجلې په اړه', '<p>مجله یک نشریهٔ علمی، پژوهشی و گاه تخصصی است که مقالات و پژوهش‌های علمی را منتشر می‌کند. هدف مجله، گسترش دانش، تبادل اندیشه‌ها، تقویت جامعهٔ علمی و حمایت از پژوهشگران است.</p><p>مقالات پیش از چاپ، توسط داوران متخصص و با رعایت اصول داوری منصفانه بررسی می‌شوند تا از صحت علمی، نوآوری و کیفیت مقاله اطمینان حاصل شود. مجله می‌تواند در حوزه‌های مختلف علمی، فرهنگی، اجتماعی یا فناوری فعالیت داشته باشد و دانشجویان، پژوهشگران و علاقه‌مندان به علم را با تازه‌ترین یافته‌ها و دستاوردهای علمی آشنا کند.</p><p>علاوه بر انتشار مقالات، برخی مجلات کارگاه‌ها، نقد و بررسی‌ها و یادداشت‌های علمی نیز منتشر می‌کنند تا فرآیند یادگیری و پژوهش برای خوانندگان جذاب و مفید باشد. مجله نقش مهمی در ارتقای سطح علمی جامعه و ایجاد انگیزه برای پژوهش‌های بیشتر دارد.</p>', '<p>مجله یو علمي، څېړنیز او کله کله تخصصي خپرونه ده چې علمي مقالې او څېړنیزې څېړنې خپروي. د مجلې هدف د پوهې پراختیا، د فکرونو تبادله، د علمي ټولنې پیاوړتیا او د څېړونکو ملاتړ دی.</p><p>مقالات د چاپ څخه مخکې د متخصصو څېړونکو لخوا ارزول کیږي او د عادلانه ارزونې اصول رعایت کیږي تر څو د مقالې علمي صحت، نوښت او کیفیت یقیني شي. مجله کولی شي په بېلابېلو علمي، فرهنګي، ټولنیزو یا ټکنالوژیکي برخو کې فعالیت وکړي او زده کوونکو، څېړونکو او د علم سره مینه والو ته تازه علمي موندنې او لاسته راوړنې وړاندې کړي.</p><p class=\"ql-align-justify\">سربېره پر دې، ځینې مجلې د ورکشاپونو، نقدونو او علمي یادښتونو خپرونه هم کوي تر څو د لوستونکو لپاره د زده کړې او څېړنې بهیر جذاب او ګټور وي. مجله په ټولنه کې د علمي کچې لوړولو او د څېړنیزو هڅو هڅولو کې مهم رول لري.</p><p><br></p>', NULL, '2025-11-29 08:07:57', '2025-12-04 05:05:52'),
(2, 'ارسال مقاله', 'مقاله لېږل', 'ارسال مقاله', 'مقاله لېږل', '<p class=\"ql-align-justify\"><strong style=\"color: red;\">برای ارسال مقاله نیاز به<u> </u></strong><a href=\"http://127.0.0.1:8000/registration-form\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: red;\"><strong><u>ثبت نام</u></strong></a><strong style=\"color: red;\"> و </strong><a href=\"login-form\" rel=\"noopener noreferrer\" target=\"_blank\" style=\"color: red;\"><strong>ورود</strong></a><strong style=\"color: red;\"> به سامانه است</strong></p><p class=\"ql-align-justify\"><strong style=\"background-color: white; color: rgb(102, 102, 102);\">چک لیست آماده سازی برای ارسال مقاله</strong></p><p class=\"ql-align-justify\"><span style=\"background-color: white; color: rgb(102, 102, 102);\">به‌عنوان مرحله‌ای از روند ارسال مقاله ، لازم است که نویسندگان از مهیا بودن اقلام مختلف برای ارسال مقاله اطمینان حاصل کنند. برای این کار لازم است که اقلام موجود در چک‌لیست زیر را بررسی نموده و در صورت مهیا بودن جلوی آن تیک بزنند</span></p><p class=\"ql-align-justify\"><br></p><p class=\"ql-align-justify\"><br></p>', '<p class=\"ql-align-justify\"><strong style=\"color: red;\">برای ارسال مقاله نیاز به ثبت نام و ورود به سامانه است</strong></p><p class=\"ql-align-justify\"><strong style=\"color: rgb(102, 102, 102);\">چک لیست آماده سازی برای ارسال مقاله</strong></p><p class=\"ql-align-justify\"><span style=\"color: rgb(102, 102, 102);\">به‌عنوان مرحله‌ای از روند ارسال مقاله ، لازم است که نویسندگان از مهیا بودن اقلام مختلف برای ارسال مقاله اطمینان حاصل کنند. برای این کار لازم است که اقلام موجود در چک‌لیست زیر را بررسی نموده و در صورت مهیا بودن جلوی آن تیک بزنند</span></p><p class=\"ql-align-justify\"><br></p>', NULL, '2025-12-01 05:12:29', '2025-12-07 13:23:03'),
(3, 'راهنمایی نویسنده گان', 'د لیکوالانو لپاره', 'راهنمایی نویسنده گان', 'د لیکوالانو لپاره', '<p class=\"ql-align-justify\"><strong style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">راهنمای نگارش و شرایط پذیرش مقاله‏</strong></p><p class=\"ql-align-justify\"><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">هدف اصلی مجله بین المللی اقتصاد و مدیریت کاتب، ارتقای تولید و تبادل دانش در حوزه علوم اقتصادی و مدیریتی و به طور خاص در حوزه‌های اقتصاد تجارتی، اداره تجارت و اداره عامه از طریق انتشار منظم و با کیفیت مقالات علمی، تسهیل همکاری‌های تحقیقاتی و پرداختن به مسائل و چالش‌های اقتصادی مرتبط با جوامع مختلف، به ویژه افغانستان، به منظور گسترش مرزهای دانش و ارائه راهکارهای علمی برای توسعه پایدار است. به این منظور، برای سرعت در داوری و انتشار به موقع مجله از کلیه استادان و محققانی که علاقه­‌مند به درج مطالب خویش در این نشریه هستند، درخواست می‌شود نکات زیر را رعایت کنند:</span></p><p class=\"ql-align-justify\"><strong style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">شرایط پذیرش مقاله</strong></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله دارای موضوع تحقیقی اصیل، تازه و حاصل مطالعات تحقیقی نویسنده یا نویسندگان باشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله قبلاً در نشریه دیگری به چاپ نرسیده، یا هم‌زمان برای نشریه دیگری ارسال نشده باشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">‌ مقاله با رعایت اصول اخلاق در تحقیق نگارش شده باشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله از منابع معتبر و به روز علمی استفاده نموده و امانت‌داری را در نقلِ گفته‌ها و نظریات ‌دیگران رعایت نموده و با ارجاع دقیق به منابعِ با رعایت شیوه‌نامۀ استنادی مورد قبول مجله همراه باشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله با رعایت حق مالکیت معنوی آثار علمی و قواعد کاپی‌رایت نگارش شده باشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">‌مسئولیت مطالب و محتوای مقاله برعهدۀ نویسنده/ نویسنده‌گان آن است؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در مقالات دارای بیش از یک نویسنده، یک نفر باید به عنوان نویسنده مسئول مشخص شود؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله در قالب فایل MS-Word بر اساسقالب مقالات نشریه، ارسال شود؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">همراه مقاله، فرم تعهد و فرم عدم تعارض منافع توسط نویسنده مسئول تکمیل و ارسال گردد.</span></li></ol><p class=\"ql-align-justify\"><strong style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">راهنمای نگارش و تنظیم مقاله</strong></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله از نظر ساختار شکلی، شامل بخش‌ها و اجزایی همچون عنوان،‌ نام و مشخصات نویسنده، چکیده، واژه‌های کلیدی، مقدمه،‌ مرور ادبیات تحقیق (ادبیات نظری و تجربی)، روش تحقیق، یافته‌های تحقیق، بحث و نتیجه‌گیری و فهرست منابع، ‌‌باشد.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مشخصات نویسنده، نویسندگان، شامل مواردی چون نام و نام خانودگی، رتبه علمی (پوهنیار، پوهنمل، پوهندوی، پوهنوال و پوهاند در افغانستان و برای نویسنده‌گانِ خارجی، رتبه‌های علمی رایج آنها)، وابستگی سازمانی (نام پوهنتون/دانشگاه، پوهنحی/دانشکده، دیپارتمنت، شهر، کشور محل تدریس یا تحقیق)، ایمیل آدرس و ترجیحا شناسه ارکید (ORCID) برای تمامی نویسندگان، می‌شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">ترتیب مشخصات نویسندگان در ذیل نام و نام‌خانوادگی آنها به زبان ملی و انگلیسی به شرح ذیل می‌باشد:</span></li></ol><p class=\"ql-align-justify\"><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">رتبه علمی، دیپارتمنت ........، پوهنحی/ دانشکده ......... ، پوهنتون/ دانشگاه ........، شهر، کشور)، آدرس ایمیل. شناسۀ علمی  (Orcid).</span></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">برای نویسند‌گانی که عضو کادر ‌علمی نیستند، از عنوان «محصل/دانشجو»، «ماستر یا دکتور» یا «استاد مدعو» با ذکرِ رشته، ‌مقطع تحصیلی و پوهنتون/ دانشگاه محل تحصیل و تدریس استفاده می‌شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقالات علمی محصلان ماستری، ‌باید بانظارت مؤثر یکی از اعضای کادر علمیِ یا یک دانش‌آموختۀ دکتورا، تکمیل و نام او به عنوان نویسنده مسئول درج شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در مقالات مستخرج از تیزس محصل، استاد راهنما، به عنوان نویسنده مسئول در نظر گرفته می‌شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">حجم مقاله ارسالی (شامل کلیه بخش‎‌های مقاله، اعم از چکیده انگلیسی، دری، منابع و...) از ۴۰۰۰ (چهار ‌هزار) واژه کم‌تر و ‌از ۷۵۰۰ (هفت‌هزار و پنج‌صد‌) واژه بیش‌تر نباشد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله همراه با چکیده انگلیسی و فارسی دری در حدود ۲۰۰ تا ۲۵۰ کلمه ارسال شود که شامل بیان زمینه مسأله، هدف و انگیزه، روش تحقیق (نوع رویکر روشی، نوع منابع یا جامعه آماری، حجم نمونه و روش‌نمونه‌گیری در صورت نیاز، نوع ابزار جمع‌آوری و روش تحلیل داده‌ها)، یافته‌های اصلی و نتیجه‌گیری تحقیق، باشد. (برای مقالاتی که به زبان‌ دری نوشته شده اند، ارسال عنوان، نام و مشخصات نویسندگان، چکیده و واژه‌های کلیدی به زبان انگلیسی نیز در صفحه جداگانه ضروری است).</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">هر شکلی از جمله گراف، چارت و سایر تصاویر باید دارای شماره و عنوان (توضیح) به‌صورت مرتب و متوالی باشد. عنوان یا توضیح باید به‌صورت وسط‌‌چین، در زیر شکل، درج شود. </span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">شکل‌ها، دیاگرام‌ها، و جدول‌ها در مقاله باید قابل ویرایش و تصحیح باشد و با کیفیت مطلوب ارسال شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">براي نمايش روابط و فرمول‌هاي رياضي از جدول دو ستوني با خطوط نامرئي استفاده شود. در ستون سمت راست جدول، شماره رابطه و در ستون سمت چپ رابطه يا فرمول مربوط نوشته شود. (استفاده از Microsoft Equation در نوشتن فرمول‌ها الزامی است.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">مقاله در نهایت و بعد از اصلاحات نهایی در قالب/تمپلت مقالات نشریهتهیه و تنظیم شود (قابل دسترس از اینجا).</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">منابع مورد استفاده، به ترتیب الفبای نام نویسندگان، در پایان مقاله و مطابق با دستورالعمل شیوه‌نامه انجمن روانشناسان آمریکا (APA Citation Style)  تنظیم شود.</span></li></ol><p class=\"ql-align-justify\"><strong style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">شیوه استناد درون متنی</strong></p><p class=\"ql-align-justify\"><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">تمامی استنادات درون متن مقاله باید به صورت درون‌متنی و با ذکر نام خانوادگی نویسنده/نویسندگان و سال انتشار باشد. در صورت لزوم، شماره صفحه نیز پس از دو نقطه ذکر شود. ارجاع‌های درون‌‌متنی بین دو قوس و بدین‌صورت تنظیم شود:</span></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">(نام خانواده‌گیِ نویسنده، ویرگول، سال نشر، دونقطۀ بیانی، شمارۀ صفحه).</span></li></ol><p class=\"ql-align-justify\"><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">برای استناد درون متنی به آثار چاپ شده از روش‌‌های زیر استفاده شود:</span></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">یک نویسنده: (نام خانوادگی، سال). مثال: (داودی، ۱۴۰۳)</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">دو نویسنده: (نام خانوادگی نویسنده اول و نام خانوادگی نویسنده دوم، سال). مثال: (داودی و عابد، ۱۴۰۳)</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">سه نویسنده یا بیشتر: برای منابع داخل متن، از واژه «دیگران» و «همکاران» استفاده نشود، بلکه تمام اسامی نویسندگان نوشته شود مگر اینکه نویسندگان بیشتر از ۳ نفر باشند. مثال: (تقوا، منصوری و فیضی، ۱۴۰۳)</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در صورتی که منبع جلدهای متعدّد داشته باشد، شمارۀ جلد بدون ذکر حرف ج، بک‌اسلش قبل از شماره صفحه ذکر گردد. مثال: (داودی، ۱۳۹۹: ۲/ ۱۵)</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در صورتی که از نویسنده‌ای دو یا چند اثر در یک سال منتشر‌شده باشد و از آنها در مقاله استفاده شده باشد، با قید الف، ب و... در کنار سال از یک‌دیگر تفکیک می‌شوند: (داودی، ۱۳۹۹ الف: ۴۵) و (داودی، ۱۳۹۹ب: ۳۲)؛</span></li></ol><p class=\"ql-align-justify\"><strong style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">قواعد تنظیم فهرست منابع در پایان مقاله</strong></p><ol><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">تمامی منابع باید بر اساس شیوه‌نامه رفرنس‌دهی APA (ویرایش ششم) ترتیب و تنظیم شود. تنها منابعی که در متن مقاله به آنها رفرنس داده شده است، باید در این بخش آورده شوند.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در فهرست منابع انگلیسی باید تمام ارجاعات فارسی‏ دری/ پشتو به انگلیسی ترجمه شود و عنوان کتاب و نام نشریه به صورت ایتالیک مشخص شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در درج و ترنیب مشخصات کتاب و مقاله و سایر منابع علمی مانند: تیزس (پایان‎نامه)، گزارش تحقیقی، آمار‌نامه‌های سازمان‎های دولتی و ...، از شیوه‎نامه APA استفاده شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">نام خانوادگی ونام نویسندگان به طور کامل نوشته شوند (نه به صورت اختصاری). در مورد نشریه‌‎‌ها اعم از فارسی‏ دری، پشتو و انگلیسی حتماً ترتیب نام نشریه (به صورت ایتالیک)، دوره، (شماره نشریه داخل پرانتز): صفحه انتهای مقاله خط تیره صفحه ابتدای مقاله رعایت شود.</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">فهرست منابع مورد استناد در فارسی ذیل کلمه «فهرست منابع» و در انگلیسی زیر کلمه \"References\" قرار گیرد؛</span></li><li data-list=\"bullet\" class=\"ql-align-justify\"><span class=\"ql-ui\" contenteditable=\"false\"></span><span style=\"background-color: rgb(255, 255, 255); color: rgb(102, 102, 102);\">در صورتی که از یک نویسنده چند اثر مورد استناد قرار گرفته باشد، منابع به ترتیب سال و از قدیم به جدید قرار گیرند.</span></li></ol><p><br></p>', NULL, NULL, '2025-12-01 05:33:04', '2025-12-02 02:49:44'),
(4, 'پیام رئیس پوهنتون وردک در مورد کنفرانس ', 'د کنفرانس په اړه د وردګ پوهنتون د رئیس پیغام', 'پیام رئیس پوهنتون وردک در مورد کنفرانس ', 'د کنفرانس په اړه د وردګ پوهنتون د رئیس پیغام', '<p class=\"ql-align-center\">بسم الله الرحمن الرحیم </p><p class=\"ql-direction-rtl ql-align-center\">الحمدلله وسلام علی عباده الذین اصطفی لاسیما علی سیدنا محمد و علی آلی و اصحابه اهل التقی و النقی</p><p class=\"ql-align-justify ql-direction-rtl\">امیرالمؤمنین حفظه‌الله تعالی ورعاه همواره به منظور رفاه، عزت و بیداری ملت، در پرتو شریعت اسلامی، فرامین و احکامی صادر کرده است که برای نظام اسلامی و ملت، جهت‌گیری‌های اساسی را مشخص می‌نماید و در مسیر چندین دهه جهاد و مبارزه، زمینه‌ساز رفع مشکلات و چالش‌هایی شده است که افغان­ها با آن‌ها مواجه بوده است.این احکام و فرامین نه‌تنها برای نظام و حکومت اسلامی، رهنمودهای تصمیم‌گیری به‌شمار می‌روند، بلکه برای تمام ابعاد جامعه، نقشۀ اساسی و اسلامی زندگی را ترسیم می‌کنند؛ نقشه‌ای که بنیان‌های حفاظت و توسعۀ شریعت، سیاست، اقتصاد، نظم اجتماعی، فرهنگ و هویت اسلامی در آن گنجانیده شده است. تمام فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه، از راهبردهای رهبری گرفته تا زندگی مردمی، بیداری فکری، وحدت ملی، استقلال اقتصادی، وقار فرهنگی و ارزش‌های دینی، افق‌های روشنی را برای ساختن یک ملت باوقار، متحد و اسلامی می‌گشایند. هر یک از این فرامین با حل مشکلات بی‌شمار و رهنمودهای شرعی پیوند خورده‌اند که لازم است به‌صورت علمی مورد بررسی و تحلیل قرار گیرند. بدین‌منظور، این همایش با عنوان «از رهبری اسلامی تا تحولات اجتماعی، سیاسی، اقتصادی، فرهنگی و شرعی؛ بررسی تأثیرات فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه» برگزار می‌گردد. هدف اساسی این نشست علمی، بررسی و تحلیل آثار، احکام و رهنمودهای صادرشده از سوی امیرالمؤمنین حفظه‌الله است. این همایش فرصت مناسبی را فراهم می‌آورد تا فرامین، احکام و هدایات صادرشده با در نظر گرفتن نیازها و چالش‌های جامعه، به‌گونه‌ای علمی مورد بررسی قرار گیرند و از رهگذر دانش، تحلیل و تبادل اندیشه، زمینه تطبیق عملی آن‌ها فراهم گردد. در نهایت، بر اساس رهبری مدبرانهٔ امارت اسلامی، تفکر ناب اسلامی و تجربیات تطبیقی، آینده‌ای روشن، مستقل، متحد و مبتنی بر اعتماد تضمین خواهد شد. در این کنفرانس، شخصیت‌های دینی، علمی، سیاسی و اجتماعی حضور خواهند یافت تا عمق علمی تأثیرات فرامین، بازتاب‌های اجتماعی آن‌ها و نقش‌شان در نهادینه‌سازی نظام را مورد تحلیل و بررسی قرار دهند. این نشست با هدف ارائه یک رویکرد روشن، مبتنی بر مبانی شریعت اسلامی و ملی برای زندگی امت اسلامی از طریق تحلیل علمی و عملی فرامین برگزار می‌شود.</p><p class=\"ql-align-center\">په درنښت</p><p class=\"ql-align-center\">شیخ القرآن والحدیث مولوی محمد عابد حقانی</p><p><br></p>', '<p class=\"ql-align-center\">بسم الله الرحمن الرحیم </p><p class=\"ql-direction-rtl ql-align-center\">الحمدلله وسلام علی عباده الذین اصطفی لاسیما علی سیدنا محمد و علی آلی و اصحابه اهل التقی و النقی</p><p class=\"ql-align-justify ql-direction-rtl\">د اسلامي امارت امیرالمؤمنین حفظه الله تعالی ورعاه د ملت د فلاح، عزت او بیدارۍ لپاره تل د اسلامي شریعت په رڼا کې داسې احکام او فرامین صادر کړي، چې د اسلامي نظام او ملت لپاره بنسټیزې تګلارې ټاکي او د څو لسیزو مجاهدې او مبارزې په لړ کې له افغاني ټولنې څخه د هغو ستونزو او مشکلاتو ټغر ټولوي، چې دا ملت ورسره لاس او ګریوان وو.&nbsp;دغه احکام او فرامین نه یوازې اسلامي نظام او حکومت لپاره د تصمیم‌نیونې حکمونه دي، بلکې د ټولنې د هر اړخ لپاره د ژوند اصلي او اسلامي نقشه وړاندې کوي، چې د شریعت، سیاست، اقتصاد، ټولنیز نظم، فرهنګ او اسلامي هویت د ساتنې او ودې بنسټونه په کې ځای پر ځای شوي دي. د امیرالمؤمنین حفظه الله تعالی ورعاه ټول فرمانونه د قیادت له ستراتیژۍ نیولې تر ولسي ژوند، فکري بیدارۍ، ملي یووالي، اقتصادي خپلواکۍ، فرهنګي وقار او دیني ارزښتونو پورې د یوه باوقاره، متحد او اسلامي ملت د جوړېدو روښانه افقونه پرانیزي، له هر یوه فرمان سره د بې شماره ستونزو حل او شرعي لارښوونې تړل شوې دي، چې په علمي توګه باید وڅېړل شي. د همدې لپاره د \"له اسلامي قیادت څخه تر ټولنیزو، سیاسي، اقتصادي، فرهنګي او شرعي پرمختګونو پورې د امیرالمؤمنین حفظه الله تعالی ورعاه د فرامینو د اغېزو\" تر سرلیک لاندې دغه کنفرانس په لاره اچول شوی دی، چې اساسي موخه یې د امیرالمؤمنین حفظه الله د فرمانونو، احکامو او لارښوونو د اغېزو څېړل دي، په دې توګه کنفرانس دا فرصت رامنځته کوي، ترڅو ټول احکام او فرامین د ټولنې ستونزو او اړتیاوو ته په کتو سره په علمي توګه و څېړل شي او د پوهې، تحلیل او فکري تبادلې له لارې یې تطبیق ته لاره هواره شي او د اسلامي امارت د مدبرانه قیادت، اسلامي مفکورې او تطبیقي تجربو پر بنسټ، یو بااعتماد، خودکفاء، متحد او روښانه راتلونکی تضمین شي. په دغه کنفرانس کې به دیني، علمي، سیاسي او ټولنیز شخصیتونه برخه واخلي، ترڅو د فرامینو د اثراتو علمي ژوروالی، ټولنیز انعکاس او د نظام‌ په جوړونه کې د هغو نقش تحلیل کړي او له دې لارې د اسلامي امت لپاره د ژوند یوه څرګنده، اسلامي او ملي تګلاره وړاندې کړي. </p><p class=\"ql-align-center\">  په درنښت   </p><p class=\"ql-align-center\">شیخ القران والحدیث مولوي محمد عابد حقاني</p><p><br></p>', NULL, '2025-12-04 02:35:43', '2025-12-14 00:02:12'),
(5, 'معرفی کنفرانس داخلی امیرالمؤمنین حفظه الله تعالی ورعاه ', 'د امیرالمؤمنین حفظه الله د فرامینو داخلي کنفرانس پېژندنه', 'معرفی کنفرانس داخلی امیرالمؤمنین حفظه الله تعالی ورعاه', 'د امیرالمؤمنین حفظه الله تعالی ورعاه د فرامینو داخلي کنفرانس پېژندنه', '<p class=\"ql-align-justify ql-direction-rtl\">عالیقدر امیرالمؤمنین حفظه الله تعالی و رعاه در روشني کلام الهی و احادیث نبوی په منظور تنظیم امور کشور در عرصه های مختلف فرامین، احکام و رهنمودهای صادر نموده اند، که از قیادت اسلامي تا ابعاد مختلف زندگی اجتماعی، بیداری فکری، وحدت ملی، استقلال اقتصادی، روابط بین الملل، عزت تاریخی و فرهنگی، ارزش های دینی و اسلامی افق های روشنی برای ساختن یک امت اسلامی، باوقار، متحد و متعهد می گشاید، در این کنفرانس، تمامی ابعاد فرامین با در نظر گرفتن شریعت، نیازهای جامعه، وضعیت کنونی، چالش‌ها و راه‌حل‌ها مورد بررسی علمی قرار خواهد گرفت و در نهایت، زمینه‌ تطبیق فرامین به‌عنوان رهنمودهای شرعی برای تأمین نظم، ثبات و پیشرفت جامعه فراهم خواهد شد. به همین منظور، پوهنتون وردک با حمایت وزارت تحصیلات عالی، کنفرانس داخلی فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه را برگزار کرده است تا علما، استادان، پژوهشگران و متخصصان را تشویق نماید که در زمینه‌ تطبیق و تأثیرات فرامین پژوهش­های انجام دهند و از طریق مقالات علمی، ابعاد و پیامدهای آن را برجسته سازند. این اقدام از یک سو آگاهی عمومی نسبت به فرامین را افزایش می‌دهد و از سوی دیگر، مسئولین امارت، مردم و علما را به تطبیق آن‌ها متوجه می‌سازد. بر این اساس، از علما، پژوهشگران، استادان پوهنتون­ها و متخصصان دعوت به عمل آمده است تا در زمینه‌های مختلف، تأثیرات فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه را مورد بررسی قرار دهند و از طریق مقالات علمی نشان دهند که این فرامین تا چه اندازه در حل مشکلات جامعه مؤثر واقع شده‌اند. تمام امور علمی، اداری و فرهنگی این کنفرانس از سوی پوهنتون وردک با حمایت ریاست تحقیق، تالیف و ترجمه وزارت تحصیلات عالی پیش برده می‌شود و انتظار می‌رود این کنفرانس در آینده‌ی نزدیک برگزار گردد.</p><p class=\"ql-align-justify ql-direction-rtl\"><br></p>', '<p class=\"ql-align-justify ql-direction-rtl\">د اسلامي امارت عالیقدر امیرالمؤمنین حفظه الله په بېلابېلو برخو کې د چارو د تنظیم په موخه د الهي کلام او نبوي احادیثو په رڼا کې فرامین، احکام او لارښوونې صادر کړې دي، چې د اسلامي قیادت له ستراتیژۍ نیولې تر ولسي ژوند، فکري بیدارۍ، ملي یووالي، اقتصادي خپلواکۍ، نړیوالو اړیکو، تاریخي او فرهنګي وقار او دیني ارزښتونو پورې د یوه باوقاره، متحد او اسلامي اُمت د جوړېدو روښانه افقونه پرانیزي. د امیرالمؤمنین فرمانونه د اسلامي شریعت مطابق د اسلامي او عادلانه نظام ټول اړخونه رانغاړي او په حقیقت کې د نظام لپاره د ثبات اساسي بنسټ جوړوي. په دې کنفرانس کې به د فرمانونو ټول اړخونه د شریعت، اړتیا، اوسني وضعیت، ستونزو او حل لارو په پام کې نیولو سره وڅېړل شي او په پایله کې به د شرعي لارښوونو په توګه د ټولنې د نظم، ثبات او پرمختګ په موخه د فرمانونو تطبیق ته لاره پرانیستل شي. </p><p class=\"ql-align-justify ql-direction-rtl\">له همدې امله وردګ پوهنتون د لوړو زده کړو وزارت په ملاتړ د امیرالمؤمنین حفظه الله تعالی ورعاه د فرمانونو داخلي کنفرانس په لاره اچولی، ترڅو علماء، استادان، څېړونکي او متخصصین وهڅوي، چې د فرمانونو پر تطبیق او اغېزو باندې څېړنې ترسره او د علمي مقالو له لارې یې اغېزې رابرسېره کړي. دا کار به له یوې خوا د فرمانونو په اړه عمومي پوهاوی رامنځته کړي او له بلې خوا به امارتي مسؤلین، خلک او علماء د هغوی تطبیق ته هم متوجه کړي. په دې توګه علماوو، څېړونکو، د پوهنتونونو استادانو او متخصصینو ته بلنه ورکړل شوې ده، ترڅو په بېلابېلو برخو کې د امیرالمؤمنین حفظه الله تعالی ورعاه د فرمانونو د اغېزو په اړه په څېړنې وکړي او د علمي مقالو له لارې وښيي، چې فرامینو تر کومې کچې د ټولنې د ستونزو په حل کې اغېزمن واقع شوي دي. د دغه کنفرانس ټول علمي، اداري او فرهنګي چارې د لوړو زده کړو وزارت د ژباړې، تألیف او ترجمې ریاست په ملاتړ وردګ پوهنتون پرمخ وړي او تمه ده، چې دغه کنفرانس په نږدې راتلونکي کې په لاره واچول شي.&nbsp;</p><p><br></p>', NULL, '2025-12-13 05:59:29', '2025-12-13 05:59:29'),
(6, 'ضرورت و اهمیت کنفرانس', 'د کنفرانس اړتیا او ارزښت', 'ضرورت و اهمیت کنفرانس', 'د کنفرانس اړتیا او ارزښت', '<p>کنفرانس تحت عنوان «از رهبری اسلامی تا پیشرفت‌های اجتماعی، سیاسی، اقتصادی، فرهنگی و شرعی در پرتو فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه» یکی از نیازهای اساسی و مهم در سطح کشور در عصر حاضر به‌شمار می‌رود. فرامین امیرالمؤمنین که مطابق با شریعت اسلامی صادر شده‌اند، تمامی ابعاد یک نظام اسلامی و عادلانه را در بر می‌گیرند و در واقع، بنیادهای ثبات و پایداری نظام را شکل می‌دهند. در این کنفرانس، تمامی ابعاد فرامین با درنظر گرفتن شریعت اسلامي، نیازهای جامعه، وضعیت کنونی، چالش‌ها و راه‌حل‌ها مورد بررسی علمی قرار خواهند گرفت و در نتیجه، این فرامین به‌عنوان رهنمودهای شرعی، نقش اساسی در نظم، ثبات و پیشرفت جامعه ایفا خواهند کرد. فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه، پیام‌آور وحدت، اطاعت، تقوا، استقلال و اخلاق برای امت اسلامی‌اند. این کنفرانس ارزش‌های شرعی، اجتماعی و تاریخی این اصول را تبیین خواهد کرد و جامعه را از پراکندگی فکری، تعصبات قومی و خشونت، به‌سوی اعتدال، وحدت و روشنی فکری سوق خواهد داد. علاوه بر این، تمام اقشار جامعه دسترسی درستی به مفاهیم و اهداف فرامین ندارند. برای نسل های جوان ضروری است که با مبانی شرعی و حکمت تصمیم‌گیری رهبری نظام اسلامی آشنا باشند. مقالات علمی و تحلیل‌های این کنفرانس، تمامی ابهامات را روشن ساخته، ارزش شرعی، سیاسی و اجتماعی فرامین را ثابت خواهند کرد و نسل­های نو را به جای دینداری تقلیدی، با دینداری آگاهانه آشنا خواهند ساخت. این کنفرانس نه تنها در سطح ملی به بررسی تأثیرات فرامین موجود خواهد پرداخت، بلکه بسیاری از نیازهای دیگر جامعه را نیز شناسایی خواهد کرد که می‌توانند در پرتو فرامین جدید مبتنی بر شریعت اسلامی برآورده شوند. توضیح فلسفه فکری امارت اسلامی برای محافل علمی و سیاسی در سطح جهانی از نیازهای اساسی زمان حاضر به شمار می‌رود، چرا که در حال حاضر چالش‌ها، فرصت‌ها و اصلاحات مرتبط با تطبیق فرامین باید به‌گونه‌ای علمی مورد بررسی قرار گیرند تا بتوان از شکل‌گیری تصورات و تحلیل‌های منفی‌ای جلوگیری کرد که در حال حاضر در سطح جهانی مطرح می‌گردند. در نتیجه، روشن خواهد شد که رهبری امارت اسلامی بر پایهٔ علم، تقوا، تجربه، اخلاص و بصیرت فکری استوار است. این کنفرانس نشان خواهد داد که فرامین از نهادهای منظم، اصول فقهی، مشورت‌ها و دیدگاه‌های عمیق فکری و شرعی سرچشمه می‌گیرند و راه را برای نظام عادلانه، استقلال اقتصادی، عزت فرهنگی، تطبیق شریعت و نظم و ثبات اجتماعی هموار می‌سازند.</p><p><br></p>', '<p>له اسلامي قیادت څخه تر ټولنیزو، سیاسي، اقتصادي، کلتوري او شرعي پرمختګونو پورې د امیرالمؤمنین حفظه الله تعالی ورعاه د فرمانونو د اغېزو تر سرلیک لاندې کنفرانس د هېواد په کچه د اوسني عصر یو له مهمو اړتیاوو څخه ګڼل کیږي. د امیرالمؤمنین فرمانونه د اسلامي شریعت مطابق د اسلامي او عادلانه نظام ټول اړخونه رانغاړي او په حقیقت کې د نظام لپاره د ثبات بنسټونه جوړوي. په دې کنفرانس کې به د فرمانونو ټول اړخونه د شریعت، اړتیا، اوسني وضعیت، ستونزو او حل لارو په پام کې نیولو سره وڅېړل شي او په پایله کې به فرمانونه د شرعي لارښوونو په توګه د ټولنې د نظم، ثبات او پرمختګ لپاره پرانیستونکي ثابت شي.</p><p>د امیرالمؤمنین حفظه الله تعالی ورعاه فرامین اُمت ته د یووالي، اطاعت، تقوا، خپلواکي او اخلاقو پیغام رسوونکي دي، دا کنفرانس به د دغو اصولو شرعي، ټولنیز او تاریخي ارزښتونه څرګند کړي، او ټولنه به له فکري تشتت، قومي تعصب او تشدد څخه اعتدال، یووالي او فکري روښانتیا ته سوق کړي. پر دې سربېره په ټولنه کې ټول خلک د فرامینو مفاهیمو او اهدافو ته سم لاس‌رسی نه لري، ځوان نسل ته دا مهمه ده چې د اسلامي نظام د مشرتابه د تصمیم نیونې له شرعي بنسټ او حکمت څخه خبر واوسي، د دغه کنفرانس علمي مقالې او شننې به ټول ابهامونه روښانه کړي او د فرامینو شرعي، سیاسي او ټولنیز ارزښت به ثابت کړي او نوی نسل به د تقلیدي دین‌پالنې پرځای له شعوري دین پالنې سره آشنا شي. دغه کنفرانس به نه یوازې په ملي کچه د موجودو فرمانونو پر اغېزو بحث وکړي، بلکې د ټولنې ډېرې نورې اړتیاوې به هم رابرسېره کړي، چې د اسلامي شریعت پر بنا د لا نورو فرمانونو په رڼا کې پوره کېدلی شي. د نړۍ په کچه علمي او سیاسي حلقو ته د اسلامي امارت د فکري فلسفې وضاحت د اوس وخت له مهمو اړتیاوو څخه ګڼل کيږي، ځکه اوسمهال د فرمانونو د تطبیق په وړاندې شته ستونزې، فرصتونه او اصلاحات باید علمي وڅېړل شي او په دې توګه د هغو منفي انځورونو او تحلیلونو مخه نیول کېدی شي، چې سمدلاسه د نړۍ په کچه مطرح دي، په پایله کې به دا واضحه شي چې د اسلامي امارت قیادت د علم، تقوا، تجربې، اخلاص او فکري بصیرت پر اساس ولاړ دی، دا کنفرانس به وښيي چې فرامین له منظمو ادارو، فقهي اصولو، مشورو او ژور فکري او شرعي لید څخه سرچینه اخلي او د عادلانه نظام، اقتصادي استقلالیت، فرهنګي عزت، د شریعت تطبیق او ټولنیز نظم او ثبات ته لاره پرانیزي.</p>', NULL, '2025-12-13 06:01:53', '2025-12-20 03:38:15'),
(7, 'اهداف کنفراس', 'د کنفرانس موخې', 'اهداف کنفراس', 'د کنفرانس موخې', '<p class=\"ql-align-justify\">هدف اصلی و اساسی این کنفرانس، تشریح و تبیین تمام اساسات و بنیادهای اسلامی و شرعی برای عملکردهای جامعه و نظام است. مسئولیت امیرالمؤمنین حفظه‌الله تعالی و رعاه این است که پیش از همه چیز، احکام الهی را بر جامعه و مردم تطبیق نماید. برای رسیدن به این هدف، امیرالمؤمنین حفظه الله در بخش­های مختلف فرامین صادر نموده‌اند و هر فرمان در پرتو کلام الهی و احادیث نبوي تشریح گردیده است. اگر به‌طور خلاصه بگوییم، اهداف این کنفرانس را می‌توان به شکل زیر جمع‌بندی کرد:</p><p class=\"ql-align-justify\">۱- تحلیل تأثیرات شرعی، اجتماعی، سیاسی، اقتصادی و فرهنگی فرامین: فرامین امیرالمؤمنین نمایانگر یک نظام اسلامی، با ثبات و مستقل‌اند و بر ابعاد مختلف جامعه تأثیر می‌گذارند. یکی از اهداف این کنفرانس آن است که تأثیرات فرامین امیرالمؤمنین حفظه‌الله در ابعاد گوناگونی همچون نظم اجتماعی، عدالت، پیشرفت اقتصادی، روابط سیاسی، شفافیت، بیداری دینی و هویت فرهنگی تبیین گردد و یک گفت‌وگوی ملی برای تطبیق عملی آن‌ها شکل گیرد.</p><p class=\"ql-align-justify\">۲- تقویت اعتماد میان ملت و رهبری امارت اسلامی: تحلیل، تبیین و بازتاب تأثیرات فرامین در جامعه باعث خواهد شد که مردم به حکمت، فلسفه و بنیاد شرعی این فرامین پی ببرند و بدین‌گونه فضای اعتماد میان ملت و رهبری نظام اسلامی بیش از پیش گسترش یابد.</p><p class=\"ql-align-justify\">۳- حمایت و تحلیل از سیاست‌های امارت اسلامی: یکی از اهداف این کنفرانس، تحلیل سیاست‌های نظام اسلامی در پرتو ارشادات و فرامین امیرالمؤمنین حفظه‌الله است، تا با جلب مشورت‌ها، پیشنهادها و حمایت نخبگان علمی، زمینه تقویت این سیاست‌ها فراهم گردد.</p><p class=\"ql-align-justify\">۴- تبیین جهانی تصویر فکری حاکمیت اسلامی: فرامین، بازتابی از اندیشه، بصیرت، تقوا و احساس مسئولیت امیرالمؤمنین حفظه‌الله هستند. در این کنفرانس، ماهیت، چشم‌انداز عمیق و مبانی شرعی رهبری اسلامی در رابطه با تمامی ابعاد جامعه تبیین خواهد شد و از این رهگذر، امارت اسلامی به‌عنوان یک نظام شرعی و اسلامی، به‌گونه‌ای علمی به جامعه جهانی معرفی می‌گردد.</p><p class=\"ql-align-justify\">۵- ایجاد مباحثات ملی درباره تأثیرات فرامین میان شخصیت‌های دینی، علمی و مسلکي: این کنفرانس چارچوب علمی فراهم می‌کند تا فضای تبادل نظر، همکاری و همفکری میان علما، اساتید، پژوهشگران و فرهنگیان ایجاد شود و بدین ترتیب از طریق بحث‌های علمی، حرفه‌ای و تحقیقاتی پیرامون فرمان‌ها، راه‌حل‌هایی برای چالش‌هایی که جامعه طی چندین دهه با آن مواجه بوده، ارائه گردد.</p><p><br></p>', '<p>د دغه کنفرانس اصلي او اساسي موخه د ټولنې او نظام د ټولو کړنو لپاره د اسلامي او شرعي اساس او بنیاد تشریح کول دي، د امیرالمؤمنین حفظه الله تعالی و رعاه دا مسؤلیت دی، چې تر هرڅه وړاندې پر ټولنه او خلکو باندې الهی احکام تطبیق کړي، دغې موخې ته د رسیدو لپاره فرمانونه ورکړل شوي او هر فرمان د قرآن او احادیثو په رڼا کې تشریح شوی دی، که په لنډه توګه ووایو د دغه کنفرانس موخې په لاندې ډول خلاصه کولی شو:</p><p>۱- د فرمانونو د شرعي، ټولنیزو، سیاسي، اقتصادي او فرهنګي اغېزو تحلیل: د امیرالمؤمنین فرامین د یو اسلامي، باثباته او خپلواک نظام استازیتوب کوي او د ټولنې پر بېلابېلو ابعادو اغېزه لري، د دې کنفرانس یوه موخه دا ده، چې په بېلابېلو ابعادو لکه ټولنیز نظم، عدالت، اقتصادي پرمختګ، سیاسی اړیکو، شفافیت، دیني شعور او فرهنګي هویت په برخو کې د امیرالمؤمنین حفظه الله د فرامینو اغېزې څرګندې او د تطبیق لپاره یې یوه ملي مباحثه رامنځته شي. </p><p>۲- د ولس او اسلامي امارت د قیادت ترمنځ د اعتماد پیاوړتیا: د فرامینو تحلیل، تشریح او په ټولنه کې د هغو اغېزې رابرسېره کول به د دې لامل شي، چې ولس د فرامینو حکمت، فلسفه او شرعي اساس درک کړي او په دې توګه به د ولس او اسلامي نظام د قیادت ترمنځ د اعتماد فضاء نوره هم پراخه شي. </p><p>۳- د اسلامي امارت د تګلارو ملاتړ او تحلیل: د کنفرانس یوه موخه دا ده چې د امیرالمؤمنین حفظه الله د ارشاداتو او فرمانونو په رڼا کې د اسلامي نظام پالیسي تحلیل کړي، او د پیاوړتیا لپاره یې د علمي قشر مشورې، وړاندیزونه او ملاتړ ترلاسه شي. </p><p>۴- د اسلامي حاکمیت د فکري تصویر نړيواله تشرېح: فرامین د امیرالمؤمنین حفظه الله د فکر، بصیرت، تقوا، او مسؤلیت څرګندونه کوي، په دغه کنفرانس کې به د ټولنې د ټولو اړخونو په اړه د اسلامی قیادت ماهیت، ژورلید او شرعي بنسټونه روښانه شی، او په دې توګه به اسلامي امارت د یو شرعي او اسلامي نظام په توګه په علمي بڼه نړېوالو ته معرفي شي. </p><p>۵- د دیني، علمي او مسلکی شخصیتونو ترمنځ د فرمانونو د اغېزو په اړه د ملي مباحثو رامنځته کول: دا کنفرانس به یو علمي چوکاټ برابر کړي چې د علماوو، استادانو، محققینو او فرهنګیانو تر منځ د فکرونو د تبادلې، همکارۍ او همفکرۍ فضاء رامنځته کړي او په دې توګه به د فرامینو په اړه د علمي، مسلکي او څېړنیزو بحثونو له لارې هغو ننګونو ته حل لارې پیدا شي، چې ټولنه له څو لسیزو راهیسې ورسره لاس او ګریوان ده.</p><p><br></p>', NULL, '2025-12-13 06:03:37', '2025-12-20 03:32:58'),
(8, 'محورهای اصلی کنفرانس', 'د کنفرانس اصلي محورونه', 'محورهای اصلی کنفرانس', 'د کنفرانس اصلي محورونه', '<h4><strong style=\"color: windowtext;\">الف. محورهای اصلی کنفرانس:</strong></h4><p class=\"ql-align-justify\">1.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین امیرالمؤمنین حفظه الله در عرصه‌های امر بالمعروف و نهی از منکر، نظام جزائی، بیداری فکری ملت، اصلاح اخلاقی، تزکیه فکری، استقلال فکری و حفظ هویت تاریخی.</p><p class=\"ql-align-justify\">2.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین امیرالمؤمنین حفظه الله در حفظ مسؤلیت­های اسلامی و افغانی رسانه‌ها، مبارزه با تبلیغات دروغین و تهاجم فکری رسانه‌ای، حفظ استقلال فرهنگی و مقابله با تهاجم فرهنگی.</p><p class=\"ql-align-justify\">3.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین امیرالمؤمنین حفظه الله در ساختار نظام اسلامی، مشروعیت سیاسی، سیاست خارجی، اصول فکری تعامل بین‌المللی و وحدت امت اسلامی، و مقایسه آنها با نمونه‌های تاریخی در پرتو تاریخ اسلام.</p><p class=\"ql-align-justify\">4.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین امیرالمؤمنین حفظه الله در عدالت اقتصادی، بیت‌المال، خودکفایی ملی، معادن، زمین‌های غصب‌شده، پروژه‌های ملی، منابع طبیعی، آب‌ها و جنگل‌ها، بانکداری اسلامی و تجارت.</p><p class=\"ql-align-justify\">5.&nbsp;&nbsp;&nbsp;&nbsp;فرامین امیرالمؤمنین حفظه الله درباره مکلفیت‌های شرعی نهادهای امنیتی در جلوگیری از جرایم به‌منظور تأمین امنیت و آرامش اجتماعی.</p><p class=\"ql-align-justify\">6.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین و رهنمودهای امیرالمؤمنین حفظه الله در ایجاد ساختارهای اداری مناسب براساس ضرورت‌ها و ارائه خدمات عامه به روش‌های کوتاه و ساده.</p><p class=\"ql-align-justify\">7.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات ارشادات امیرالمؤمنین <strong>حفظه الله </strong>در اصلاح نصاب‌های مکاتب و پوهنتون‌ها در چارچوب اسلامی و شرعی و حفظ ارزش‌ها و افکار اسلامی در آنها.</p><p class=\"ql-align-justify\">8.&nbsp;&nbsp;&nbsp;&nbsp;تأثیرات فرامین امیرالمؤمنین حفظه الله در استفاده مشروع از وسایل عصری و کنترول رسانه‌های اجتماعی و فضای دیجیتال.</p><h4><strong style=\"color: windowtext;\">ب. محورهای فرعی کنفرانس:</strong></h4><p><strong>۱.۱ هماهنگی بین نظام جزائی و امر بالمعروف و نحوه اجرای شریعت</strong></p><p><strong>۲.۱ نقش امر بالمعروف در بیداری فکری ملت و اصلاح اخلاقی</strong></p><p><strong>۳.۱ &nbsp;راهبردهای اساسی امر بالمعروف برای تزکیه فکری و حفظ هویت تاریخی</strong></p><p><strong>۱.۲ رهنمودهای امیرالمؤمنین حفظه الله درباره مسئولیت‌های رسانه‌ای اسلامی و افغانی</strong></p><p><strong>۲.۲ روش‌های مبارزه با تبلیغات دروغین و تهاجم فکری رسانه‌ای</strong></p><p><strong>۳.۲ راهبردهای حفظ استقلال فرهنگی و جلوگیری از تهاجم فرهنگی</strong></p><p><strong>۱.۳ ساختار نظام اسلامی و مشروعیت سیاسی در پرتو فرامین</strong></p><p><strong>۲.۳ &nbsp;اصول فکری سیاست خارجی و تعامل بین‌المللی</strong></p><p><strong>۳.۳ نمونه‌های تاریخی وحدت امت اسلامی و مقایسه با فرامین امیرالمؤمنین حفظه الله</strong></p><p><strong>۱.۴ اصول شرعی حفظ و مصرف بیت‌المال، معادن و منابع طبیعی</strong></p><p><strong>۲.۴ مدیریت خودکفایی ملی، زمین‌های غصب‌شده و پروژه‌های ملی</strong></p><p><strong>۳.۴ توسعه بانکداری و تجارت اسلامی و تأمین عدالت</strong></p><p><strong>۱.۵ نحوه مسئولیت‌های شرعی ارگان‌های امنیتی</strong></p><p><strong>۲.۵ تطبیق مکانیزم‌های عدلی اسلامی برای جلوگیری از جرایم</strong></p><p><strong>۳.۵ راهبردهای عملی برای تأمین آرامش و امنیت اجتماعی</strong></p><p><strong>۱.۶ ایجاد ساختارهای اداری مناسب بر اساس نیاز</strong></p><p><strong>۲.۶ ارائه خدمات عمومی از طریق راه‌های کوتاه و ساده</strong></p><p><strong>۳.۶ &nbsp;اصول بنیادین شفافیت و عدالت اسلامی در امور اداری</strong></p><p><strong>۱.۷ اصلاح اسلامی و شرعی نصاب‌های آموزشی</strong></p><p><strong>۲.۷ &nbsp;حفظ ارزش‌ها و افکار اسلامی در مؤسسات آموزشی</strong></p><p><strong>۳.۷ مراحل عملی اصلاح نصاب‌های مکاتب و پوهنتون‌ها</strong></p><p><strong>۱.۸ &nbsp;اصول استفاده مشروع از وسایل مدرن</strong></p><p><strong>۲.۸ راه‌های کنترول و مدیریت صحیح رسانه‌های اجتماعی</strong></p><p><strong>۳.۸ &nbsp;راهبردهای استفاده از فضای دیجیتال و کنترول اخلاقی آن</strong></p><p><br></p>', '<h4><strong style=\"color: windowtext;\">الف. د کنفرانس اصلي محورونه:</strong></h4><p class=\"ql-align-justify\">۱. د امربالمعروف او نهې عن المنکر د قانون، جزائي نظام، د ملت فکري ویښتابه، اخلاقي اصلاح، فکري تزکیې، فکري استقلال او تاریخي هويت ژغورنې په برخو کې د امیر المؤمنین حفظه الله د فرمانونو اغېزې. </p><p class=\"ql-align-justify\">۲. د رسنیو اسلامي او افغاني مسؤلیت ساتلو، دروغجنو تبلیغاتو او رسنیز فکري یرغل په وړاندې د مبارزې کولو، فرهنګي خپلواکۍ ساتلو او فرهنګي یرغل توږلو په برخه کې د امیرالمؤمنین حفظه الله د فرمانونو اغېزې </p><p class=\"ql-align-justify\">۳. د اسلامي نظام د جوړښت، سیاسي مشروعیت، بهرني سیاست، د نړیوال تعامل فکري اصولو او د اسلامي امت د یووالي په برخو کې د امیرالمؤمنین حفظه الله د فرمانونو اغېزې او د اسلامي تاریخ په رڼا کې د هغو مقایسه او تاریخي نمونې. </p><p class=\"ql-align-justify\">۴. د اقتصادي عدالت، بیت المال، ملي ځان بسیاینې، کانونو، غصب شوو ځمکو، ملي پروژو، طبیعي زیرمو، اوبو او ځنګلونو، اسلامي بانکدارۍ او سوداګرۍ په برخو کې د امیرالمؤمنین حفظه الله د فرمانونو اغېزې</p><p class=\"ql-align-justify\">۵. د ټولنیز امن او سکون په موخه د جرایمو د مخنیوي په برخه کې د امنیتي ارګانونو د شرعي مکلفیتونو په اړه د امیر المؤمنین حفظه الله فرمانونه.</p><p class=\"ql-align-justify\">۶. له اړتیا سره سم د مناسبو اداري جوړښتونو رامنځته کولو، په لنډو او ساده لارو د عامه خدمتونو وړاندې کولو په برخه کې د امیرالمؤمنین حفظه الله د فرمانونو او لارښوونو اغېزې.&nbsp;</p><p class=\"ql-align-justify\">۷. په اسلامي او شرعي چوکاټ کې د ښوونځیو او پوهنتونونو د نصابونو اصلاح او په هغو کې د اسلامي ارزښتونو او افکارو د ساتنې په اړه د امیرالمؤمنین حفظه الله د ارشاداتو اغېزې</p><p class=\"ql-align-justify\">۸. د عصري وسائلو د مشروع استعمال، د ټولنیزو رسنیو او ډیجیټل فضا د کنټرول په برخه کې د امیرالمؤمنین حفظه الله د فرمانونو اغېزې.&nbsp;</p><h4><strong style=\"color: windowtext;\">ب. د کنفرانس فرعي محورونه:</strong></h4><p class=\"ql-align-justify\">۱.۱ د جزائی نظام او امر بالمعروف تر منځ همغږي او د شریعت د تنفیذ څرنګوالی</p><p class=\"ql-align-justify\">۲.۱ د ملت فکري ویښتابه او د اخلاقي اصلاح په برخه کې د امربالمعروف رول</p><p class=\"ql-align-justify\">۳.۱ د فکري تذکیې او تاریخي هویت ژغورنې لپاره د امربالمعروف بنسټیزې تګلارې</p><p class=\"ql-align-justify\">۱.۲ د اسلامي او افغاني رسنیزو مسؤلیتونو په اړه د امیر المؤمنین حفظه الله لارښوونې</p><p class=\"ql-align-justify\">۲.۲ د دروغجنو تبلیغاتو او رسنیز فکري یرغل په وړاندې د مبارزې میتودونه</p><p class=\"ql-align-justify\">۳.۲ د فرهنګي خپلواکۍ ساتلو او فرهنګي یرغل د مخنیوي تګلارې</p><p class=\"ql-align-justify\">۱.۳ د اسلامي نظام جوړښت او سیاسي مشروعیت د فرمانونو په رڼا کې </p><p class=\"ql-align-justify\">۲.۳ د بهرني سیاست او نړیوال تعامل فکري اصول </p><p class=\"ql-align-justify\">۳.۳ د اسلامي امت د یووالي تاریخي نمونې او د امیرالمؤمنین حفظه الله د فرمانونو تاریخي مقایسه </p><p class=\"ql-align-justify\">۱.۴ &nbsp;د بیت المال، کانونو او طبیعي زیرمو د ساتنې او مصرف شرعي اصول</p><p class=\"ql-align-justify\">۲.۴ &nbsp;د ملي ځان بسیاینې، غصب شوو ځمکو او ملي پروژو مدیریت </p><p class=\"ql-align-justify\">۳.۴ &nbsp;د اسلامي بانکدارۍ او سوداګرۍ د پراختیا او عدالت تامین </p><p class=\"ql-align-justify\">۱.۵ د امنیتي ارګانونو د شرعي مکلفیتونو څرنګوالی</p><p class=\"ql-align-justify\">۲.۵ د جرایمو د مخنیوي لپاره د اسلامي عدلي میکانیزمونو پلي کول</p><p class=\"ql-align-justify\">۳.۵ د ټولنیز سکون او امن د ټینګښت لپاره عملي تګلارې</p><p class=\"ql-align-justify\">۱.۶ &nbsp;د اړتیا سره سم د مناسبو اداري جوړښتونو رامنځته کول</p><p class=\"ql-align-justify\">۲.۶ په لنډو او ساده لارو د عامه خدمتونو وړاندې کول</p><p class=\"ql-align-justify\">۳.۶ د شفافیت او اسلامي عدالت بنسټیز اصول په اداري چارو کې </p><p class=\"ql-align-justify\">۴.۶ د ښوونیزو نصابونو اسلامي او شرعي اصلاح </p><p class=\"ql-align-justify\">۱.۷ په تعلیمي ادارو کې د اسلامي ارزښتونو او افکارو ساتنه </p><p class=\"ql-align-justify\">۲.۷ د ښوونځیو او پوهنتونونو د نصابونو د اصلاح عملي مرحلې</p><p class=\"ql-align-justify\">۱.۸ د عصري وسائلو د مشروع استعمال اصول</p><p>۲.۸ د ټولنیزو رسنیو د کنټرول او سم مدیریت لارې&nbsp;</p><p class=\"ql-align-justify\">۳.۸ د برښنائي فضا د استفادې او اخلاقي کنټرول تګلارې.&nbsp;</p><p><br></p>', NULL, '2025-12-13 06:08:28', '2025-12-13 06:26:24'),
(9, 'امتیازات اشتراک در کنفرانس', 'په کنفرانس کې د ګډون امتیازونه', 'امتیازات اشتراک در کنفرانس', 'په کنفرانس کې د ګډون امتیازونه', '<p><strong>1.&nbsp;&nbsp;&nbsp;&nbsp;مقالات منتخب درجه اول با حمایت وزارت تحصیلات عالی در یکي از ژورنال­های بین‌المللی منتشر خواهند شد. </strong></p><p>2.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>مقالات آنعده از علماء که در ازمون ماستري موفق شده، اما تا اکنون رساله­های خود را تکمیل نکرده­اند، مقالات پذیرفته شدۀ این کنفرانس برایشان معادل رساله محسوب می­گردد.</strong></p><p><strong>3.&nbsp;&nbsp;&nbsp;&nbsp;مقالات درجه دوم در ژورنال علمی – تحقیقی علوم اجتماعی پوهنتون وردک (وردیکا) به نشر خواهد رسید.</strong></p><p><strong>4.&nbsp;&nbsp;&nbsp;&nbsp;مقالات درجه سوم در مجموعۀ عمومی کنفرانس نشر خواهد شد.</strong></p><p><strong>5.&nbsp;&nbsp;&nbsp;&nbsp;برای ارائه‌دهندگان مقالات، محل بود و باش و مصارف در جریان کنفرانس در نظر گرفته شده است.</strong></p><p><strong>6.&nbsp;&nbsp;&nbsp;&nbsp;برای نویسندگان و پژوهشگران برتر، جوایز نقدی و غیرنقدی در نظر گرفته شده است.</strong></p><p><br></p>', '<p class=\"ql-align-justify\">1. لومړۍ درجه غوره شوې مقالې به د لوړو زده کړو وزارت په ملاتړ په یوه نړیوالو ژورنال کې خپرې شي. </p><p class=\"ql-align-justify\">2. د هغو علماوو مقالې چې د ماسترۍ په ازموینو کې بریالي شوي، خو رسالې یې تر دې مهاله نه دي تکمیل کړې، د دې کنفرانس لومړۍ درجه منل شوې مقالې به یې د رسالې معادل محاسبه شي. </p><p class=\"ql-align-justify\">3. دوهمه درجه مقالې به د وردګ پوهنتون د ټولنیزو علومو په علمي ـ تحقیقي ژورنال (وردیکا) کې خپرې شي. </p><p class=\"ql-align-justify\">4.&nbsp;درېیمه درجه مقالې به د کنفرانس په عامه مجموعه کې خپرې شي. </p><p class=\"ql-align-justify\">5. د مقالو د ارائه کوونکو لپاره د کنفرانس په جریان کې د اوسېدو ځای او لګښتونه په پام کې نیول شوي دي. </p><p class=\"ql-align-justify\">6.&nbsp;د غوره مقاله لیکونکو او څېړونکو لپاره نقدي او غیرنقدي ډالۍ په پام کې نیول شوې دي.&nbsp;</p><p><br></p>', NULL, '2025-12-13 06:31:49', '2025-12-13 06:31:49'),
(10, 'نتایج متوقعه', 'متوقعه پایلې', 'نتایج متوقعه', 'متوقعه پایلې', '<p>1.&nbsp;&nbsp;&nbsp;&nbsp;درباره تمامی احکام و فرامین امیرالمؤمنین حفظه‌الله تعالی و رعاه، آگاهی ملی و بین‌المللی ایجاد خواهد شد، ابعاد فکری، شرعی و تطبیقی آن به‌صورت عام و خاص تبیین می‌گردد و برای ملت موجب وحدت فکری و اعتماد خواهد شد. </p><p>2.&nbsp;&nbsp;&nbsp;&nbsp;به رهبری امارت اسلامی از طریق مقالات علمی، تحلیل‌ها و پژوهش‌ها، پیشنهادات مشخص و راه‌حل‌هایی برای تطبیق بهتر فرامین، افزایش آگاهی عمومی و تأثیرگذاری اجتماعی ارائه خواهد شد.</p><p>3.&nbsp;&nbsp;&nbsp;&nbsp;از طریق این کنفرانس، تأثیرات فرامین در جامعهٔ افغانستان از رهگذر تبادل فکری و مباحثات علمی میان پژوهشگران دینی، سیاسی، علمی و فرهنگی مورد بررسی قرار خواهد گرفت و از سوی قشر علمی، تمام ابعاد فرامین در مباحثات اجتماعی، رسانه‌ای و علمی تبیین شده و زمینهٔ تفکر و عمل مشترک فراهم خواهد شد.</p><p>4.&nbsp;&nbsp;&nbsp;&nbsp;ماهیت علمی کنفرانس و محورهای تحلیلی آن، یک تصویر عادلانه و مشروع از امارت اسلامی و نظام اسلامی ارائه خواهد کرد و بر مبنای شریعت اسلامی، راه‌حل‌هایی برای چالش‌ها و مشکلات چندبعدی افغانستان پیشنهاد خواهد کرد.</p><p>5.&nbsp;&nbsp;&nbsp;&nbsp;یافته‌های علمی کنفرانس، بنیادهای علمی برای تبیین و تقویت سیاست‌های اسلامي، اقتصادی، اجتماعی و فرهنگی امارت اسلامی فراهم خواهند کرد.</p><p>6.&nbsp;&nbsp;&nbsp;&nbsp;نتایج کنفرانس در تدوین و اصلاح نصاب تعلیمی و تحصیلی، ارتقای آگاهی شرعی و تربیت فکری نسل نو مؤثر خواهند بود.</p><p>7.&nbsp;&nbsp;&nbsp;&nbsp;کنفرانس نشان خواهد داد که امیرالمؤمنین حفظه‌الله تعالی ورعاه، در تمام عرصه‌های دینی، سیاسی، اقتصادی، فرهنگی و اجتماعی، بر اساس شریعت اسلامی رهنمودهای روشن و صریح برای هدایت ملت ارائه کرده است که سعادت دنیوی و اخروی امت در آن نهفته است.</p><p>8.&nbsp;&nbsp;&nbsp;&nbsp;این کنفرانس در ایجاد مفاهیم و دیدگاه‌های نوین میان علما، دانشمندان، سیاست‌گذاران و متخصصین کمک خواهد کرد و برای پیشرفت در تمام عرصه‌ها، بر اساس ثبات، بنیاد اسلامی و شرعی برای افغانستان فراهم خواهد کرد. </p><p>9.&nbsp;&nbsp;&nbsp;&nbsp;ابعاد شرعی فرامین امیرالمؤمنین حفظه‌الله تعالی ورعاه روشن خواهد شد و اذهان مردم از فتنه‌هایی که فتنه‌گران با چهره‌ها و لباس‌های گوناگون نشر می‌کنند و می‌کوشند افکار مردم را مشوش سازند، محفوظ خواهند ماند.</p><p>10. موضوعاتی چون قضاوت، حدود، زکات، حقوق زنان، حجاب، امر به معروف، حیای اسلامی، بیت‌المال، امانت‌داری، عدالت و برادری میان اقوام، زبان‌ها و طبقات مختلف، مقابله با تفرقه‌های قومی، آموزش و تحصیل، صلح و امنیت از ارکان اساسی نظام اسلامی به شمار می‌روند. بررسی چنین موضوعاتی در چارچوب این کنفرانس می‌تواند در ایجاد جامعه‌ای اسلامی و متحد نقش مؤثری ایفا نماید.</p><p><br></p>', '<p>1.&nbsp;&nbsp;&nbsp;&nbsp;د امیرالمؤمنین حفظه الله تعالی ورعاه د ټولو احکامو او فرامینو په اړه به یو ملي او نړیوال پوهاوی رامنځته شوی وي، فکري، شرعي او تطبیقي ابعاد به یې په عامه او خاصه توګه واضح شي او د ملت لپاره به د فکري وحدت او باور سبب وګرځي.</p><p>2.&nbsp;&nbsp;&nbsp;&nbsp;د اسلامي امارت مشرتابه ته به د علمي مقالو، تحلیلونو او څېړنو له لارې د فرامینو د لا ښه تطبیق، عامه پوهاوي، او ټولنیزې اغېزمنتیا لپاره مشخص وړاندیزونه او حل لارې وړاندې شي. </p><p>3.&nbsp;&nbsp;&nbsp;&nbsp;د کنفرانس له لارې به د دیني، سیاسي، علمي او فرهنګي څېړونکو ترمنځ د فکري تبادلې او علمي مباحثو له لارې په افغاني ټولنه کې د فرامینو اغېزې وڅېړل شي او د علمي قشر له لارې به په ټولنیزو، مطبوعاتي او علمي مباحثو کې د فرامینو ټول اړخونه واضح شي او د مشترک فکر او عمل لپاره به زمینه مساعده شي</p><p>4.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;د کنفرانس علمي بڼه او د تحلیل محورونه به د اسلامي امارت او اسلامي نظام په اړه یو عادلانه، مشروع او بشردوستانه تصویر رامنځته کړي او د اسلامي شریعت پر بنا به د افغانستان څو اړخیزه ننګونو او ستونزو ته حل لارې وړاندې شوې وي. </p><p>5.&nbsp;&nbsp;&nbsp;&nbsp;د کنفرانس علمي موندنې به د اسلامي امارت د سیاسي، اقتصادي، ټولنیزو او فرهنګي پالیسیو د تشریح او تقویې لپاره علمي بنسټونه رامنځته کړي.</p><p>6.&nbsp;&nbsp;&nbsp;&nbsp;د کنفرانس پایلې به د تعلیمي او تحصیلي نصاب، شرعي پوهاوي او د نوي نسل په فکري روزنه کې مرسته وکړي. </p><p>7.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;کنفرانس به وښيي چې د اسلامي امارت امیرالمؤمین حفظه الله تعالی ورعاه په ټولو دیني، سیاسي، اقتصادي، فرهنګي او ټولنیزو برخو کې د ملت د لارښوونې لپاره د اسلامي شریعت پربنا واضحې او څرګندنې لارښوونې وړاندې کړې، چې د ملت د دنیا او آخرت نېکمرغي په کې نغښتې ده. </p><p>8.&nbsp;&nbsp;&nbsp;&nbsp;کنفرانس به د علماوو، پوهانو، پالیسي جوړونکو او متخصصینو ترمنځ د نوو مفاهیمو او لیدلورو په رامنځته کولو کې مرسته وکړي، او د یو باثباته افغانستان لپاره به په ټولو برخو کې د پرمختګ لپاره اسلامي او شرعي اساس او بنیاد رامنځته شي. </p><p>9.&nbsp;&nbsp;&nbsp;&nbsp;د امیرالمؤمین <sup>حفظه الله تعالی ورعاه</sup> د فرامینو شرعي اړخونه به روښانه شي، د ولس ذهنونه به له هغو فتنو ژغورل شوي وي، چې فتنه اچوونکي یې په بېلابېلو جامو کې خپروي او هڅه کوي، د ولس ذهنونه مغشوش کړي. </p><p>10.&nbsp;د قضاوت، حدودو، زکات، د ښځو حقوق، حجاب، امربالمعروف، اسلامي حیاء، بیت المال، امانتدارۍ، د قومونو، ژبو او قشرونو ترمنځ عدالت او ورورولي او د قوم محوره تفرقو په وړاندې د ملي وحدت ټینګښت، تعلیم او تحصیل، سوله او امنیت هغه موضوعات دي، چې د اسلامي نظام بنسټ جوړوي، د دغه کنفرانس په لړ کې د داسې موضوعاتو څېړل به د یوې اسلامي او متحدې ټولنې په رامنځته کولو کې اغېزمن ثابت شي.&nbsp;</p><p><br></p>', NULL, '2025-12-13 06:35:10', '2025-12-20 03:30:35');
INSERT INTO `web_pages` (`id`, `name_fa`, `name_en`, `title_fa`, `title_en`, `content_fa`, `content_en`, `cover_image`, `created_at`, `updated_at`) VALUES
(11, 'تأثیرات کنفرانس', 'د کنفرانس اغېزې', 'تأثیرات کنفرانس', 'د کنفرانس اغېزې', '<p>فرمان‌های امیرالمؤمنین حفظه‌الله تمامی امور سیاسی، اقتصادی، مذهبی و فرهنگی مرتبط با ملت را در پرتو شریعت اسلامی تنظیم می‌نمایند. این یک مسؤولیت بزرگ است که در پرتو رهنمودهای رسول اکرمﷺ، سیرت خلفای راشدین و اصول فقه اسلامی تفسیر و تحلیل می‌گردد. رهبری نظام اسلامی بر ستون‌های اساسی هدایت، عدالت، رحمت و استقامت استوار است. چنین رهبری از وحی الهی سرچشمه می‌گیرد و هدف آن، تأمین سعادت انسان در دنیا و آخرت می‌باشد. فرامین امیرالمؤمنین حفظه‌الله تعالی بر اساس شریعت الهی صادر می‌شوند که از یک سو دارای مشروعیت شرعی‌اند و از سوی دیگر وحدت، امنیت و عزت امت را تضمین می‌کنند. این کنفرانس زمینه‌ساز وحدت سیاسی، وفاداری به رهبری و ایجاد فضای اعتماد میان مردم خواهد شد. این کنفرانس فرصت مناسبی است برای تحلیل، تبادل فکری و انجام پژوهش‌ها در زمینه فرامین مربوط به تعیین سرنوشت امت، اجرای شریعت، اتخاذ موضع مقابل دشمن، تقویت نظم داخلی و حل مشکلات در بخش‌های مختلف که نتایج و تأثیرات آن از طریق قشر علمی با ملت به اشتراک گذاشته خواهد شد. ثبات و اصلاح جامعه از اهداف اساسی امارت اسلامی به شمار می‌رود. فرامین امیرالمؤمنین حفظه‌الله پیام‌های مستقیم در زمینه عدالت اجتماعی، تقویت حقوق زنان و مردان و اخلاق اجتماعي دارند. این کنفرانس همچنین به بررسی فرامین امیرالمؤمنین حفظه الله در زمینه خودکفایی اقتصادی، استفاده بهینه از منابع داخلی، اجرای زکات و عشر، و حمایت از محرومان می‌پردازد تا نظام اقتصادی عادلانه‌ای را ترویج کند. این فرامین پیام‌هایی درباره بانکداری اسلامی، بازار عادلانه و رهایی از وابستگی‌های خارجی دارند که همه این موضوعات در چارچوب این کنفرانس مورد بررسی قرار گرفته و سیاست‌های اقتصادی حکومت اسلامی را روشن خواهند ساخت. پاسداري از ارزش‌های فرهنگی یکی از راه‌های مهم برای بقاء و پایداری ملت است. فرامین امیرالمؤمنین حفظه الله بر حفظ ارزش‌ها، سنت‌ها، زبان‌ها و هویت فکری افغانی و اسلامی تأکید دارند. این فرامین تلاش می‌کنند تا در مقابل تهاجم فرهنگی غرب، جایگزین اسلامی ارائه دهند و نشان دهند که ملت‌های مسلمان مالک تمدن، هنر، زبان و ادبیات خود هستند. یک بعد دیګر و مهم فراین تطبیق شریعت است، فرامین بر اساس اصول شریعت اسلامی صادر شده‌اند و تلاش می‌کنند که هر جنبه‌ای از شریعت در عرصه عملی زندگی به اجرا درآید. اجرای حدود، روشن‌سازی اصول قضاوت، نقش علما و گسترش آموزش‌های دینی از جمله پیشرفت‌های مهم شرعی است که در سایه این فرامین تحقق می‌یابد. این کنفرانس ابعاد شرعی تمامی فرامین را بررسی خواهد کرد و از طریق ارائه مقالات علمی، زمینه‌های بیشتری برای اجرای آنها فراهم خواهد ساخت. به طور کلی، این کنفرانس بحثی علمی و فکری، مشاوره‌ها و تحلیل‌های عمیقی پیرامون فرامین امیرالمؤمنین حفظه الله ایجاد می‌کند. همچنین، کنفرانس به بررسی تجارب، چالش‌ها و راهکارهای اجرای فرامین امیرالمؤمنین خواهد پرداخت و اساتید دانشگاه‌ها، علما، قضات و متخصصان نظرات، استدلال‌ها و پیشنهادات علمی خود را در خصوص اجرای این فرامین ارائه خواهند کرد. در نهایت می‌توان گفت که فرامین امیرالمؤمنین حفظه الله تحت چتر رهبری اسلامی، بیداری فکری، سیاسی، اقتصادی، اجتماعی، فرهنگی و شرعی یک ملت را به ارمغان می‌آورد. در این کنفرانس، مکانیزم‌های واضحی برای اجرای این فرامین ارائه خواهد شد تا ملت بتواند در عمل ثمرات آن را مشاهده کند.</p><p><br></p>', '<p class=\"ql-align-justify ql-direction-rtl\">د امیرالمؤمنین<sup> حفظه الله</sup> فرمانونه د اسلامي شریعت په رڼا کې په ملت پورې تړلې ټولې سیاسي، اقتصادي، مذهبي او کلتوري چارې تنظیموي، دا یو داسې مسؤولیت دی چې د رسول اللهﷺ تر لارښوونو، د خلفأ راشدینو ترسیرت او د اسلامي فقهې تر اصولو لاندې تفسیرېږي. اسلامي قیادت د هدایت، عدالت، رحمت او استقامت بنسټیزې ستنې لري، دا ډول قیادت د اللهﷻ له وحی څخه سرچینه اخلي او هدف یې د انسان د دنیا او آخرت نېکمرغي ده. د امیرالمؤمنین <sup>حفظه الله</sup> فرامین، د الله ﷻ د شریعت پر بنسټ صادرېږي، چې له یو خوا شرعي مشروعیت لري او له بلې خوا د اُمت د وحدت، امنیت او عزت ضمانت کوي، دغه کنفرانس به سیاسي یووالی، مشرتابه ته وفاداري او د خلکو تر منځ د اعتماد فضا رامنځته کړي. دغه کنفرانس د اُمت د سرنوشت ټاکلو، د شریعت د نفاذ، د دښمن پر وړاندې د موقف نیولو، د داخلي نظم د ټینګښت او په بېلابېلو برخو کې د ستونزو د حل په تړاو د فرامینو د تحلیل، فکري تبادلې او څېړنو یو ښه فرصت دی، چې د علمي قشر له لارې به یې پایلې او اغېزې له ملت سره شریکې شي. د ټولنې ثبات او اصلاح د اسلامي امارت له مهمو موخو څخه ګڼل کيږي. د امیرالمؤمنین <sup>حفظه الله</sup> فرامین د ټولنیز عدالت، د ښځو او نارینه­وو د حقوقو او اخلاقو د پیاوړتیا په برخه کې مستقیم پیغامونه لري. پر دې سربېره د امیرالمؤمنین <sup>حفظه الله</sup> فرامین د اقتصادي خودکفائۍ، په سمه توګه د داخلي منابعو دکارولو، د زکات او عشر د تطبیق، او له بېوزلو څخه د ملاتړ له لارې یو عادلانه اقتصادي نظام ترویجوي، فرامین د اسلامي بانکدارۍ، عادلانه بازار، او له بهرنیو وابستګیو څخه د خلاصون پیغامونه لري، چې دا ټول به د دغه کنفرانس په لړ کې وڅېړل شي او په اقتصادي برخه کې به د اسلامي امارت کړنلارې روښانه شي. د فرهنګي ارزښتونو پالنه د ملت د بقاء یوه مهمه لاره ده. د امیرالمؤمنین <sup>حفظه الله</sup> فرامین د افغاني او اسلامي ارزښتونو، دودونو، ژبو، او فکري هویت پر ساتنه ټینګار کوي. دغه فرامین هڅه کوي چې د غربي فرهنګي یرغل پر وړاندې اسلامي بدیل وړاندې کړي او دا وښيي چې مسلمان ملتونه د خپل تمدن، هنر، ژبې او ادب خاوندان دي. د فرامینو بل مهم بعد د شریعت د تطبیق لوری دی. فرامین د اسلامي شریعت د اصولو پر بنسټ صادرېږي او هڅه کوي چې د شریعت هر اړخ د ژوند په عملي ډګر کې تطبیق شي. د حدودو تطبیق، د قضاوت د اصولو وضاحت، د علماوو نقش او د دیني زده کړو پراختیا له مهمو شرعي پرمختګونو څخه دي چې د فرامینو په رڼا کې رامنځته کيږي. دغه کنفرانس به د ټولو فرامینو شرعي ابعاد وڅېړي او د علمي مقالو د ارائه کولو له لارې به یې تطبیق ته لار نوره زمینه هم مساعده شي. په ټولیزه توګه به د دغه کنفرانس له لارې د امیرالمؤمنین <sup>حفظه الله</sup> د فرامینو په اړه یو علمي او فکري بحث، مشورې او ژورې شننې رامنځته کړي، کنفرانس به د امیرالمؤمنین د فرامینو د تطبیق تجربې، ننګونې، او حللارې مطرح کړي. او همداراز د پوهنتونونو استادان، علما، قاضیان او متخصصین به یې د تطبیق په اړه خپل وړاندیزونه، علمي استدلال او منطق وړاندې کړي.&nbsp;په پای کې ویلی شو، چې د اسلامي قیادت تر چتر لاندې د امیرالمؤمنین<sup> حفظه الله</sup> فرامین د یوه ملت فکري، سیاسي، اقتصادي، ټولنیز، فرهنګي او شرعي بیداري رامنځته کوي. په دغه کنفرانس کې به یې د تطبیق لپاره واضح ميکانيزم وړاندې شي، ترڅو ملت په عمل کې د هغو ثمره وویني.</p><p><br></p>', NULL, '2025-12-13 06:41:44', '2025-12-13 06:41:44'),
(12, 'شرکت‌کنندگان کنفرانس', 'د کنفرانس ګډونوال', 'شرکت‌کنندگان کنفرانس', 'د کنفرانس ګډونوال', '<p>در کنفرانسی که از سوی پوهنتون‌ وردگ برگزار می‌شود، پژوهشگران مرتبط با موضوع کنفرانس،علما، استادان پوهنتون‌های داخلی و خارجی، محصلان، نمایندگان ادارات ملی و امارتی، متخصصان نهادهای ملی، سیاست‌گذاران و برنامه‌ریزان، نمایندگان بخش خصوصی، کارمندان شرکت‌ها، چهره‌های اجتماعی و علاقه‌مندان حوزه‌های تخصصی حضور خواهند داشت.</p>', '<p>د وردګ پوهنتون له لوري په جوړېدونکي کنفرانس کې به د کنفرانس د موضوع اړوند څېړونکي، علما، د داخلي او خارجي پوهنتونونو استادان، محصلین، د ملي او امارتي ادارو استازي، د ملي ادارو متخصصین، پالیسي او پلان جوړونکي، د خصوصي سکتور استازي، د کمپنیو کارکوونکي، ټولنیز مخور او د مسلک مینه وال ګډون کوي.</p>', NULL, '2025-12-13 06:51:24', '2025-12-13 06:51:24'),
(14, 'راهنمایی نگارش مقاله', 'راهنمایی نگارش مقاله', 'راهنمایی نگارش مقاله', 'راهنمایی نگارش مقاله', '<p><br></p>', NULL, NULL, '2025-12-20 10:15:21', '2025-12-20 10:15:21'),
(15, 'خلاصه‌های پذیرفته شده', 'منل شوي لنډیزونه', 'خلاصه‌های پذیرفته شده', 'منل شوي لنډیزونه', '<p><br></p>', NULL, NULL, '2025-12-20 11:01:14', '2025-12-20 11:01:14'),
(16, 'مقاله‌های پذیرفته شده', 'منل شوې مقالې', 'مقاله‌های پذیرفته شده', 'منل شوې مقالې', NULL, NULL, NULL, '2025-12-20 11:01:48', '2025-12-20 11:01:48'),
(17, 'فهرست فرامین', 'د فرمانونو فهرست', 'فهرست فرامین', 'د فرمانونو فهرست', '<p><br></p>', NULL, NULL, '2025-12-20 11:16:09', '2025-12-20 11:16:09'),
(18, 'فهرست احکام', 'د احکامو فهرست', 'فهرست احکام', 'د احکامو فهرست', '<p><br></p>', NULL, NULL, '2025-12-20 11:18:13', '2025-12-20 11:18:13'),
(19, 'راهنمای ارسال مقاله', 'د مقالې لېږلو لارښود', 'راهنمای ارسال مقاله', 'د مقالې لېږلو لارښود', '<p>برای ارسال مقاله، لطفا این ویدیو را تماشا کنید!</p>', '<p>برای ارسال مقاله، لطفا این ویدیو را تماشا کنید!</p>', NULL, '2025-12-20 11:47:58', '2025-12-22 03:14:26');

-- --------------------------------------------------------

--
-- Table structure for table `web_page_files`
--

CREATE TABLE `web_page_files` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `page_id` bigint(20) UNSIGNED NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_name` text DEFAULT NULL,
  `file_type` enum('pdf','docx','xlsx','mp4','mov','avi','webm') DEFAULT NULL,
  `file_size` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `web_page_files`
--

INSERT INTO `web_page_files` (`id`, `page_id`, `file_path`, `file_name`, `file_type`, `file_size`, `created_at`, `updated_at`) VALUES
(3, 14, 'website/pages/files14/QLBoqh94fvTbTk7j6bdfjrefmNuGoPC5dKmfXFYB.pdf', 'د مقالې فارمټ1.pdf', 'pdf', 711128, '2025-12-20 10:15:21', '2025-12-20 10:15:21'),
(4, 15, 'website/pages/files15/E3dpjvtW5fUkN9XUterJVaLfpnEpqHAio74PSMf8.pdf', 'تائید شوو لنډیزونو لیست.pdf', 'pdf', 781160, '2025-12-20 11:03:42', '2025-12-20 11:06:23'),
(6, 18, 'website/pages/files18/tQt5hbs9fLLf5rI7gPDqBeMYnCvxaFoBRCt0eTe1.pdf', 'احکامو د ویب سایټ لپاره.pdf', 'pdf', 739604, '2025-12-20 11:18:13', '2025-12-20 11:18:13'),
(7, 17, 'website/pages/files17/9b00NluskFgW0BgBAVBvCINz1vGyPsSZNBqKwetm.pdf', 'د فراینو لیست د ویب سایټ لپاره.pdf', 'pdf', 866300, '2025-12-20 11:21:06', '2025-12-20 11:21:06');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `academic_ranks`
--
ALTER TABLE `academic_ranks`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_roles`
--
ALTER TABLE `access_roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `access_role_user`
--
ALTER TABLE `access_role_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `access_role_user_user_id_role_id_unique` (`user_id`,`role_id`),
  ADD KEY `access_role_user_role_id_foreign` (`role_id`);

--
-- Indexes for table `actions`
--
ALTER TABLE `actions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `author_types`
--
ALTER TABLE `author_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `countries`
--
ALTER TABLE `countries`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `editorial_decisions`
--
ALTER TABLE `editorial_decisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `editorial_decisions_submission_id_foreign` (`submission_id`),
  ADD KEY `editorial_decisions_editor_id_foreign` (`editor_id`);

--
-- Indexes for table `education_degrees`
--
ALTER TABLE `education_degrees`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `gazettes`
--
ALTER TABLE `gazettes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `gazette_files`
--
ALTER TABLE `gazette_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `gazette_files_gazette_id_foreign` (`gazette_id`);

--
-- Indexes for table `indexes`
--
ALTER TABLE `indexes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `issues`
--
ALTER TABLE `issues`
  ADD PRIMARY KEY (`id`);

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
-- Indexes for table `keywords`
--
ALTER TABLE `keywords`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `leadership_board`
--
ALTER TABLE `leadership_board`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menus`
--
ALTER TABLE `menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `name` (`name_fa`,`url`,`parent_id`,`grand_parent_id`);

--
-- Indexes for table `menu_sections`
--
ALTER TABLE `menu_sections`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `menu_types`
--
ALTER TABLE `menu_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `permissions`
--
ALTER TABLE `permissions`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `posts_user_id_foreign` (`user_id`);

--
-- Indexes for table `provinces`
--
ALTER TABLE `provinces`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reviews_submission_id_foreign` (`submission_id`),
  ADD KEY `reviews_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `review_decisions`
--
ALTER TABLE `review_decisions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_decisions_review_id_foreign` (`review_id`);

--
-- Indexes for table `review_files`
--
ALTER TABLE `review_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_files_review_id_foreign` (`review_id`);

--
-- Indexes for table `review_invites`
--
ALTER TABLE `review_invites`
  ADD PRIMARY KEY (`id`),
  ADD KEY `review_invites_submission_id_foreign` (`submission_id`),
  ADD KEY `review_invites_reviewer_id_foreign` (`reviewer_id`);

--
-- Indexes for table `scientific_board`
--
ALTER TABLE `scientific_board`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scientific_board_members`
--
ALTER TABLE `scientific_board_members`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `submissions`
--
ALTER TABLE `submissions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submissions_submitter_id_foreign` (`submitter_id`),
  ADD KEY `submissions_issue_id_foreign` (`issue_id`);

--
-- Indexes for table `submission_authors`
--
ALTER TABLE `submission_authors`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_authors_country_id_foreign` (`country_id`),
  ADD KEY `submission_authors_user_id_foreign` (`user_id`),
  ADD KEY `submission_authors_type_id_foreign` (`type_id`),
  ADD KEY `submission_authors_submission_id_foreign` (`submission_id`);

--
-- Indexes for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_files_submission_id_foreign` (`submission_id`),
  ADD KEY `submission_files_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `submission_keywords`
--
ALTER TABLE `submission_keywords`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `submission_keywords_submission_id_keyword_id_unique` (`submission_id`,`keyword_id`),
  ADD KEY `submission_keywords_keyword_id_foreign` (`keyword_id`);

--
-- Indexes for table `submission_logs`
--
ALTER TABLE `submission_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_logs_submission_id_foreign` (`submission_id`),
  ADD KEY `submission_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `submission_tags`
--
ALTER TABLE `submission_tags`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_tags_submission_id_foreign` (`submission_id`);

--
-- Indexes for table `submission_views`
--
ALTER TABLE `submission_views`
  ADD PRIMARY KEY (`id`),
  ADD KEY `submission_views_submission_id_ip_index` (`submission_id`,`ip`);

--
-- Indexes for table `system_logs`
--
ALTER TABLE `system_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD UNIQUE KEY `users_phone_no_unique` (`phone_no`);

--
-- Indexes for table `web_menus`
--
ALTER TABLE `web_menus`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `slug` (`slug`);

--
-- Indexes for table `web_pages`
--
ALTER TABLE `web_pages`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `web_page_files`
--
ALTER TABLE `web_page_files`
  ADD PRIMARY KEY (`id`),
  ADD KEY `web_page_files_page_id_foreign` (`page_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `academic_ranks`
--
ALTER TABLE `academic_ranks`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `access_roles`
--
ALTER TABLE `access_roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `access_role_user`
--
ALTER TABLE `access_role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `actions`
--
ALTER TABLE `actions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `author_types`
--
ALTER TABLE `author_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `countries`
--
ALTER TABLE `countries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=90;

--
-- AUTO_INCREMENT for table `editorial_decisions`
--
ALTER TABLE `editorial_decisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `education_degrees`
--
ALTER TABLE `education_degrees`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `gazettes`
--
ALTER TABLE `gazettes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `gazette_files`
--
ALTER TABLE `gazette_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=116;

--
-- AUTO_INCREMENT for table `indexes`
--
ALTER TABLE `indexes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `issues`
--
ALTER TABLE `issues`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `keywords`
--
ALTER TABLE `keywords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `leadership_board`
--
ALTER TABLE `leadership_board`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `menus`
--
ALTER TABLE `menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `menu_sections`
--
ALTER TABLE `menu_sections`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `menu_types`
--
ALTER TABLE `menu_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=273;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `permissions`
--
ALTER TABLE `permissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=237;

--
-- AUTO_INCREMENT for table `posts`
--
ALTER TABLE `posts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `provinces`
--
ALTER TABLE `provinces`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=34;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `review_decisions`
--
ALTER TABLE `review_decisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `review_files`
--
ALTER TABLE `review_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `review_invites`
--
ALTER TABLE `review_invites`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `scientific_board`
--
ALTER TABLE `scientific_board`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `scientific_board_members`
--
ALTER TABLE `scientific_board_members`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `submissions`
--
ALTER TABLE `submissions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `submission_authors`
--
ALTER TABLE `submission_authors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `submission_files`
--
ALTER TABLE `submission_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=59;

--
-- AUTO_INCREMENT for table `submission_keywords`
--
ALTER TABLE `submission_keywords`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=79;

--
-- AUTO_INCREMENT for table `submission_logs`
--
ALTER TABLE `submission_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submission_tags`
--
ALTER TABLE `submission_tags`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `submission_views`
--
ALTER TABLE `submission_views`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `system_logs`
--
ALTER TABLE `system_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `web_menus`
--
ALTER TABLE `web_menus`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `web_pages`
--
ALTER TABLE `web_pages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `web_page_files`
--
ALTER TABLE `web_page_files`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `access_role_user`
--
ALTER TABLE `access_role_user`
  ADD CONSTRAINT `access_role_user_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `access_roles` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `access_role_user_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `editorial_decisions`
--
ALTER TABLE `editorial_decisions`
  ADD CONSTRAINT `editorial_decisions_editor_id_foreign` FOREIGN KEY (`editor_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `editorial_decisions_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `gazette_files`
--
ALTER TABLE `gazette_files`
  ADD CONSTRAINT `gazette_files_gazette_id_foreign` FOREIGN KEY (`gazette_id`) REFERENCES `gazettes` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `reviews_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reviews_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_decisions`
--
ALTER TABLE `review_decisions`
  ADD CONSTRAINT `review_decisions_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_files`
--
ALTER TABLE `review_files`
  ADD CONSTRAINT `review_files_review_id_foreign` FOREIGN KEY (`review_id`) REFERENCES `reviews` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `review_invites`
--
ALTER TABLE `review_invites`
  ADD CONSTRAINT `review_invites_reviewer_id_foreign` FOREIGN KEY (`reviewer_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `review_invites_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submissions`
--
ALTER TABLE `submissions`
  ADD CONSTRAINT `submissions_issue_id_foreign` FOREIGN KEY (`issue_id`) REFERENCES `issues` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `submissions_submitter_id_foreign` FOREIGN KEY (`submitter_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_authors`
--
ALTER TABLE `submission_authors`
  ADD CONSTRAINT `submission_authors_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_authors_type_id_foreign` FOREIGN KEY (`type_id`) REFERENCES `author_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_authors_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `submission_files`
--
ALTER TABLE `submission_files`
  ADD CONSTRAINT `submission_files_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_files_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `submission_keywords`
--
ALTER TABLE `submission_keywords`
  ADD CONSTRAINT `submission_keywords_keyword_id_foreign` FOREIGN KEY (`keyword_id`) REFERENCES `keywords` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_keywords_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_logs`
--
ALTER TABLE `submission_logs`
  ADD CONSTRAINT `submission_logs_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `submission_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `submission_tags`
--
ALTER TABLE `submission_tags`
  ADD CONSTRAINT `submission_tags_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `submission_views`
--
ALTER TABLE `submission_views`
  ADD CONSTRAINT `submission_views_submission_id_foreign` FOREIGN KEY (`submission_id`) REFERENCES `submissions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `web_page_files`
--
ALTER TABLE `web_page_files`
  ADD CONSTRAINT `web_page_files_page_id_foreign` FOREIGN KEY (`page_id`) REFERENCES `web_pages` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
