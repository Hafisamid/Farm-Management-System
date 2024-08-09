-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Aug 09, 2024 at 02:25 AM
-- Server version: 10.4.25-MariaDB
-- PHP Version: 7.4.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `farm_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `name`, `description`) VALUES
(1, 'Electric Bill', 'An electric bill details the amount owed for electricity usage over a specific period, including charges for energy consumption, taxes, and service fees. It typically provides a breakdown of usage, cost per unit, and payment due dates.\r\n\r\n\r\n\r\n\r\n\r\n\r\n'),
(2, 'Water Bill', 'A water bill is a statement issued by a utility company that details the charges for water usage over a specific period. It typically includes information on the amount of water consumed, the cost per unit of water, and any additional fees or taxes.'),
(3, 'Rent', 'A rent bill is a document issued by a landlord or property management company to a tenant, detailing the amount of rent due for a specific period. It typically includes payment due dates, the amount owed, and any additional fees or charges.'),
(4, 'Cow Purchase', 'Cow purchase involves acquiring cows for various purposes, such as dairy production, beef production, or breeding. It requires evaluating factors like the cow\'s health, breed, age, and cost to ensure a beneficial investment.'),
(5, 'Milk Purchase', 'Cow milk purchase involves buying fresh or processed milk produced by cows, available in various forms such as whole, skimmed, and organic. It is a common household staple used for drinking, cooking, and baking, offering essential nutrients like calcium and protein.'),
(6, 'Food Stock ', '\r\nFood stock refers to the reserve supply of food products maintained for future use, ensuring availability during shortages or increased demand. It includes a variety of perishable and non-perishable items stored for both short-term and long-term needs.'),
(7, 'Vaccine Stock', 'Vaccine stock refers to the quantity of vaccine doses available for distribution and administration. It is a critical aspect of public health management to ensure sufficient supply for immunization programs.');

-- --------------------------------------------------------

--
-- Table structure for table `cow`
--

CREATE TABLE `cow` (
  `Cow_Id` int(11) NOT NULL,
  `User_Id` int(11) DEFAULT NULL,
  `Gender` enum('male','female') NOT NULL,
  `Cow_Image` varchar(255) DEFAULT NULL,
  `Type` varchar(50) DEFAULT NULL,
  `Shed_Id` int(11) DEFAULT NULL,
  `Category_Id` int(11) DEFAULT NULL,
  `Quantity` int(11) DEFAULT NULL,
  `Last_Update_Timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `Last_Updated_By` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cow`
--

INSERT INTO `cow` (`Cow_Id`, `User_Id`, `Gender`, `Cow_Image`, `Type`, `Shed_Id`, `Category_Id`, `Quantity`, `Last_Update_Timestamp`, `Last_Updated_By`) VALUES
(62, 18, 'male', 'uploads/Wagyu Cow.jpeg', 'Wagyu Cow ', 17, 2, 30, '2024-06-19 23:56:25', 18),
(63, 18, 'male', 'uploads/Wagyu Cow.jpeg', 'Wagyu Cow ', 17, 4, 40, '2024-06-19 23:56:53', 18),
(64, 18, 'female', 'uploads/Wagyu Cow.jpeg', 'Wagyu Cow ', 17, 2, 30, '2024-06-19 23:58:22', 18),
(65, 18, 'female', 'uploads/Wagyu Cow.jpeg', 'Wagyu Cow ', 17, 4, 60, '2024-06-19 23:58:54', 18),
(66, 18, 'male', 'uploads/Holstein Frisia.jpg', 'Holstein Frisia', 18, 2, 12, '2024-06-20 00:00:50', 18),
(67, 18, 'female', 'uploads/Holstein Frisia.jpg', 'Holstein Frisia', 18, 1, 30, '2024-06-20 00:01:15', 18),
(68, 18, 'female', 'uploads/Holstein Frisia.jpg', 'Holstein Frisia', 18, 3, 123, '2024-06-20 00:01:34', 18),
(69, 18, 'male', 'uploads/Holstein Frisia.jpg', 'Holstein Frisia', 18, 4, 20, '2024-06-20 00:02:00', 18),
(70, 18, 'male', 'uploads/Hereford Breeder.jpeg', 'Hereford Breeder', 19, 2, 124, '2024-06-20 00:07:41', 18),
(71, 18, 'male', 'uploads/Hereford Breeder.jpeg', 'Hereford Breeder', 19, 4, 45, '2024-06-20 00:08:08', 18),
(72, 18, 'female', 'uploads/Hereford Breeder.jpeg', 'Hereford Breeder', 19, 3, 20, '2024-06-20 00:11:27', 18),
(73, 18, 'male', 'uploads/Australian Black Angus.jpg', 'Australia Black Angus', 20, 2, 12, '2024-06-20 00:15:05', 18),
(74, 18, 'male', 'uploads/Australian Black Angus.jpg', 'Australia Black Angus', 20, 4, 12, '2024-06-20 00:15:22', 18),
(75, 18, 'male', 'uploads/Belted Galloway Cow.webp', 'Belted Galloway', 21, 2, 23, '2024-06-20 00:20:36', 18),
(76, 18, 'female', 'uploads/Belted Galloway Cow.webp', 'Belted Galloway', 21, 3, 30, '2024-06-20 00:20:54', 18),
(77, 18, 'female', 'uploads/Brahman.webp', 'Brahman', 22, 1, 12, '2024-06-20 00:25:49', 18),
(78, 18, 'female', 'uploads/Brahman.webp', 'Brahman', 22, 3, 123, '2024-06-20 00:26:05', 18),
(79, 18, 'male', 'uploads/Brahman.webp', 'Brahman', 22, 4, 12, '2024-06-20 00:29:22', 18),
(80, 18, 'male', 'uploads/Holstein Frisia.jpg', 'Holstein Frisia', 27, 2, 12, '2024-06-20 03:17:25', 18);

-- --------------------------------------------------------

--
-- Table structure for table `cow_category`
--

CREATE TABLE `cow_category` (
  `Category_Id` int(11) NOT NULL,
  `Category_Name` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cow_category`
--

INSERT INTO `cow_category` (`Category_Id`, `Category_Name`, `Description`) VALUES
(1, 'Dairy Cows', 'Dairy cows are a specific breed of cattle that are raised for the primary purpose of producing milk. They are typically highly specialized breeds, selected and bred for their ability to produce large quantities of milk. These cows are typically well-cared for and live in comfortable conditions on dairy farms.'),
(2, ' Beef Cow', 'Beef cattle, often simply referred to as beef cows, are cattle primarily raised for the purpose of producing beef..'),
(3, 'Dairy Cows Available Milk ', 'A dairy cow available for milk production typically produces high-quality milk consistently and efficiently, making it ideal for dairy farming.'),
(4, ' Beef Cow Available Meat', 'A beef cow available for meat production is bred and raised to yield high-quality, tender meat efficiently, making it suitable for beef farming.');

-- --------------------------------------------------------

--
-- Table structure for table `cow_sale`
--

CREATE TABLE `cow_sale` (
  `CowSale_Id` int(11) NOT NULL,
  `Cow_Id` int(11) NOT NULL,
  `Category_Id` int(11) NOT NULL,
  `Name` varchar(100) NOT NULL,
  `Quantity` decimal(10,2) NOT NULL,
  `Cost_Price_per_kg` decimal(10,2) NOT NULL,
  `Sale_Price_per_kg` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `cow_sale`
--

INSERT INTO `cow_sale` (`CowSale_Id`, `Cow_Id`, `Category_Id`, `Name`, `Quantity`, `Cost_Price_per_kg`, `Sale_Price_per_kg`) VALUES
(14, 63, 4, 'Wagyu Meat', '986.00', '1000.00', '4500.00'),
(15, 69, 4, 'Holstein Meat', '1989.00', '17.00', '18.00'),
(16, 71, 4, 'Hereford Meat', '2991.00', '26.32', '27.00'),
(17, 74, 4, 'Black Angus Meat', '293.00', '25.30', '27.00'),
(18, 79, 4, 'Brahman Meat', '995.00', '14.11', '16.00'),
(19, 69, 4, 'Holstein Meat', '100.00', '12.00', '21.00');

-- --------------------------------------------------------

--
-- Table structure for table `customer`
--

CREATE TABLE `customer` (
  `customer_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `due_pay` decimal(10,2) NOT NULL,
  `status_pay` enum('Paid','Pending') NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `customer`
--

INSERT INTO `customer` (`customer_id`, `name`, `due_pay`, `status_pay`, `created_at`) VALUES
(7, 'Mohamd Imran Saiful', '282.00', 'Paid', '2024-06-19 19:45:00'),
(15, 'Irfan danial', '726.00', 'Paid', '2024-06-19 18:24:00'),
(16, 'Nurul Saadiah Binti Rahiman', '132.00', 'Paid', '2024-05-19 18:39:00'),
(17, 'Saidatul Insyirah Binti Kasmin', '1994.00', 'Paid', '2024-05-21 02:30:00'),
(18, 'Suresh A/L Rajit', '13555.80', 'Paid', '2024-04-20 08:35:00'),
(19, 'Salman Bin Hadi', '1.20', 'Pending', '2024-04-20 08:36:00'),
(20, 'Hadi Rashid Bin Samsul', '0.00', 'Pending', '2024-04-20 08:40:00'),
(21, 'Sulaiman Bin Sarifudin', '102.00', 'Paid', '2024-03-20 08:37:00'),
(22, 'Suzana Binti Kamsuri', '4668.60', 'Paid', '2024-02-20 08:37:00'),
(23, 'Suraya Binti Said', '4500.00', 'Pending', '2024-02-20 08:37:00'),
(24, 'Sukar Bin Joekarno', '9000.00', 'Paid', '2024-01-20 08:38:00'),
(25, 'Salah Bin Salahudin', '158.00', 'Paid', '2023-12-20 08:38:00'),
(26, 'Rizalman Bin Ruzaidi', '4801.60', 'Paid', '2023-11-20 08:39:00'),
(27, 'Manaf Bin Sayaf', '427.20', 'Paid', '2023-10-20 08:39:00'),
(28, 'Henry William', '13713.60', 'Paid', '2023-09-20 08:40:00'),
(29, 'Sabri Bin Yunus', '513.20', 'Paid', '2023-08-20 00:43:00'),
(30, 'Susanti A/P Ah Seng', '9427.20', 'Paid', '2023-07-20 08:41:00'),
(31, 'hafis', '4590.60', 'Paid', '2024-06-20 11:20:00');

-- --------------------------------------------------------

--
-- Table structure for table `expense`
--

CREATE TABLE `expense` (
  `expense_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `date` date NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `prove` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `expense`
--

INSERT INTO `expense` (`expense_id`, `category_id`, `date`, `total_price`, `prove`) VALUES
(1, 2, '2024-06-17', '21.00', 'uploads/water bill.jpeg'),
(12, 1, '2024-06-19', '122.00', 'uploads/bill water.webp'),
(14, 7, '2024-06-19', '12.00', 'uploads/bill water.webp'),
(15, 1, '2024-06-19', '1121.00', 'uploads/bill water.webp'),
(16, 3, '2024-06-20', '212.00', 'uploads/Screenshot 2024-05-20 110236.png'),
(17, 6, '2024-06-20', '3000.00', 'uploads/water bill.jpeg');

-- --------------------------------------------------------

--
-- Table structure for table `milk_sale`
--

CREATE TABLE `milk_sale` (
  `MilkSale_Id` int(11) NOT NULL,
  `User_Id` int(11) NOT NULL,
  `Cow_Id` int(11) NOT NULL,
  `Category_Id` int(11) NOT NULL,
  `Litre_Collect` decimal(10,2) NOT NULL,
  `Cost_Price_Per_Litre` decimal(10,2) NOT NULL,
  `Sale_Price_Per_Litre` decimal(10,2) NOT NULL,
  `Date_Collected` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `milk_sale`
--

INSERT INTO `milk_sale` (`MilkSale_Id`, `User_Id`, `Cow_Id`, `Category_Id`, `Litre_Collect`, `Cost_Price_Per_Litre`, `Sale_Price_Per_Litre`, `Date_Collected`) VALUES
(43, 18, 68, 3, '9965.00', '0.40', '0.60', '2024-06-19'),
(44, 18, 72, 3, '351.00', '149.24', '150.00', '2024-06-19'),
(45, 18, 76, 3, '992.00', '57.92', '60.00', '2024-06-21'),
(46, 18, 78, 3, '992.00', '1.81', '3.00', '2024-05-31');

-- --------------------------------------------------------

--
-- Table structure for table `sales_record`
--

CREATE TABLE `sales_record` (
  `record_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `sale_type` enum('milk','cow') NOT NULL,
  `item_id` int(11) NOT NULL,
  `quantity` decimal(10,2) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `sale_date` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sales_record`
--

INSERT INTO `sales_record` (`record_id`, `customer_id`, `sale_type`, `item_id`, `quantity`, `price`, `sale_date`) VALUES
(1, 7, 'cow', 13, '1.00', '12.00', '2024-06-19 11:42:23'),
(2, 7, 'cow', 13, '1.00', '12.00', '2024-06-19 11:42:26'),
(3, 7, 'milk', 36, '2.00', '42.00', '2024-06-19 12:07:40'),
(4, 11, 'milk', 36, '2.00', '42.00', '2024-06-19 12:07:53'),
(5, 13, 'milk', 36, '2.00', '42.00', '2024-06-19 12:14:56'),
(6, 13, 'cow', 13, '2.00', '24.00', '2024-06-19 12:14:57'),
(7, 15, 'milk', 36, '22.00', '462.00', '2024-06-19 12:25:48'),
(8, 15, 'cow', 13, '22.00', '264.00', '2024-06-19 12:25:48'),
(9, 16, 'milk', 36, '2.00', '42.00', '2024-06-19 12:39:52'),
(10, 16, 'cow', 13, '2.00', '24.00', '2024-06-19 12:39:52'),
(11, 16, 'milk', 36, '2.00', '42.00', '2024-06-19 13:43:38'),
(12, 16, 'cow', 13, '2.00', '24.00', '2024-06-19 13:43:38'),
(13, 7, 'milk', 36, '2.00', '42.00', '2024-06-19 13:44:53'),
(14, 17, 'milk', 41, '1.00', '21.00', '2024-06-19 20:31:23'),
(15, 17, 'cow', 13, '2.00', '24.00', '2024-06-19 20:31:23'),
(16, 17, 'milk', 41, '1.00', '21.00', '2024-06-19 20:31:27'),
(17, 17, 'cow', 13, '2.00', '24.00', '2024-06-19 20:31:27'),
(18, 17, 'cow', 13, '68.00', '952.00', '2024-06-19 20:38:39'),
(19, 17, 'cow', 13, '68.00', '952.00', '2024-06-19 20:38:41'),
(20, 18, 'milk', 43, '3.00', '1.80', '2024-06-20 02:41:52'),
(21, 18, 'cow', 14, '3.00', '13500.00', '2024-06-20 02:41:52'),
(22, 18, 'cow', 15, '3.00', '54.00', '2024-06-20 02:41:52'),
(23, 19, 'milk', 43, '2.00', '1.20', '2024-06-20 02:42:24'),
(24, 21, 'milk', 43, '20.00', '12.00', '2024-06-20 02:42:53'),
(25, 21, 'cow', 15, '2.00', '36.00', '2024-06-20 02:42:53'),
(26, 21, 'cow', 16, '2.00', '54.00', '2024-06-20 02:42:53'),
(27, 22, 'milk', 43, '1.00', '0.60', '2024-06-20 02:43:33'),
(28, 22, 'milk', 44, '1.00', '150.00', '2024-06-20 02:43:33'),
(29, 22, 'cow', 14, '1.00', '4500.00', '2024-06-20 02:43:33'),
(30, 22, 'cow', 15, '1.00', '18.00', '2024-06-20 02:43:33'),
(31, 23, 'cow', 14, '1.00', '4500.00', '2024-06-20 02:44:01'),
(32, 7, 'cow', 15, '1.00', '18.00', '2024-06-20 02:44:25'),
(33, 7, 'cow', 16, '2.00', '54.00', '2024-06-20 02:44:25'),
(34, 7, 'cow', 17, '2.00', '54.00', '2024-06-20 02:44:25'),
(35, 24, 'cow', 14, '2.00', '9000.00', '2024-06-20 02:44:39'),
(36, 25, 'cow', 15, '1.00', '18.00', '2024-06-20 02:45:51'),
(37, 25, 'cow', 16, '2.00', '54.00', '2024-06-20 02:45:51'),
(38, 25, 'cow', 17, '2.00', '54.00', '2024-06-20 02:45:51'),
(39, 25, 'cow', 18, '2.00', '32.00', '2024-06-20 02:45:51'),
(40, 26, 'milk', 43, '1.00', '0.60', '2024-06-20 02:46:31'),
(41, 26, 'milk', 44, '1.00', '150.00', '2024-06-20 02:46:31'),
(42, 26, 'milk', 45, '1.00', '60.00', '2024-06-20 02:46:31'),
(43, 26, 'milk', 46, '1.00', '3.00', '2024-06-20 02:46:31'),
(44, 26, 'cow', 14, '1.00', '4500.00', '2024-06-20 02:46:31'),
(45, 26, 'cow', 15, '1.00', '18.00', '2024-06-20 02:46:31'),
(46, 26, 'cow', 16, '1.00', '27.00', '2024-06-20 02:46:31'),
(47, 26, 'cow', 17, '1.00', '27.00', '2024-06-20 02:46:31'),
(48, 26, 'cow', 18, '1.00', '16.00', '2024-06-20 02:46:31'),
(49, 27, 'milk', 43, '2.00', '1.20', '2024-06-20 02:46:50'),
(50, 27, 'milk', 44, '2.00', '300.00', '2024-06-20 02:46:50'),
(51, 27, 'milk', 45, '2.00', '120.00', '2024-06-20 02:46:50'),
(52, 27, 'milk', 46, '2.00', '6.00', '2024-06-20 02:46:50'),
(53, 28, 'milk', 43, '1.00', '0.60', '2024-06-20 02:47:08'),
(54, 28, 'milk', 44, '1.00', '150.00', '2024-06-20 02:47:08'),
(55, 28, 'milk', 45, '1.00', '60.00', '2024-06-20 02:47:08'),
(56, 28, 'milk', 46, '1.00', '3.00', '2024-06-20 02:47:08'),
(57, 28, 'cow', 14, '3.00', '13500.00', '2024-06-20 02:47:08'),
(58, 29, 'milk', 43, '2.00', '1.20', '2024-06-20 02:47:26'),
(59, 29, 'milk', 44, '2.00', '300.00', '2024-06-20 02:47:26'),
(60, 29, 'milk', 45, '2.00', '120.00', '2024-06-20 02:47:26'),
(61, 29, 'milk', 46, '2.00', '6.00', '2024-06-20 02:47:26'),
(62, 29, 'cow', 17, '2.00', '54.00', '2024-06-20 02:47:26'),
(63, 29, 'cow', 18, '2.00', '32.00', '2024-06-20 02:47:26'),
(64, 30, 'milk', 43, '2.00', '1.20', '2024-06-20 02:47:41'),
(65, 30, 'milk', 44, '2.00', '300.00', '2024-06-20 02:47:41'),
(66, 30, 'milk', 45, '2.00', '120.00', '2024-06-20 02:47:41'),
(67, 30, 'milk', 46, '2.00', '6.00', '2024-06-20 02:47:41'),
(68, 30, 'cow', 14, '2.00', '9000.00', '2024-06-20 02:47:41'),
(69, 31, 'milk', 43, '1.00', '0.60', '2024-06-20 05:27:39'),
(70, 31, 'cow', 14, '1.00', '4500.00', '2024-06-20 05:27:39'),
(71, 31, 'cow', 15, '2.00', '36.00', '2024-06-20 05:29:42'),
(72, 31, 'cow', 16, '2.00', '54.00', '2024-06-20 05:29:42');

-- --------------------------------------------------------

--
-- Table structure for table `shed`
--

CREATE TABLE `shed` (
  `Shed_Id` int(11) NOT NULL,
  `Name` varchar(50) NOT NULL,
  `Description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `shed`
--

INSERT INTO `shed` (`Shed_Id`, `Name`, `Description`) VALUES
(17, 'Shed1', 'shed 1'),
(18, 'Shed 2', 'Shed 2'),
(19, 'Shed 3', 'Shed 3'),
(20, 'Shed 4', 'Shed 4'),
(21, 'Shed 5', 'Shed 5'),
(22, 'Shed 6', 'Shed 6'),
(23, 'Shed 7', 'Shed 7'),
(24, 'Shed 8', 'Shed 8'),
(25, 'Shed 9', 'Shed 9'),
(26, 'Shed 10', 'Shed 10'),
(27, 'shed holstein', 'holstein cow');

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `User_Id` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(100) NOT NULL,
  `Full_Name` varchar(100) DEFAULT NULL,
  `Email` varchar(100) DEFAULT NULL,
  `Contact` varchar(20) DEFAULT NULL,
  `User_Type` enum('admin','farmer','health manager','worker') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`User_Id`, `Username`, `Password`, `Full_Name`, `Email`, `Contact`, `User_Type`) VALUES
(18, 'hafis', '$2y$10$QiWvotrjt2b1ER8ZJFtED.nETbBo7FMMVOdEZv1Km6TlrMQIYT0Mq', 'Mohamad Hafiszudin Bin Amid', 'hafisamid4@gmail.com', '0135267674', 'admin'),
(19, 'hafis2', '$2y$10$Bl6wmZDcQEBayb.aRL8ecuPf6i91Bj3BokKrStUbbqRVAzn26JdP6', 'Muhamad Hafis Suib Bun abdul Ghani', 'hafisamid490@gmail.com', '0135267676', 'worker'),
(20, 'hafis3', '$2y$10$.UV6PQhkB8hPndwTEqvcJe4AHjKhvdsu/KVyZRVjKCdt./eUoKqTG', 'Mohamad Hafiszudins Bin Amid', 'hafisamid4454@gmail.com', '0135267690', 'worker'),
(21, 'Siti Wan Maimunah', '$2y$10$1dr41AeCB/I4Juo8JpCU3ue7Zz4Nkbk65qfiy18Dx65HR5DF9o8VW', 'Siti Wan Maimunah ', 'wanmaimunah@gmail.com', '01365278876', 'worker'),
(22, 'Hilmi', '$2y$10$8flMZNyqnZBGD8wK4xslYOm3BpN5rhxulhtgiZrCCweZN.CJqwsvq', 'Hilmi Bin Tugi', 'hilmi4@gmail.com', '0167167872', 'worker'),
(23, 'Hilmian', '$2y$10$2NddoC9V1ngTWZJ7TYFW0uZFp0RdgW3hhYSnO9QmiUAzC..VuXZEa', 'Hilmian Bin Tugi', 'hilmian4@gmail.com', '016716343', 'worker'),
(24, 'hafis2', '$2y$10$BnD8BGLqeY6Bt5nk0yyPses3fb.gzeE0lt7ixZmSDmTPLQnu1bLKq', 'hafiszudin', 'hafisamid4@gmail.com', '0135267674', 'worker');

-- --------------------------------------------------------

--
-- Table structure for table `vaccine`
--

CREATE TABLE `vaccine` (
  `Vaccine_Id` int(11) NOT NULL,
  `User_Id` int(11) DEFAULT NULL,
  `Date_Vaccine` date DEFAULT NULL,
  `Shed_Id` int(11) DEFAULT NULL,
  `Dose` int(11) DEFAULT NULL,
  `Disease` varchar(255) DEFAULT NULL,
  `Duration` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `vaccine`
--

INSERT INTO `vaccine` (`Vaccine_Id`, `User_Id`, `Date_Vaccine`, `Shed_Id`, `Dose`, `Disease`, `Duration`) VALUES
(16, 18, '2024-06-20', 17, 2, 'Rota-Corona virus', ' six hours of birth'),
(17, 18, '2024-06-20', 18, 2, 'Bangs Vaccination', 'Four to Ten Months'),
(18, 18, '2024-06-20', 19, 2, 'IBR, PI3, BVD, BRSV-MLV', 'Thirteen to Sixteen Months'),
(19, 18, '2024-05-29', 21, 2, '5way Lepto', 'Thirteen to Sixteen Months'),
(20, 18, '2024-07-11', 22, 2, 'Vibrio', 'Thirteen to Sixteen Months'),
(22, 19, '2024-06-21', 19, 2, 'Vibrio', 'Thirteen to Sixteen Months');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `cow`
--
ALTER TABLE `cow`
  ADD PRIMARY KEY (`Cow_Id`),
  ADD KEY `User_Id` (`User_Id`),
  ADD KEY `FK_Shed` (`Shed_Id`),
  ADD KEY `fk_category` (`Category_Id`),
  ADD KEY `Type` (`Type`);

--
-- Indexes for table `cow_category`
--
ALTER TABLE `cow_category`
  ADD PRIMARY KEY (`Category_Id`);

--
-- Indexes for table `cow_sale`
--
ALTER TABLE `cow_sale`
  ADD PRIMARY KEY (`CowSale_Id`),
  ADD KEY `Cow_Id` (`Cow_Id`),
  ADD KEY `cow_sale_ibfk_2` (`Category_Id`);

--
-- Indexes for table `customer`
--
ALTER TABLE `customer`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `expense`
--
ALTER TABLE `expense`
  ADD PRIMARY KEY (`expense_id`),
  ADD KEY `category_id` (`category_id`);

--
-- Indexes for table `milk_sale`
--
ALTER TABLE `milk_sale`
  ADD PRIMARY KEY (`MilkSale_Id`),
  ADD KEY `User_Id` (`User_Id`),
  ADD KEY `Cow_Id` (`Cow_Id`),
  ADD KEY `Category_Id` (`Category_Id`);

--
-- Indexes for table `sales_record`
--
ALTER TABLE `sales_record`
  ADD PRIMARY KEY (`record_id`);

--
-- Indexes for table `shed`
--
ALTER TABLE `shed`
  ADD PRIMARY KEY (`Shed_Id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`User_Id`);

--
-- Indexes for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD PRIMARY KEY (`Vaccine_Id`),
  ADD KEY `User_Id` (`User_Id`),
  ADD KEY `vaccine_ibfk_3` (`Shed_Id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `category`
--
ALTER TABLE `category`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `cow`
--
ALTER TABLE `cow`
  MODIFY `Cow_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=81;

--
-- AUTO_INCREMENT for table `cow_category`
--
ALTER TABLE `cow_category`
  MODIFY `Category_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `cow_sale`
--
ALTER TABLE `cow_sale`
  MODIFY `CowSale_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `customer`
--
ALTER TABLE `customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `expense`
--
ALTER TABLE `expense`
  MODIFY `expense_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `milk_sale`
--
ALTER TABLE `milk_sale`
  MODIFY `MilkSale_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `sales_record`
--
ALTER TABLE `sales_record`
  MODIFY `record_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=73;

--
-- AUTO_INCREMENT for table `shed`
--
ALTER TABLE `shed`
  MODIFY `Shed_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `User_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `vaccine`
--
ALTER TABLE `vaccine`
  MODIFY `Vaccine_Id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cow`
--
ALTER TABLE `cow`
  ADD CONSTRAINT `FK_Shed` FOREIGN KEY (`Shed_Id`) REFERENCES `shed` (`Shed_Id`),
  ADD CONSTRAINT `cow_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user` (`User_Id`),
  ADD CONSTRAINT `fk_category` FOREIGN KEY (`Category_Id`) REFERENCES `cow_category` (`Category_Id`);

--
-- Constraints for table `cow_sale`
--
ALTER TABLE `cow_sale`
  ADD CONSTRAINT `cow_sale_ibfk_1` FOREIGN KEY (`Cow_Id`) REFERENCES `cow` (`Cow_Id`),
  ADD CONSTRAINT `cow_sale_ibfk_2` FOREIGN KEY (`Category_Id`) REFERENCES `cow_category` (`Category_Id`);

--
-- Constraints for table `expense`
--
ALTER TABLE `expense`
  ADD CONSTRAINT `expense_ibfk_1` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`);

--
-- Constraints for table `milk_sale`
--
ALTER TABLE `milk_sale`
  ADD CONSTRAINT `milk_sale_ibfk_1` FOREIGN KEY (`User_Id`) REFERENCES `user` (`User_Id`),
  ADD CONSTRAINT `milk_sale_ibfk_2` FOREIGN KEY (`Cow_Id`) REFERENCES `cow` (`Cow_Id`),
  ADD CONSTRAINT `milk_sale_ibfk_3` FOREIGN KEY (`Category_Id`) REFERENCES `cow_category` (`Category_Id`);

--
-- Constraints for table `vaccine`
--
ALTER TABLE `vaccine`
  ADD CONSTRAINT `vaccine_ibfk_2` FOREIGN KEY (`User_Id`) REFERENCES `user` (`User_Id`),
  ADD CONSTRAINT `vaccine_ibfk_3` FOREIGN KEY (`Shed_Id`) REFERENCES `shed` (`Shed_Id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
