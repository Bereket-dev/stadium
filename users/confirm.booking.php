<?php
include './includes/header.php';

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
$stmt->bind_param("s", $booking_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$first_name = $row["first_name"];
$last_name = $row["last_name"];
$seat_name  = $row["seat_type"];
$seat_price = $row["price"];
$booking_qr  =  $row["qr_code"];

$seat_id = $row["seat_id"];
$event_id = $row["event_id"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM seats WHERE  id = ?");
$stmt->bind_param("s", $seat_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$seat_number = $row["seat_number"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM events WHERE  id = ?");
$stmt->bind_param("s", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$event_name = $row["event_name"];

$stadium_id = $row["stadium_id"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM stadiums WHERE  id = ?");
$stmt->bind_param("s", $stadium_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stadium_name = $row["stadium_name"];
$stmt->close();
$conn->close();
?>

<div class="container">
    <div class="">
        FIRST NAME: <span><?php echo $first_name; ?></span>
    </div>
    <div class="">
        LAST NAME: <span><?php echo $last_name; ?></span>
    </div>
    <div class="">
        STADIUM: <span><?php echo $stadium_name; ?></span>
    </div>
    <div class="">
        EVENT: <span><?php echo $event_name; ?></span>
    </div>
    <div class="">
        SEAT TYPE: <span><?php echo $seat_name; ?></span>
    </div>
    <div class="">
        SEAT NUMBER: <span><?php echo $seat_number; ?></span>
    </div>
    <div class="">
        PRCIE: <span><?php echo $seat_price . " ETB"; ?></span>
    </div>
    <div class="">
        <?php
        // Assuming $qrCodeURL is fetched from the database
        echo '<img src="' . $booking_qr . '" alt="QR Code">';
        ?>

    </div>
</div>

<?php 

