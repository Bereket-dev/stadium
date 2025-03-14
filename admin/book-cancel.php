<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

$booking_id = "";
$first_name = "";
$last_name = "";
$stadium_name = "";
$event_name = "";
$seat_type  = "";
$seat_number = "";
$seat_price = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $booking_id = $_GET["id"];
} else {
    echo "confirm page doesnt get id";
    exit();
}

$stmt = $conn->prepare("SELECT * FROM bookings WHERE  id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$book_status = $row["status"];
if ($book_status == 'pending') {
    $seat_idArray = json_decode($row["seat_id_data"], true);
    // Update seat status
    foreach ($seat_idArray as $seat_id) {
        $stmt = $conn->prepare("UPDATE seats SET seat_status = 'available' WHERE id = ?");
        $stmt->bind_param("i", $seat_id);
        $stmt->execute();
        $stmt->close();
    }

    $stmt = $conn->prepare("UPDATE bookings SET `status` = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
} else if ($book_status === 'cancelled') {
    $stmt = $conn->prepare("DELETE FROM bookings  WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
}
$_SESSION["message"] = '';
header("Location: ./book-management.php");
exit();
