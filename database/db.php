<?php
$conn = new mysqli("localhost", "root", "", "");

if ($conn->connect_error) {
    die("connection failed: " . $conn->connect_error);
}





