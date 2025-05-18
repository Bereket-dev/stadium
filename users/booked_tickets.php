<?php
session_start();
include '../database/db.php';
include './includes/auth.user.php';

$user_id = $_SESSION["user_id"] ?? null;
if (!$user_id) {
    header("Location: ../auth.login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Stadium|Tickets</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">

</head>

<body>

    <?php include '../users/includes/header.php'; ?>

    <div class="container frames">
        <?php
        $stmt_booking = $conn->prepare("SELECT * FROM booking WHERE user_id = ?");
        $stmt_booking->bind_param("i", $user_id);
        $stmt_booking->execute();
        $result_booking = $stmt_booking->get_result();

        if ($result_booking->num_rows == 0) {
            echo '<div class="fs-1">No Tickets</div>';
        } else {
            while ($booking = $result_booking->fetch_assoc()) {
                $seattype_id = $booking["seattype_id"];

                $stmt_seat = $conn->prepare("SELECT * FROM seattype WHERE id = ?");
                $stmt_seat->bind_param("i", $seattype_id);
                $stmt_seat->execute();
                $result_seat = $stmt_seat->get_result();
                $seat = $result_seat->fetch_assoc();

                $seat_name = $seat["seat_name"];
                $seat_price = $seat["seat_price"];
                $event_id = $seat["event_id"];
                $stmt_seat->close();

                $stmt_event = $conn->prepare("SELECT * FROM `event` WHERE id = ?");
                $stmt_event->bind_param("i", $event_id);
                $stmt_event->execute();
                $result_event = $stmt_event->get_result();
                $event = $result_event->fetch_assoc();
                $stmt_event->close();

                $event_name = $event["event_name"];
                $event_date = $event["event_date"];

                echo '<div class="card mb-3" style="width: 100%; height: min-content;">';
                echo '<h5 class="card-header">' . htmlspecialchars($event_name) . '</h5>';
                echo '<div class="card-body">';
                echo '<h5 class="card-title">Event Date: ' . htmlspecialchars($event_date) . '</h5>';
                echo '<p class="card-text">Seat: ' . htmlspecialchars($seat_name) . ' - ' . htmlspecialchars($seat_price) . ' ETB</p>';
                echo '<a href="confirm.booking.php?id=' . $booking["id"]  . '" class="btn btn-primary">View Ticket</a>';
                echo '</div></div>';
            }
        }
        ?>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="./assets/js/main.js"></script>
</body>

</html>