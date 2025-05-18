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

$stmt = $conn->prepare("SELECT * FROM booking WHERE  id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$book_status = $row["status"];
$seattype_id = $row["seattype_id"];
if ($book_status == 'pending' && !isset($_SESSION["issupdated"])) {
    // Update seat status
    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'selected' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $selected_number = $seat["number"];
    $selected_number--;

    $stmt = $conn->prepare("UPDATE seat SET `number` = ? WHERE id = ?");
    $stmt->bind_param("ii", $selected_number, $seat_id);
    if ($stmt->execute()) {
        $_SESSION["isupdate"] = true;
    }
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'available' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $available_number = $seat["number"];
    $available_number++;

    $stmt = $conn->prepare("UPDATE seat SET `number` = ? WHERE id = ?");
    $stmt->bind_param("ii", $available_number, $seat_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("UPDATE booking SET `status` = 'cancelled' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
} else if ($book_status === 'cancelled') {
    $stmt = $conn->prepare("DELETE FROM booking  WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
}
$_SESSION["message"] = '';
header("Location: ./book-management.php");
exit();
