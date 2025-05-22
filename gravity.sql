-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 21, 2025 at 09:17 AM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `gravity`
--

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `status` enum('Pending','Processing','Shipped','Delivered','Cancelled','Confirmed') NOT NULL DEFAULT 'Pending',
  `order_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`id`, `user_id`, `product_id`, `quantity`, `total_price`, `status`, `order_date`) VALUES
(1, 3, 1, 0, 200.00, 'Pending', '2025-03-31 07:35:22'),
(2, 3, 1, 0, 200.00, 'Pending', '2025-04-07 14:25:52'),
(3, 3, 1, 0, 200.00, 'Pending', '2025-04-23 03:19:57'),
(4, 3, 1, 0, 200.00, 'Pending', '2025-04-23 12:14:42'),
(5, 3, 1, 2, 400.00, 'Confirmed', '2025-04-23 12:24:24'),
(6, 3, 2, 1, 300.00, 'Confirmed', '2025-05-10 06:11:38'),
(7, 6, 1, 1, 200.00, 'Pending', '2025-05-17 04:19:06'),
(8, 6, 2, 1, 300.00, 'Pending', '2025-05-17 04:19:11'),
(9, 6, 4, 1, 150.00, 'Pending', '2025-05-17 14:40:28'),
(10, 9, 6, 1, 250.00, 'Pending', '2025-05-21 06:52:31');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(255) NOT NULL,
  `supplier_id` int(11) DEFAULT NULL,
  `is_approved` tinyint(1) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `name`, `price`, `image`, `supplier_id`, `is_approved`) VALUES
(1, 'Zero-Gravity Shoes', 200.00, 'shoes.jpg\r\n', NULL, 1),
(2, 'Pants', 300.00, 'pants.jpg', 4, 1),
(3, 'Over Size Woman T-shirt', 200.00, 'Over Size Woman T-shirt.jpg', 8, 1),
(4, 'Cap Tivity', 150.00, 'cap.jpg', 8, 1),
(5, 'Gravity SwayLeather', 350.00, 'jacket.jpg', 8, 1),
(6, 'jogging pants', 250.00, 'jogging pants.jpg', 8, 1);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fullname` varchar(255) NOT NULL,
  `username` varchar(100) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `user_type` enum('customer','supplier','admin') NOT NULL DEFAULT 'customer',
  `address` varchar(100) NOT NULL,
  `balance` decimal(11,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fullname`, `username`, `email`, `phone`, `password`, `user_type`, `address`, `balance`) VALUES
(1, 'Arjay Oblino Ocfemia', 'Arjay30', 'ocfemiaarjay30@gmail.com', '09533303725', 'Arjay123', 'customer', '', 0.00),
(2, 'Arjay Ocfemia', 'Arjay123', 'ocfemiacute30@gmail.com', '09494744141', 'password', 'customer', '', 0.00),
(3, 'Justine Ocfemia', 'justine123', 'justine@gmail.com', '09544456511', '$2y$10$jaQrhi3N6oO5u9KrsL2pUuOd1ccLDEkV3dQ4jEufLK0RAo.OuswUu', 'customer', 'Pioduran', 0.00),
(4, 'Arjay Ocfemia', 'Arjay', 'ocfemiaarjayy30@gmail.com', '09533303725', '$2y$10$JOijgQaa9eSy7tcpvT7sf.PyhEU77mq1/sgTb2szmnXJ6otHmhqE.', 'supplier', '', 0.00),
(5, 'Arjay', 'Cute', 'Cuteoverload@gmail.com', '0952303725', '$2y$10$.FztAe9bGXptfC8a/hzPseTxf79ubt5KD1wKW/gri0sFlWNlxg2Vy', 'admin', '', 35.00),
(6, 'Grace Bicaldo', 'gracebicaldo', 'grace@gmail.com', '09123456789', '$2y$10$qs.GWBJO78o2cntHVlg0tugpc4vT449EX8VXalYWIfiT3inbpFUA6', 'customer', '', 0.00),
(8, 'john Ray Alcantara', 'john_ray', 'alcantara@gmail.com', '09123456789', '$2y$10$VvqTZEDKOpyvpXXdtmZBke9cTHPof/kOUKRJgMsNC7M8zaFYySwX6', 'supplier', '', 0.00),
(9, 'John Ray Alcantara', 'user_johnray', 'johnray123@gmail.com', '09123456789', '$2y$10$YnS/HeV89efATDGfThEZFuaplOYJrGg92e5Lua7Zc7KQyKdyZxu3i', 'customer', '', 0.00);

-- --------------------------------------------------------

--
-- Table structure for table `withdrawals`
--

CREATE TABLE `withdrawals` (
  `id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(100) NOT NULL,
  `payment_details` varchar(255) DEFAULT NULL,
  `status` enum('Pending','Approved','Rejected') DEFAULT 'Pending',
  `requested_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `processed_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `withdrawals`
--

INSERT INTO `withdrawals` (`id`, `supplier_id`, `amount`, `payment_method`, `payment_details`, `status`, `requested_at`, `processed_at`) VALUES
(1, 4, 200.00, 'GCash', NULL, 'Approved', '2025-05-13 15:05:48', '2025-05-13 15:06:37'),
(2, 4, 80.00, 'GCash', '09533303725', 'Approved', '2025-05-16 13:23:39', '2025-05-16 13:30:07'),
(3, 4, 5.00, 'GCash', '09533303725', 'Approved', '2025-05-16 13:30:59', '2025-05-16 13:31:21');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD KEY `supplier_id` (`supplier_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `withdrawals`
--
ALTER TABLE `withdrawals`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `withdrawals`
--
ALTER TABLE `withdrawals`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`);

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_ibfk_1` FOREIGN KEY (`supplier_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
