<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.user.php';
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET["id"]) || !filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
        header("Location: ../users/users.event.calendar.php");
        exit();
    } else {
        $_SESSION["event_id"] = $_GET['id'];
    }
}

$event_id = $_SESSION["event_id"] ?? null;



// Fetch event details
$stmt = $conn->prepare("SELECT * FROM `event` WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$event_name = $event["event_name"] ?? null;
$event_image = $event['layout_image'];
$stmt->close();

if (!$event) {
    exit("Event not found!");
}

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $seattype_id = $_POST["seattype_id"] ?? null;
    $transactionRef = $_POST["transactionRef"] ?? null;
    if (empty($transactionRef) || !$seattype_id) {
        $_SESSION["message"] = "All fields are required";
        exit(1);
    }

    $user_id = $_SESSION["user_id"] ?? null;

    if (!$user_id) {
        exit("User not found.");
    }

    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'available' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $available_number = $seat["number"];
    $available_number--; //decrease since it will add to selected
    if (!isset($_SESSION["isupdated"])) {
        // Update seat number on specific status
        $stmt = $conn->prepare("UPDATE `seat` SET `number` = ?  WHERE id = ? AND seat_status = 'available'");
        $stmt->bind_param("ii", $available_number, $seat_id);
        if ($stmt->execute()) {
            $_SESSION["isupdated"] = true;
        }
        $stmt->close();
    }

    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'selected' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $selected_number = $seat["number"];
    $selected_number++;

    if (!isset($_SESSION["isupdated"])) {
        $stmt = $conn->prepare("UPDATE `seat` SET `number` = ?  WHERE id = ? AND seat_status = 'selected'");
        $stmt->bind_param("ii", $selected_number, $seat_id);
        if ($stmt->execute()) {
            $_SESSION["isupdated"] = true;
        }
        $stmt->close();
    }
    $seat_number = $selected_number;
    // Insert booking record
    $stmt = $conn->prepare("INSERT INTO booking (user_id, seat_number, seattype_id, transactionRef) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiis", $user_id, $seat_number, $seattype_id, $transactionRef);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Generate QR Code with actual booking details
    $qrData = "Booking ID: $booking_id";
    $qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);

    // Ensure QR Code is stored in the database
    $stmt = $conn->prepare("UPDATE booking SET qr_code = ? WHERE id = ?");
    $stmt->bind_param("si", $qrCodeURL, $booking_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("SELECT * FROM booking WHERE  id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();


    // Redirect to confirmation
    header("Location: ./confirm.booking.php?id=" . urlencode($booking_id));
    unset($_POST["email_address"]);
    unset($_POST["first_name"]);
    unset($_POST["last_name"]);
    unset($_SESSION["isupdated"]);
    exit();
}
form:
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium|booking</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <?php include './includes/header.php'; ?>
    <div class="event-banner" style="background-image: url('../assets/Images/uploaded/<?php echo $event_image ?>');">
        <div class="container">
            <div class="calling-text" style="color: white;width: 50%;">
                <h1><?php echo $event_name; ?></h1>
            </div>
            <a href="./users/users.event.calendar.php" class="btn btn-primary">Book An Event</a>
        </div>
    </div>
    <form class="g-3" method="post" action="">
        <?php echo '<p class ="text-center">' . $message . '</p>'; ?>
        <!--ticket selecting area-->
        <div class="container frames col-6">
            <div class="row border border-primary text-center p-3">
                <div class="col-4">Seat Type</div>
                <div class="col-4">Price</div>
                <div class="col-4">Status</div>
            </div>

            <?php
            $stmt = $conn->prepare('SELECT * FROM seattype WHERE event_id = ?');
            $stmt->bind_param("i", $event_id);
            $stmt->execute();
            $results = $stmt->get_result();


            while ($row = $results->fetch_assoc()) {
                $stmt = $conn->prepare('SELECT * FROM `seat` WHERE seattype_id = ?');
                $stmt->bind_param("i", $row["id"]);
                $stmt->execute();
                $result = $stmt->get_result();
                $status = ($result->fetch_assoc()["number"] > 0) ? "<div class='col-4 text-green'>Available</div>" : "<div class='col-4 text-red' disabled>Not Available</div>";

                echo '<div class="row border-bottom border-primary p-3 text-center  align-items-center">';

                // Column for checkbox
                echo '<div class="col-auto">';
                echo '<input class="form-check-input" required type="radio" name="seattype_id" value="' . $row["id"] . '" id="checkDefault">';
                echo '</div>';

                // Column for label (which wraps the rest)
                echo '<div class="col px-0 d-flex align-items-center">';

                // Seat name
                echo '<div class="col-3">' . $row["seat_name"] . '</div>';

                // seat price
                echo '<div class="col-5">' . $row["seat_price"] . ' ETB</div>';

                // Seat status
                echo $status;

                echo '</div>'; // close main col
                echo '</div>'; // close row
            };
            $stmt->close();
            ?>

        </div>
        <!-- booking form -->
        <div class="container col-6 mt-5 p-5 banner-container" style="box-shadow: 1px 1px 3px black;">
            <div class="text-center fs-3 mb-3"><?php echo $event["event_name"]; ?></div>

            <div class="mb-3">
                <label for="exampleFormControlInput2" class="form-label">Transaction Ref:</label>
                <input type="text" class="form-control" name="transactionRef" id="exampleFormControlInput2" placeholder="" required>
            </div>

            <div class="col-12">
                <button type="submit" class="mt-2 btn btn-primary">Book</button>
            </div>
        </div>
    </form>
    <!-- footer -->
    <?php include '../includes/footer.php'; ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>