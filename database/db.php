<?php
$conn = new mysqli("localhost", "root", "", "student_data");

if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}

// $sql = "CREATE TABLE seatType (
// id INT PRIMARY KEY AUTO_INCREMENT,
// seat_name VARCHAR(255) NOT NULL,
// stadium_id INT NOT NULL,
// event_id INT NOT NULL,
// price FLOAT NOT NULL,
// FOREIGN KEY(stadium_id) REFERENCES stadiums(id) ON DELETE CASCADE,
// FOREIGN KEY(event_id) REFERENCES events(id) ON DELETE CASCADE

// )";

// $conn->query($sql);