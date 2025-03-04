<?php
$conn = new mysqli("localhost", "root", "", "student_data");

if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}