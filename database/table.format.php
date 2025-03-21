<?php
include 'db.php';

// //users table
// $sql = "CREATE TABLE `users` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `username` varchar(100) NOT NULL,
//   `email` varchar(100) NOT NULL,
//   `password_hash` varchar(100) NOT NULL,
//   `roles` varchar(10) NOT NULL DEFAULT 'user',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


// //stadiums table
// $sql = "CREATE TABLE `stadiums` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `stadium_name` varchar(255) NOT NULL,
//   `stadium_address` varchar(255) NOT NULL,
//   `city` varchar(255) NOT NULL,
//   `region` varchar(255) NOT NULL,
//   `postal_code` varchar(255) NOT NULL,
//   `total_seats` int(11) NOT NULL,
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


// //events table
// $sql  = "CREATE TABLE `events` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `event_name` varchar(255) NOT NULL,
//   `event_date` datetime NOT NULL,
//   `stadium_id` int(11) NOT NULL,
//   `event_description` text NOT NULL,
//   `layout_image` longblob NOT NULL,
//   PRIMARY KEY (`id`),
//   KEY `stadium_id` (`stadium_id`),
//   CONSTRAINT `fk_stadium` FOREIGN KEY (`stadium_id`) REFERENCES `stadiums` (`id`) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";

// $result = $conn->query($sql);


// //seattype table
// $sql = "CREATE TABLE `seattype` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `seat_name` varchar(255) NOT NULL,
//   `stadium_id` int(11) NOT NULL,
//   `event_id` int(11) NOT NULL,
//   `seat_amount` int(11) NOT NULL,
//   `seat_price` float NOT NULL,
//   PRIMARY KEY (`id`),
//   KEY `stadium_id` (`stadium_id`),
//   KEY `event_id` (`event_id`),
//   CONSTRAINT `seattype_ibfk_1` FOREIGN KEY (`stadium_id`) REFERENCES `stadiums` (`id`) ON DELETE CASCADE,
//   CONSTRAINT `seattype_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


// //seats table
// $sql = "CREATE TABLE `seats` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `stadium_id` int(11) NOT NULL,
//   `seattype_id` int(11) NOT NULL,
//   `seat_status` enum('available','booked','selected') NOT NULL DEFAULT 'available',
//   `event_id` int(11) NOT NULL,
//   `seat_number` int(11) NOT NULL,
//   PRIMARY KEY (`id`),
//   KEY `stadium_id` (`stadium_id`),
//   KEY `event_id` (`event_id`),
//   KEY `seattype_id` (`seattype_id`),
//   CONSTRAINT `seat_ibfk_1` FOREIGN KEY (`stadium_id`) REFERENCES `stadiums` (`id`) ON DELETE CASCADE,
//   CONSTRAINT `seat_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE,
//   CONSTRAINT `seat_ibfk_3` FOREIGN KEY (`seattype_id`) REFERENCES `seattype` (`id`) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


// //booking table
// $sql = "CREATE TABLE `bookings` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `first_name` varchar(255) NOT NULL,
//   `last_name` varchar(255) NOT NULL,
//   `email_address` varchar(255) NOT NULL,
//   `user_id` int(11) NOT NULL,
//   `event_id` int(11) NOT NULL,
//   `seat_number` varchar(255) NOT NULL,
//   `seat_id_data` text(255) NOT NULL,
//   `booking_date` datetime DEFAULT current_timestamp(),
//   `qr_code` varchar(255) DEFAULT NULL,
//   `seat_type` varchar(255) NOT NULL,
//   `quantity` int(11) NOT NULL,
//   `price` int(11) NOT NULL,
//   `status` enum('pending', 'confirmed','cancelled') NOT NULL DEFAULT 'pending',
//   `transactionRef` varchar(255) NOT NULL,
//   PRIMARY KEY (`id`),
//   KEY `user_id` (`user_id`),
//   KEY `event_id` (`event_id`),
//   CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
//   CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `events` (`id`) ON DELETE CASCADE
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


// //product table
// $sql = "CREATE TABLE `products` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `product_name` varchar(255) NOT NULL,
//   `product_price` int(11) NOT NULL,
//   `product_amount` int(11) NOT NULL,  
//   `product_image` longblob NOT NULL,
//    PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
// ";
// $result = $conn->query($sql);


//orders table
$sql = "CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `status` enum('pending', 'processing','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `quantity` int(11) NOT NULL, 
  `total_price` int(11) NOT NULL, 
  `seat_number` varchar(255) NOT NULL, 
  PRIMARY KEY (`id`),
  KEY `user_id`(`user_id`),
  KEY `product_id`(`product_id`),
  CONSTRAINT `product_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ,
  CONSTRAINT `product_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
";
$result = $conn->query($sql);
