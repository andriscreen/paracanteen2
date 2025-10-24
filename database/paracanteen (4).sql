-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 24, 2025 at 04:11 PM
-- Server version: 8.0.30
-- PHP Version: 8.4.14

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `paracanteen`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nip` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
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
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1'
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
(10, 'Procurement', 1),
(11, 'Mancing', 1);

-- --------------------------------------------------------

--
-- Table structure for table `kupon_history`
--

CREATE TABLE `kupon_history` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `order_id` int NOT NULL,
  `jumlah_kupon` int NOT NULL,
  `tanggal_dapat` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `keterangan` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `meal_validations`
--

CREATE TABLE `meal_validations` (
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `rfid` varchar(20) NOT NULL,
  `order_id` int DEFAULT NULL,
  `week_id` int NOT NULL,
  `day` varchar(10) NOT NULL,
  `meal_type` enum('lunch','dinner') DEFAULT 'lunch',
  `validation_date` date NOT NULL,
  `validation_time` time NOT NULL,
  `status` enum('validated','cancelled') DEFAULT 'validated',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `menu`
--

CREATE TABLE `menu` (
  `id` int NOT NULL,
  `week_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `day` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `menu_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`id`, `week_id`, `vendor_id`, `day`, `menu_name`, `keterangan`) VALUES
(31, 43, 1, 'Senin', 'musang', 'musang'),
(32, 43, 2, 'Senin', 'batu', 'batu');

-- --------------------------------------------------------

--
-- Table structure for table `menu_images`
--

CREATE TABLE `menu_images` (
  `id` int NOT NULL,
  `week_id` int NOT NULL,
  `vendor_id` int NOT NULL,
  `day` varchar(20) COLLATE utf8mb4_general_ci NOT NULL,
  `image_url` varchar(255) COLLATE utf8mb4_general_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `menu_images`
--

INSERT INTO `menu_images` (`id`, `week_id`, `vendor_id`, `day`, `image_url`) VALUES
(2, 2, 1, 'Minggu', 'assets/img/menu/kuda.png'),
(5, 3, 1, 'Selasa', 'assets/img/menu/week3_Selasa.png'),
(6, 3, 1, 'Senin', '../assets/img/menu/week3_Senin.jpeg'),
(8, 1, 1, 'Selasa', 'assets/img/menu/week1_Selasa.png'),
(20, 43, 1, 'Senin', 'assets/img/menu/week43_vendor1_Senin.jpeg'),
(21, 43, 2, 'Senin', 'assets/img/menu/week43_vendor2_Senin.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `nama_vendor`
--

CREATE TABLE `nama_vendor` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `is_active` tinyint(1) DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `nama_vendor`
--

INSERT INTO `nama_vendor` (`id`, `name`, `is_active`) VALUES
(1, 'Serikandi', 1),
(2, 'Sinar Budi', 1),
(3, 'Andri Kantin', 1);

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int NOT NULL,
  `week_id` int NOT NULL,
  `year_id` int NOT NULL,
  `plant_id` int NOT NULL,
  `place_id` int NOT NULL,
  `shift_id` int DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `user_id` int UNSIGNED NOT NULL,
  `makan_senin` tinyint(1) DEFAULT '0',
  `kupon_senin` tinyint(1) DEFAULT '0',
  `libur_senin` tinyint(1) DEFAULT '0',
  `makan_selasa` tinyint(1) DEFAULT '0',
  `kupon_selasa` tinyint(1) DEFAULT '0',
  `libur_selasa` tinyint(1) DEFAULT '0',
  `makan_rabu` tinyint(1) DEFAULT '0',
  `kupon_rabu` tinyint(1) DEFAULT '0',
  `libur_rabu` tinyint(1) DEFAULT '0',
  `makan_kamis` tinyint(1) DEFAULT '0',
  `kupon_kamis` tinyint(1) DEFAULT '0',
  `libur_kamis` tinyint(1) DEFAULT '0',
  `makan_jumat` tinyint(1) DEFAULT '0',
  `kupon_jumat` tinyint(1) DEFAULT '0',
  `libur_jumat` tinyint(1) DEFAULT '0',
  `makan_sabtu` tinyint(1) DEFAULT '0',
  `kupon_sabtu` tinyint(1) DEFAULT '0',
  `libur_sabtu` tinyint(1) DEFAULT '0',
  `makan_minggu` tinyint(1) DEFAULT '0',
  `kupon_minggu` tinyint(1) DEFAULT '0',
  `libur_minggu` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `after_order_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    DECLARE total_kupon_baru INT DEFAULT 0;
    
    -- Hitung total kupon dari order baru
    SET total_kupon_baru = 
        NEW.kupon_senin + NEW.kupon_selasa + NEW.kupon_rabu + 
        NEW.kupon_kamis + NEW.kupon_jumat + NEW.kupon_sabtu + NEW.kupon_minggu;
    
    -- Jika ada kupon, tambahkan ke kupon_history dan update users
    IF total_kupon_baru > 0 THEN
        -- Tambahkan record ke kupon_history
        INSERT INTO kupon_history (user_id, order_id, jumlah_kupon, keterangan)
        VALUES (NEW.user_id, NEW.id, total_kupon_baru, 'Kupon dari pemesanan makanan');
        
        -- PERBAIKAN: Tambahkan kupon baru ke total yang ada
        UPDATE users 
        SET total_kupon = total_kupon + total_kupon_baru 
        WHERE id = NEW.user_id;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `pic_kantin`
--

CREATE TABLE `pic_kantin` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `departemen` varchar(50) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pic_kantin`
--

INSERT INTO `pic_kantin` (`id`, `nama`, `departemen`, `gmail`, `password`, `created_at`) VALUES
(1, 'PIC Kantin 1', 'PICK001', 'kantin1@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-10-13 14:02:29'),
(4, 'Andri GGS', '4', 'andri@gmail.com', '6bd3108684ccc9dfd40b126877f850b0', '2025-10-20 10:56:54'),
(5, 'eeng', '2', 'eeng@gmail.com', 'c58ee092d8b7aa80048ce3e7a721e08a', '2025-10-20 11:47:04');

-- --------------------------------------------------------

--
-- Table structure for table `place`
--

CREATE TABLE `place` (
  `id` int NOT NULL,
  `plant_id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
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
(7, 6, 'Kantin J6 Belakang'),
(8, 6, 'Kantin J6 yang kayak cafe itu');

-- --------------------------------------------------------

--
-- Table structure for table `plant`
--

CREATE TABLE `plant` (
  `id` int NOT NULL,
  `name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL
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
  `id` int NOT NULL,
  `nama` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `kupon` int NOT NULL,
  `gambar` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `keterangan` text COLLATE utf8mb4_general_ci,
  `aktif` tinyint(1) DEFAULT '1'
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
  `id` int NOT NULL,
  `user_id` int UNSIGNED NOT NULL,
  `item_id` int NOT NULL,
  `quantity` int NOT NULL,
  `kupon_used` int NOT NULL,
  `item_name` varchar(100) COLLATE utf8mb4_general_ci NOT NULL,
  `redemption_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_general_ci DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `redemption_history`
--

INSERT INTO `redemption_history` (`id`, `user_id`, `item_id`, `quantity`, `kupon_used`, `item_name`, `redemption_date`, `status`) VALUES
(1, 10, 3, 2, 4, 'Gula', '2025-10-18 02:56:29', 'pending'),
(2, 10, 2, 1, 3, 'Minyak Goreng', '2025-10-18 02:58:42', 'pending'),
(3, 10, 2, 1, 3, 'Minyak Goreng', '2025-10-18 03:07:20', 'pending'),
(4, 10, 3, 1, 2, 'Gula', '2025-10-18 03:56:22', 'pending'),
(5, 10, 5, 1, 1, 'Teh Celup', '2025-10-18 03:56:22', 'pending'),
(6, 11, 3, 2, 4, 'Gula', '2025-10-18 10:54:19', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `shift`
--

CREATE TABLE `shift` (
  `id` int NOT NULL,
  `nama_shift` varchar(50) COLLATE utf8mb4_general_ci NOT NULL
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
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nip` varchar(50) COLLATE utf8mb4_general_ci NOT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) COLLATE utf8mb4_general_ci NOT NULL,
  `rfid` varchar(20) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `avatars` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `departemen` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `total_kupon` int DEFAULT '0',
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `nama`, `nip`, `gmail`, `password`, `rfid`, `created_at`, `avatars`, `departemen`, `total_kupon`, `updated_at`) VALUES
(1, 'Sahroni Nugroho', '12565484', 'sahroni@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2070652015', '2025-09-30 15:45:31', 'assets/img/avatars/avatar_u1_20251011053851.png', 'Human Resources', 0, '2025-10-24 16:03:54'),
(2, 'andri', '', 'andri@ggs.com', '529ca8050a00180790cf88b63468826a', NULL, '2025-10-08 15:18:15', NULL, NULL, 0, '2025-10-18 12:07:11'),
(6, 'amanda indah rahayu ningsih', '55555', 'amanda@gmail.com', '6209804952225ab3d14348307b5a4a27', NULL, '2025-10-10 16:46:59', NULL, NULL, 0, '2025-10-18 12:08:02'),
(7, 'mamang eeng', '25454511', 'mamang@gmail.com', '3bd3feb3f927d7c1dace62e7997bcd94', NULL, '2025-10-11 03:49:21', 'assets/img/avatars/avatar_u7_20251011063559.png', 'Operations', 0, '2025-10-18 12:07:11'),
(10, 'manda', '6566565', 'manda@gmail.com', '86cc266e1c70ed60524b9f23c79e3a28', NULL, '2025-10-18 02:42:36', 'assets/img/avatars/avatar_u10_20251019051901.png', 'Procurement', 0, '2025-10-24 13:36:47'),
(11, 'mahdi', '', 'mahdi@gmail.com', 'f9c24b8f961d48841a9838cca5274d8d', NULL, '2025-10-18 10:45:37', NULL, NULL, 0, '2025-10-24 13:36:47'),
(12, 'hantu', '', 'hantu@gmail.com', '805a52ccb5200f0d38cf57dda28ba545', NULL, '2025-10-19 07:06:14', NULL, NULL, 0, '2025-10-24 13:36:47'),
(13, 'kamen', '', 'kamen@gmail.com', 'ff24af3e638218b0f26bd6f2113131d1', NULL, '2025-10-20 12:10:09', NULL, 'Human Resources', 0, '2025-10-24 13:36:47');

-- --------------------------------------------------------

--
-- Table structure for table `vendorkantin`
--

CREATE TABLE `vendorkantin` (
  `id` int UNSIGNED NOT NULL,
  `nama` varchar(150) COLLATE utf8mb4_general_ci NOT NULL,
  `nama_vendor` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `gmail` varchar(255) COLLATE utf8mb4_general_ci NOT NULL,
  `password` char(32) COLLATE utf8mb4_general_ci NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vendorkantin`
--

INSERT INTO `vendorkantin` (`id`, `nama`, `nama_vendor`, `gmail`, `password`, `created_at`) VALUES
(1, 'CV Katering', '0', 'vendor@example.com', '482c811da5d5b4bc6d497ffa98491e38', '2025-09-30 15:45:31'),
(4, 'julian', '1', 'julian@gmail.com', '0d7b7c838c8fdd728bbb3ccb2cb3078a', '2025-10-13 15:43:25'),
(5, 'andri', '3', 'andri@andri.com', '6bd3108684ccc9dfd40b126877f850b0', '2025-10-24 12:42:27');

-- --------------------------------------------------------

--
-- Table structure for table `week`
--

CREATE TABLE `week` (
  `id` int NOT NULL,
  `year_id` int NOT NULL,
  `week_number` int NOT NULL
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
  `id` int NOT NULL,
  `year_value` int NOT NULL
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
-- Indexes for table `meal_validations`
--
ALTER TABLE `meal_validations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `week_id` (`week_id`),
  ADD KEY `idx_validation_date` (`validation_date`),
  ADD KEY `idx_rfid_date` (`rfid`,`validation_date`),
  ADD KEY `idx_user_week_day` (`user_id`,`week_id`,`day`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_menu_week_day_vendor` (`week_id`,`day`,`vendor_id`),
  ADD KEY `week_id` (`week_id`),
  ADD KEY `idx_menu_week_vendor` (`week_id`,`vendor_id`),
  ADD KEY `idx_menu_vendor` (`vendor_id`);

--
-- Indexes for table `menu_images`
--
ALTER TABLE `menu_images`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_week_day_vendor` (`week_id`,`day`,`vendor_id`),
  ADD KEY `fk_menu_images_vendor` (`vendor_id`);

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
  ADD UNIQUE KEY `uq_users_gmail` (`gmail`),
  ADD KEY `idx_rfid` (`rfid`);

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
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `kupon_history`
--
ALTER TABLE `kupon_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `meal_validations`
--
ALTER TABLE `meal_validations`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `menu_images`
--
ALTER TABLE `menu_images`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `nama_vendor`
--
ALTER TABLE `nama_vendor`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `pic_kantin`
--
ALTER TABLE `pic_kantin`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `place`
--
ALTER TABLE `place`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `plant`
--
ALTER TABLE `plant`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `redeem_items`
--
ALTER TABLE `redeem_items`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `redemption_history`
--
ALTER TABLE `redemption_history`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `shift`
--
ALTER TABLE `shift`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `vendorkantin`
--
ALTER TABLE `vendorkantin`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `week`
--
ALTER TABLE `week`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `year`
--
ALTER TABLE `year`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

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
-- Constraints for table `meal_validations`
--
ALTER TABLE `meal_validations`
  ADD CONSTRAINT `meal_validations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `meal_validations_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`),
  ADD CONSTRAINT `meal_validations_ibfk_3` FOREIGN KEY (`week_id`) REFERENCES `week` (`id`);

--
-- Constraints for table `menu`
--
ALTER TABLE `menu`
  ADD CONSTRAINT `fk_menu_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `nama_vendor` (`id`),
  ADD CONSTRAINT `menu_ibfk_1` FOREIGN KEY (`week_id`) REFERENCES `week` (`id`);

--
-- Constraints for table `menu_images`
--
ALTER TABLE `menu_images`
  ADD CONSTRAINT `fk_menu_images_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `nama_vendor` (`id`);

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
