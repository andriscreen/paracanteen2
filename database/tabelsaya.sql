-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 18, 2025 at 06:34 AM
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
-- Database: `paragonapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `password` char(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `nama`, `nip`, `gmail`, `password`, `created_at`) VALUES
(1, 'Ibu Admin', 'ADM001', 'admin@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-09-30 15:45:31');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `name`, `is_active`) VALUES
(1, 'Facility Management', 1),
(2, 'Human Resources', 1),
(3, 'Finance', 1),
(4, 'IT', 1),
(5, 'Production', 1),
(6, 'Quality Assurance', 1),
(7, 'Marketing', 1),
(8, 'Sales', 1),
(9, 'Operations', 1),
(10, 'Procurement', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kupon_history`
--

CREATE TABLE `kupon_history` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `order_id` int(11) NOT NULL,
  `jumlah_kupon` int(11) NOT NULL,
  `tanggal_dapat` timestamp NOT NULL DEFAULT current_timestamp(),
  `keterangan` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `kupon_history`
--

INSERT INTO `kupon_history` (`id`, `user_id`, `order_id`, `jumlah_kupon`, `tanggal_dapat`, `keterangan`) VALUES
(1, 10, 29, 1, '2025-10-18 02:43:20', 'Kupon dari pemesanan makanan'),
(2, 10, 29, 1, '2025-10-18 02:43:20', 'Kupon dari pemesanan makanan'),
(3, 10, 29, 1, '2025-10-18 02:43:20', 'Kupon dari pemesanan makanan'),
(4, 10, 29, 1, '2025-10-18 02:43:20', 'Kupon dari pemesanan makanan'),
(5, 10, 29, 1, '2025-10-18 02:43:20', 'Kupon dari pemesanan makanan'),
(6, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(7, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(8, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(9, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(10, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(11, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(12, 10, 30, 1, '2025-10-18 02:58:04', 'Kupon dari pemesanan makanan'),
(13, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(14, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(15, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(16, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(17, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(18, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(19, 10, 31, 1, '2025-10-18 03:07:05', 'Kupon dari pemesanan makanan'),
(20, 10, 33, 1, '2025-10-18 03:52:02', 'Kupon dari pemesanan makanan'),
(21, 10, 34, 1, '2025-10-18 04:02:24', 'Kupon dari pemesanan makanan'),
(22, 10, 34, 1, '2025-10-18 04:02:24', 'Kupon dari pemesanan makanan'),
(23, 10, 34, 1, '2025-10-18 04:02:24', 'Kupon dari pemesanan makanan'),
(24, 10, 34, 1, '2025-10-18 04:02:24', 'Kupon dari pemesanan makanan'),
(25, 10, 34, 1, '2025-10-18 04:02:24', 'Kupon dari pemesanan makanan'),
(26, 10, 35, 1, '2025-10-18 04:07:06', 'Kupon dari pemesanan makanan');

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int(11) NOT NULL,
  `week_id` int(11) NOT NULL,
  `day` varchar(20) DEFAULT NULL,
  `menu_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `week_id`, `day`, `menu_name`) VALUES
(1, 1, 'Senin', 'Nasi Goreng'),
(2, 1, 'Selasa', 'Mie Ayam'),
(3, 1, 'Rabu', 'Soto Ayam'),
(4, 1, 'Kamis', 'Bakso'),
(5, 1, 'Jumat', 'Ayam Geprek'),
(6, 1, 'Sabtu', 'Nasi Uduk'),
(7, 1, 'Minggu', 'Lontong Sayur'),
(8, 2, 'Senin', 'Nasi Kepal merah'),
(9, 2, 'Selasa', 'Nasi Pake Mangga'),
(10, 2, 'Rabu', 'Ayam Penyet gondrong'),
(11, 2, 'Kamis', 'Ayam Bakar'),
(12, 2, 'Jumat', 'Ikan Teri Bohay'),
(13, 2, 'Sabtu', 'Libur'),
(14, 2, 'Minggu', 'Libur');

-- --------------------------------------------------------

--
-- Table structure for table `menu_images`
--

CREATE TABLE `menu_images` (
  `id` int(11) NOT NULL,
  `week_id` int(11) NOT NULL,
  `day` varchar(20) NOT NULL,
  `image_url` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_images`
--

INSERT INTO `menu_images` (`id`, `week_id`, `day`, `image_url`) VALUES
(1, 1, 'Senin', 'assets/img/menu/babi.png'),
(2, 2, 'Minggu', 'assets/img/menu/kuda.png'),
(3, 2, 'Senin', 'assets/img/menu/monyet.png');

-- --------------------------------------------------------

--
-- Table structure for table `nama_vendor`
--

CREATE TABLE `nama_vendor` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nama_vendor`
--

INSERT INTO `nama_vendor` (`id`, `name`, `is_active`) VALUES
(1, 'Serikandi', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `week_id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `place_id` int(11) NOT NULL,
  `shift_id` int(11) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `week_id`, `year_id`, `plant_id`, `place_id`, `shift_id`, `created_at`, `user_id`) VALUES
(1, 1, 1, 1, 1, NULL, '2025-10-06 14:38:10', 1),
(2, 1, 1, 1, 1, NULL, '2025-10-06 14:41:52', 1),
(3, 1, 3, 1, 1, NULL, '2025-10-06 14:48:37', 1),
(4, 1, 3, 1, 1, NULL, '2025-10-06 14:49:45', 1),
(5, 1, 3, 1, 1, NULL, '2025-10-06 14:52:13', 1),
(6, 1, 3, 1, 1, NULL, '2025-10-06 14:53:36', 1),
(7, 1, 3, 1, 1, NULL, '2025-10-06 14:57:08', 1),
(8, 1, 3, 1, 1, NULL, '2025-10-06 14:57:22', 1),
(9, 1, 3, 1, 1, NULL, '2025-10-06 14:57:57', 1),
(10, 1, 3, 1, 1, NULL, '2025-10-06 15:04:52', 1),
(11, 1, 3, 1, 1, NULL, '2025-10-06 15:05:24', 1),
(12, 1, 3, 1, 1, NULL, '2025-10-06 15:14:22', 1),
(13, 1, 3, 1, 1, NULL, '2025-10-06 15:14:59', 1),
(14, 1, 3, 2, 2, NULL, '2025-10-08 13:53:49', 1),
(15, 1, 1, 1, 1, 3, '2025-10-08 14:01:49', 1),
(16, 2, 1, 6, 6, 1, '2025-10-08 14:45:48', 1),
(18, 1, 1, 1, 1, 1, '2025-10-10 16:55:40', 1),
(19, 1, 1, 1, 1, 1, '2025-10-11 01:09:18', 1),
(20, 1, 1, 1, 1, 1, '2025-10-11 01:13:00', 1),
(21, 2, 1, 1, 1, 1, '2025-10-11 01:14:01', 1),
(22, 2, 1, 2, 2, 2, '2025-10-11 01:20:11', 1),
(23, 1, 1, 6, 6, 1, '2025-10-11 01:45:12', 6),
(24, 2, 1, 2, 2, 1, '2025-10-11 04:36:42', 7),
(25, 1, 1, 1, 1, 2, '2025-10-11 05:29:20', 7),
(26, 2, 1, 2, 2, 2, '2025-10-13 15:16:03', 1),
(27, 2, 1, 1, 1, 1, '2025-10-17 23:46:27', 1),
(28, 1, 1, 1, 1, 1, '2025-10-18 00:06:55', 1),
(29, 1, 1, 1, 1, 1, '2025-10-18 02:43:20', 10),
(30, 2, 1, 1, 1, 1, '2025-10-18 02:58:04', 10),
(31, 1, 1, 2, 2, 2, '2025-10-18 03:07:05', 10),
(32, 5, 1, 1, 1, 1, '2025-10-18 03:51:34', 10),
(33, 2, 1, 6, 7, 1, '2025-10-18 03:52:02', 10),
(34, 1, 1, 1, 1, 1, '2025-10-18 04:02:24', 10),
(35, 1, 1, 1, 1, 1, '2025-10-18 04:07:06', 10);

-- --------------------------------------------------------

--
-- Table structure for table `order_menus`
--

CREATE TABLE `order_menus` (
  `id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `menu_id` int(11) NOT NULL,
  `makan` tinyint(1) DEFAULT 0,
  `kupon` tinyint(1) DEFAULT 0,
  `libur` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `order_menus`
--

INSERT INTO `order_menus` (`id`, `order_id`, `menu_id`, `makan`, `kupon`, `libur`) VALUES
(1, 1, 3, 0, 0, 0),
(2, 1, 6, 0, 0, 0),
(3, 2, 1, 0, 0, 0),
(4, 2, 2, 0, 0, 0),
(5, 3, 2, 0, 0, 0),
(6, 3, 5, 0, 0, 0),
(7, 4, 2, 0, 0, 0),
(8, 4, 5, 0, 0, 0),
(9, 5, 2, 0, 0, 0),
(10, 5, 3, 0, 0, 0),
(11, 6, 1, 0, 0, 0),
(12, 6, 4, 0, 0, 0),
(13, 7, 2, 0, 0, 0),
(14, 8, 3, 0, 0, 0),
(15, 8, 6, 0, 0, 0),
(16, 9, 1, 0, 0, 0),
(17, 10, 1, 0, 0, 0),
(18, 10, 2, 0, 0, 0),
(19, 10, 4, 0, 0, 0),
(20, 11, 2, 0, 0, 0),
(21, 12, 1, 0, 0, 0),
(22, 12, 2, 0, 0, 0),
(23, 12, 3, 0, 0, 0),
(24, 12, 4, 0, 0, 0),
(25, 12, 5, 0, 0, 0),
(26, 12, 6, 0, 0, 0),
(27, 12, 7, 0, 0, 0),
(28, 13, 1, 0, 0, 0),
(29, 13, 2, 0, 0, 0),
(30, 13, 3, 0, 0, 0),
(31, 13, 4, 0, 0, 0),
(32, 13, 5, 0, 0, 0),
(33, 13, 6, 0, 0, 0),
(34, 13, 7, 0, 0, 0),
(35, 14, 6, 0, 0, 0),
(36, 15, 1, 0, 0, 0),
(37, 15, 3, 0, 0, 0),
(38, 15, 5, 0, 0, 0),
(39, 16, 8, 0, 0, 0),
(40, 16, 11, 0, 0, 0),
(44, 18, 1, 0, 0, 0),
(45, 18, 2, 0, 0, 0),
(46, 18, 3, 0, 0, 0),
(47, 19, 1, 1, 0, 0),
(48, 20, 1, 1, 0, 0),
(49, 22, 11, 1, 0, 0),
(50, 22, 12, 1, 0, 0),
(51, 22, 8, 0, 1, 0),
(52, 22, 9, 0, 1, 0),
(53, 22, 10, 0, 1, 0),
(54, 22, 13, 0, 0, 1),
(55, 22, 14, 0, 0, 1),
(56, 23, 1, 1, 0, 0),
(57, 23, 4, 1, 0, 0),
(58, 23, 5, 1, 0, 0),
(59, 23, 2, 0, 1, 0),
(60, 23, 3, 0, 1, 0),
(61, 23, 6, 0, 1, 0),
(62, 23, 7, 0, 0, 1),
(63, 24, 8, 1, 0, 0),
(64, 24, 9, 1, 0, 0),
(65, 24, 10, 0, 1, 0),
(66, 24, 11, 0, 1, 0),
(67, 24, 12, 0, 1, 0),
(68, 24, 13, 0, 0, 1),
(69, 24, 14, 0, 0, 1),
(70, 25, 1, 1, 0, 0),
(71, 25, 2, 1, 0, 0),
(72, 25, 3, 1, 0, 0),
(73, 25, 4, 1, 0, 0),
(74, 25, 5, 1, 0, 0),
(75, 25, 6, 0, 0, 1),
(76, 25, 7, 0, 0, 1),
(77, 26, 9, 1, 0, 0),
(78, 26, 11, 1, 0, 0),
(79, 26, 12, 1, 0, 0),
(80, 26, 14, 1, 0, 0),
(81, 26, 8, 0, 1, 0),
(82, 26, 13, 0, 1, 0),
(83, 26, 10, 0, 0, 1),
(84, 27, 8, 1, 0, 0),
(85, 27, 9, 1, 0, 0),
(86, 27, 10, 1, 0, 0),
(87, 27, 11, 1, 0, 0),
(88, 27, 12, 1, 0, 0),
(89, 27, 13, 1, 0, 0),
(90, 27, 14, 1, 0, 0),
(91, 28, 1, 0, 1, 0),
(92, 28, 2, 0, 1, 0),
(93, 28, 3, 0, 1, 0),
(94, 28, 4, 0, 1, 0),
(95, 28, 5, 0, 1, 0),
(96, 28, 6, 0, 1, 0),
(97, 28, 7, 0, 1, 0),
(98, 29, 6, 1, 0, 0),
(99, 29, 1, 0, 1, 0),
(100, 29, 2, 0, 1, 0),
(101, 29, 3, 0, 1, 0),
(102, 29, 4, 0, 1, 0),
(103, 29, 5, 0, 1, 0),
(104, 29, 7, 0, 0, 1),
(105, 30, 8, 0, 1, 0),
(106, 30, 9, 0, 1, 0),
(107, 30, 10, 0, 1, 0),
(108, 30, 11, 0, 1, 0),
(109, 30, 12, 0, 1, 0),
(110, 30, 13, 0, 1, 0),
(111, 30, 14, 0, 1, 0),
(112, 31, 1, 0, 1, 0),
(113, 31, 2, 0, 1, 0),
(114, 31, 3, 0, 1, 0),
(115, 31, 4, 0, 1, 0),
(116, 31, 5, 0, 1, 0),
(117, 31, 6, 0, 1, 0),
(118, 31, 7, 0, 1, 0),
(119, 33, 8, 1, 0, 0),
(120, 33, 9, 1, 0, 0),
(121, 33, 10, 1, 0, 0),
(122, 33, 11, 1, 0, 0),
(123, 33, 12, 1, 0, 0),
(124, 33, 13, 0, 1, 0),
(125, 33, 14, 0, 0, 1),
(126, 34, 1, 0, 1, 0),
(127, 34, 2, 0, 1, 0),
(128, 34, 3, 0, 1, 0),
(129, 34, 4, 0, 1, 0),
(130, 34, 5, 0, 1, 0),
(131, 34, 6, 0, 0, 1),
(132, 34, 7, 0, 0, 1),
(133, 35, 1, 1, 0, 0),
(134, 35, 2, 1, 0, 0),
(135, 35, 4, 1, 0, 0),
(136, 35, 5, 1, 0, 0),
(137, 35, 6, 1, 0, 0),
(138, 35, 3, 0, 1, 0),
(139, 35, 7, 0, 0, 1);

--
-- Triggers `order_menus`
--
DELIMITER $$
CREATE TRIGGER `after_order_menu_insert` AFTER INSERT ON `order_menus` FOR EACH ROW BEGIN
    DECLARE v_user_id INT UNSIGNED;
    
    -- Ambil user_id dari tabel orders
    SELECT user_id INTO v_user_id 
    FROM orders 
    WHERE id = NEW.order_id;
    
    -- Jika kolom kupon = 1, tambahkan ke kupon_history
    IF NEW.kupon = 1 THEN
        -- Tambahkan record ke kupon_history
        INSERT INTO kupon_history (user_id, order_id, jumlah_kupon, keterangan)
        VALUES (v_user_id, NEW.order_id, 1, 'Kupon dari pemesanan makanan');
        
        -- Update total_kupon di tabel users
        UPDATE users 
        SET total_kupon = total_kupon + 1 
        WHERE id = v_user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pic_kantin`
--

CREATE TABLE `pic_kantin` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `departemen` varchar(50) DEFAULT NULL,
  `gmail` varchar(255) NOT NULL,
  `password` char(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pic_kantin`
--

INSERT INTO `pic_kantin` (`id`, `nama`, `departemen`, `gmail`, `password`, `created_at`) VALUES
(1, 'PIC Kantin 1', 'PICK001', 'kantin1@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-10-13 14:02:29');

-- --------------------------------------------------------

--
-- Table structure for table `place`
--

CREATE TABLE `place` (
  `id` int(11) NOT NULL,
  `plant_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `place`
--

INSERT INTO `place` (`id`, `plant_id`, `name`) VALUES
(1, 1, 'Kantin J1'),
(2, 2, 'Kantin J2'),
(3, 3, 'Kantin J3'),
(4, 4, 'Kantin J4'),
(5, 5, 'Kantin J5'),
(6, 6, 'Kantin J6 Depan'),
(7, 6, 'Kantin J6 Belakang');

-- --------------------------------------------------------

--
-- Table structure for table `plant`
--

CREATE TABLE `plant` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `plant`
--

INSERT INTO `plant` (`id`, `name`) VALUES
(1, 'Jatake 1'),
(2, 'Jatake 2'),
(3, 'Jatake 3'),
(4, 'Jatake 4'),
(5, 'Jatake 5'),
(6, 'Jatake 6');

-- --------------------------------------------------------

--
-- Table structure for table `redeem_items`
--

CREATE TABLE `redeem_items` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) NOT NULL,
  `kupon` int(11) NOT NULL,
  `gambar` varchar(255) NOT NULL,
  `keterangan` text DEFAULT NULL,
  `aktif` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `redeem_items`
--

INSERT INTO `redeem_items` (`id`, `nama`, `kupon`, `gambar`, `keterangan`, `aktif`) VALUES
(1, 'Beras', 5, '../assets/img/barang/beras.jpg', 'Beras premium kualitas terbaik 1kg', 1),
(2, 'Minyak Goreng', 3, '../assets/img/barang/minyak.jpg', 'Minyak goreng kemasan 1 liter', 1),
(3, 'Gula', 2, '../assets/img/barang/gula.jpg', 'Gula pasir murni 1kg', 1),
(4, 'Kopi Sachet', 1, '../assets/img/barang/kopi.jpg', 'Kopi instan sachet isi 10', 1),
(5, 'Teh Celup', 1, '../assets/img/barang/teh.jpg', 'Teh celup isi 25 sachet', 1);

-- --------------------------------------------------------

--
-- Table structure for table `redemption_history`
--

CREATE TABLE `redemption_history` (
  `id` int(11) NOT NULL,
  `user_id` int(10) UNSIGNED NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `kupon_used` int(11) NOT NULL,
  `item_name` varchar(100) NOT NULL,
  `redemption_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','completed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `redemption_history`
--

INSERT INTO `redemption_history` (`id`, `user_id`, `item_id`, `quantity`, `kupon_used`, `item_name`, `redemption_date`, `status`) VALUES
(1, 10, 3, 2, 4, 'Gula', '2025-10-18 02:56:29', 'pending'),
(2, 10, 2, 1, 3, 'Minyak Goreng', '2025-10-18 02:58:42', 'pending'),
(3, 10, 2, 1, 3, 'Minyak Goreng', '2025-10-18 03:07:20', 'pending'),
(4, 10, 3, 1, 2, 'Gula', '2025-10-18 03:56:22', 'pending'),
(5, 10, 5, 1, 1, 'Teh Celup', '2025-10-18 03:56:22', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` int(11) NOT NULL,
  `nama_shift` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shift`
--

INSERT INTO `shift` (`id`, `nama_shift`) VALUES
(1, 'Shift 1'),
(2, 'Shift 2'),
(3, 'Shift 3');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nip` varchar(50) NOT NULL,
  `gmail` varchar(255) NOT NULL,
  `password` char(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatars` varchar(255) DEFAULT NULL,
  `departemen` varchar(255) DEFAULT NULL,
  `total_kupon` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `nip`, `gmail`, `password`, `created_at`, `avatars`, `departemen`, `total_kupon`) VALUES
(1, 'Sahroni', '12565484', 'sahroni@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-09-30 15:45:31', 'assets/img/avatars/avatar_u1_20251011053851.png', 'Human Resources', 0),
(2, 'andri', '', 'andri@ggs.com', '529ca8050a00180790cf88b63468826a', '2025-10-08 15:18:15', NULL, NULL, 0),
(3, 'naruto', '', 'naruto@gmail.com', '1463ccd2104eeb36769180b8a0c86bb6', '2025-10-08 15:21:18', NULL, NULL, 0),
(4, 'haki', '', 'haki2@gg.com', '1463ccd2104eeb36769180b8a0c86bb6', '2025-10-08 15:21:43', NULL, NULL, 0),
(6, 'amanda indah rahayu ningsih', '', 'amanda@gmail.com', '6209804952225ab3d14348307b5a4a27', '2025-10-10 16:46:59', NULL, NULL, 0),
(7, 'mamang eeng', '25454511', 'mamang@gmail.com', '3bd3feb3f927d7c1dace62e7997bcd94', '2025-10-11 03:49:21', 'assets/img/avatars/avatar_u7_20251011063559.png', 'Operations', 0),
(10, 'manda', '', 'manda@gmail.com', '86cc266e1c70ed60524b9f23c79e3a28', '2025-10-18 02:42:36', NULL, NULL, 6);

-- --------------------------------------------------------

--
-- Table structure for table `vendorkantin`
--

CREATE TABLE `vendorkantin` (
  `id` int(10) UNSIGNED NOT NULL,
  `nama` varchar(150) NOT NULL,
  `nama_vendor` varchar(100) DEFAULT NULL,
  `gmail` varchar(255) NOT NULL,
  `password` char(32) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendorkantin`
--

INSERT INTO `vendorkantin` (`id`, `nama`, `nama_vendor`, `gmail`, `password`, `created_at`) VALUES
(1, 'CV Katering', '0', 'vendor@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-09-30 15:45:31'),
(4, 'julian', '1', 'julian@gmail.com', '0d7b7c838c8fdd728bbb3ccb2cb3078a', '2025-10-13 15:43:25');

-- --------------------------------------------------------

--
-- Table structure for table `week`
--

CREATE TABLE `week` (
  `id` int(11) NOT NULL,
  `year_id` int(11) NOT NULL,
  `week_number` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `week`
--

INSERT INTO `week` (`id`, `year_id`, `week_number`) VALUES
(1, 1, 1),
(2, 1, 2),
(3, 1, 3),
(4, 1, 4),
(5, 1, 5),
(6, 1, 6),
(7, 1, 7),
(8, 1, 8),
(9, 1, 9),
(10, 1, 10),
(11, 1, 11),
(12, 1, 12),
(13, 1, 13),
(14, 1, 14),
(15, 1, 15),
(16, 1, 16),
(17, 1, 17),
(18, 1, 18),
(19, 1, 19),
(20, 1, 20),
(21, 1, 21),
(22, 1, 22),
(23, 1, 23),
(24, 1, 24),
(25, 1, 25),
(26, 1, 26),
(27, 1, 27),
(28, 1, 28),
(29, 1, 29),
(30, 1, 30),
(31, 1, 31),
(32, 1, 32),
(33, 1, 33),
(34, 1, 34),
(35, 1, 35),
(36, 1, 36),
(37, 1, 37),
(38, 1, 38),
(39, 1, 39),
(40, 1, 40),
(41, 1, 41),
(42, 1, 42),
(43, 1, 43),
(44, 1, 44),
(45, 1, 45),
(46, 1, 46),
(47, 1, 47),
(48, 1, 48),
(49, 1, 49),
(50, 1, 50),
(51, 1, 51),
(52, 1, 52);

-- --------------------------------------------------------

--
-- Table structure for table `year`
--

CREATE TABLE `year` (
  `id` int(11) NOT NULL,
  `year_value` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `year`
--

INSERT INTO `year` (`id`, `year_value`) VALUES
(1, 2025),
(2, 2026),
(3, 2027);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_gmail` (`gmail`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `kupon_history`
--
ALTER TABLE `kupon_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `order_id` (`order_id`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD KEY `week_id` (`week_id`);

--
-- Indexes for table `menu_images`
--
ALTER TABLE `menu_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_week_day` (`week_id`,`day`);

--
-- Indexes for table `nama_vendor`
--
ALTER TABLE `nama_vendor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `name` (`name`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `week_id` (`week_id`),
  ADD KEY `year_id` (`year_id`),
  ADD KEY `plant_id` (`plant_id`),
  ADD KEY `place_id` (`place_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `order_menus`
--
ALTER TABLE `order_menus`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `menu_id` (`menu_id`);

--
-- Indexes for table `pic_kantin`
--
ALTER TABLE `pic_kantin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_admin_gmail` (`gmail`);

--
-- Indexes for table `place`
--
ALTER TABLE `place`
  ADD PRIMARY KEY (`id`),
  ADD KEY `plant_id` (`plant_id`);

--
-- Indexes for table `plant`
--
ALTER TABLE `plant`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redeem_items`
--
ALTER TABLE `redeem_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `redemption_history`
--
ALTER TABLE `redemption_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `item_id` (`item_id`);

--
-- Indexes for table `shift`
--
ALTER TABLE `shift`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_users_gmail` (`gmail`);

--
-- Indexes for table `vendorkantin`
--
ALTER TABLE `vendorkantin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uq_vendor_gmail` (`gmail`);

--
-- Indexes for table `week`
--
ALTER TABLE `week`
  ADD PRIMARY KEY (`id`),
  ADD KEY `year_id` (`year_id`);

--
-- Indexes for table `year`
--
ALTER TABLE `year`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `kupon_history`
--
ALTER TABLE `kupon_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `menu_images`
--
ALTER TABLE `menu_images`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `nama_vendor`
--
ALTER TABLE `nama_vendor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `order_menus`
--
ALTER TABLE `order_menus`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=140;

--
-- AUTO_INCREMENT for table `pic_kantin`
--
ALTER TABLE `pic_kantin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `place`
--
ALTER TABLE `place`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `plant`
--
ALTER TABLE `plant`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `redeem_items`
--
ALTER TABLE `redeem_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `redemption_history`
--
ALTER TABLE `redemption_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `vendorkantin`
--
ALTER TABLE `vendorkantin`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `week`
--
ALTER TABLE `week`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `year`
--
ALTER TABLE `year`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `kupon_history`
--
ALTER TABLE `kupon_history`
  ADD CONSTRAINT `kupon_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `kupon_history_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `week` (`id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `week` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`),
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`plant_id`) REFERENCES `plant` (`id`),
  ADD CONSTRAINT `orders_ibfk_4` FOREIGN KEY (`place_id`) REFERENCES `place` (`id`),
  ADD CONSTRAINT `orders_ibfk_5` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `order_menus`
--
ALTER TABLE `order_menus`
  ADD CONSTRAINT `order_menus_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `order_menus_ibfk_2` FOREIGN KEY (`menu_id`) REFERENCES `menu` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `place`
--
ALTER TABLE `place`
  ADD CONSTRAINT `place_ibfk_1` FOREIGN KEY (`plant_id`) REFERENCES `plant` (`id`);

--
-- Constraints for table `redemption_history`
--
ALTER TABLE `redemption_history`
  ADD CONSTRAINT `redemption_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `redemption_history_ibfk_2` FOREIGN KEY (`item_id`) REFERENCES `redeem_items` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `week`
--
ALTER TABLE `week`
  ADD CONSTRAINT `week_ibfk_1` FOREIGN KEY (`year_id`) REFERENCES `year` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
