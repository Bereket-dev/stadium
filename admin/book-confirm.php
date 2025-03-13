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

$first_name = $row["first_name"];
$last_name = $row["last_name"];
$seat_name  = $row["seat_type"];
$seat_price = $row["price"];
$booking_qr  =  $row["qr_code"];
$book_status = $row["status"];
$seat_number = $row["seat_number"];
$event_id = $row["event_id"];


$seat_idArray = json_decode($row["seat_id_data"], true);
$fullName = $first_name . " " . $last_name;
//for confirmation maile
$email_address = $row["email_address"];
$stmt->close();

if ($book_status == 'pending') {
    $stmt = $conn->prepare("UPDATE bookings SET `status` = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();
    $issent = false;
} else if ($book_status === 'confirm') {
    $issent = true;
}

// Update seat status
foreach ($seat_idArray as $seat_id) {
    $stmt = $conn->prepare("UPDATE seats SET seat_status = 'booked' WHERE id = ?");
    $stmt->bind_param("i", $seat_id);
    $stmt->execute();
    $stmt->close();
}

$stmt = $conn->prepare("SELECT * FROM events WHERE  id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$event_name = $row["event_name"];
$event_date = $row["event_date"];

$stadium_id = $row["stadium_id"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM stadiums WHERE  id = ?");
$stmt->bind_param("i", $stadium_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stadium_name = $row["stadium_name"];
$stmt->close();
$conn->close();
if ($book_status == "confirmed") {
    //prevent double email sending
    if (!$issent) {
        $_SESSION["full_name"] = $fullName;
        $_SESSION["event_name"] = $event_name;
        $_SESSION["event_date"] = $event_date;
        $_SESSION["seat_type"] = $seat_name;
        $_SESSION["seat_number"] = $seat_number;
        $_SESSION["booking_qr"] = $booking_qr;
        $_SESSION["email_address"] = $email_address;
        $_SESSION["stadium_name"] = $stadium_name;
        ob_start();
        include './email_to_user/email.script.php';
       
        ob_end_flush();
    } else {
        echo 'Has been sent!';
    }
}
