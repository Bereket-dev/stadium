<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.user.php';

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

$fullName = $first_name . " " . $last_name;
//for confirmation maile
$email_address = $row["email_address"];

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
$event_date = $row["event_date"];

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
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium|booking confirmation</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
</head>

<body>
    <?php include './includes/header.php'; ?>
    <div class="container">
        <div class="">
            FULL NAME: <span><?php echo $fullName; ?></span>
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
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>


<?php
//sessions for confirmation  email
$issent = $_SESSION["issent"];

if (!$issent) {
    $_SESSION["full_name"] = $fullName;
    $_SESSION["event_name"] = $event_name;
    $_SESSION["event_date"] = $event_date;
    $_SESSION["seat_type"] = $seat_name;
    $_SESSION["seat_number"] = $seat_number;
    $_SESSION["booking_qr"] = $booking_qr;
    $_SESSION["email_address"] = $email_address;

    include './email_to_user/email.script.php';
} else {
    echo 'Has been sent!';
}
