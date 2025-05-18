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

$stmt = $conn->prepare("SELECT * FROM booking WHERE  id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$user_id = $row["user_id"];
$seattype_id = $row["seattype_id"];
$seat_number = $row["seat_number"];
$booking_qr  =  $row["qr_code"];
$status = $row["status"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM seattype WHERE  id = ?");
$stmt->bind_param("i", $seattype_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$seat_name = $row["seat_name"];
$seat_price = $row["seat_price"];

$event_id = $row["event_id"];
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM `event` WHERE  id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();
$event_name = $row["event_name"];
$event_date = $row["event_date"];
$stadium_id = $row["stadium_id"];

$stmt->close();

$stmt = $conn->prepare("SELECT * FROM stadium WHERE  id = ?");
$stmt->bind_param("i", $stadium_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$stadium_name = $row["stadium_name"];
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM user WHERE  id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();
$row = $result->fetch_assoc();

$first_name = $row["first_name"];
$last_name = $row["last_name"];
$email = $row["email"];

$fullName = $first_name . " " . $last_name;
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
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <div class="container">
        <div class=" card confirm-card mx-auto mt-5 p-3" style="height: 700px; width: 450px">
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                FULL NAME: <span><?php echo $fullName; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                STADIUM: <span><?php echo $stadium_name; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                EVENT: <span><?php echo $event_name; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                STATUS: <span><?php echo $status; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                SEAT TYPE: <span><?php echo $seat_name; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                SEAT NUMBER: <span><?php echo $seat_number; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-3">
                PRCIE: <span><?php echo $seat_price . " ETB"; ?></span>
            </div>
            <div class="d-flex justify-content-between border-bottom border-dark mb-4">
                Event Date: <span><?php echo $event_date . " ETB"; ?></span>
            </div>
            <div class="text-center">
                <?php
                // Assuming $qrCodeURL is fetched from the database
                echo '<img src="' . $booking_qr . '" alt="QR Code">';
                ?>

            </div>
        </div>
        <div class="text-start mx-5"><a href="./users.event.calendar.php" class="btn btn-primary"><- back</a></div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>


<?php
