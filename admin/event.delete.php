<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

$event_id = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $event_id = $_GET['id'];
} else {
    header("Location: event-management.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "Event not found!";
    exit();
}
$stmt->close();

$stmt = $conn->prepare("DELETE FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$conn->close();
header("Location: event-management.php");
exit();
