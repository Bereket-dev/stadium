<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.user.php';

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET["id"]) || !filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
        header("Location: ../users/users.event.calendar.php");
        exit();
    } else {
        $_SESSION["event_id"] = $_GET['id'];
    }

    if (!isset($_GET["seattype_id"]) || empty($_GET["seattype_id"])) {
        header("Location: ../users/users.event.calendar.php");
        exit();
    } else {
        $_SESSION["seattype_id"] = $_GET["seattype_id"];
    }

    $stmt = $conn->prepare("SELECT * FROM seattype WHERE id = ?");
    $stmt->bind_param("i", $_SESSION["seattype_id"]);
    $stmt->execute();
    $row = $stmt->get_result()->fetch_assoc();

    $_SESSION["seat_price"] = $row['seat_price'];
    $_SESSION["seat_name"] = $row['seat_name'];
    $stmt->close();
}

$event_id = $_SESSION["event_id"] ?? null;
$seat_price = $_SESSION["seat_price"] ?? null;
$seat_name = $_SESSION["seat_name"] ?? null;
$seattype_id = $_SESSION["seattype_id"] ?? null;

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stmt->close();

if (!$event) {
    exit("Event not found!");
}

// When form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email_address = trim($_POST["email_address"] ?? "");

    if (empty($first_name) || empty($last_name) || empty($email_address)) {
        echo "All fields are required";
        goto form;
    }

    $username = $_SESSION["username"] ?? null;
    if (!$username) {
        exit("User not logged in.");
    }

    $event_id = $_SESSION["event_id"] ?? null;
    $seat_price = $_SESSION["seat_price"] ?? null;
    $seat_name = $_SESSION["seat_name"] ?? null;
    $seattype_id = $_SESSION["seattype_id"] ?? null;

    // Fetch user ID
    $stmt = $conn->prepare("SELECT id FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $user_id = $stmt->get_result()->fetch_assoc()["id"] ?? null;
    $stmt->close();

    if (!$user_id) {
        exit("User not found.");
    }

    // Fetch available seat
    $stmt = $conn->prepare("SELECT id, stadium_id FROM seats WHERE seattype_id = ? AND seat_status = 'available' AND event_id = ? LIMIT 1");
    $stmt->bind_param("ii", $seattype_id, $event_id);
    $stmt->execute();
    $seat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$seat) {
        echo "<div class='text-center'>No available seats.</div>";
        goto form;
    }

    $seat_id = $seat["id"];
    $stadium_id = $seat["stadium_id"];

    // Fetch stadium name
    $stmt = $conn->prepare("SELECT stadium_name FROM stadiums WHERE id = ?");
    $stmt->bind_param("i", $stadium_id);
    $stmt->execute();
    $stadium_name = $stmt->get_result()->fetch_assoc()["stadium_name"] ?? "Unknown";
    $stmt->close();

    // Fetch event name
    $stmt = $conn->prepare("SELECT event_name FROM events WHERE id = ?");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $event_name = $stmt->get_result()->fetch_assoc()["event_name"] ?? "Unknown";
    $stmt->close();

    // Insert booking record
    $stmt = $conn->prepare("INSERT INTO bookings (first_name, last_name, email_address, user_id, event_id, seat_id, seat_type, price) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiisi", $first_name, $last_name, $email_address, $user_id, $event_id, $seat_id, $seat_name, $seat_price);
    $stmt->execute();
    $booking_id = $stmt->insert_id;
    $stmt->close();

    // Generate QR Code with actual booking details
    $qrData = "Booking ID: $booking_id\nStadium: $stadium_name\nEvent: $event_name\nSeat: $seat_name\nPrice: $seat_price";
    $qrCodeURL = "https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=" . urlencode($qrData);

    // Ensure QR Code is stored in the database
    $stmt = $conn->prepare("UPDATE bookings SET qr_code = ? WHERE id = ?");
    $stmt->bind_param("si", $qrCodeURL, $booking_id);
    $stmt->execute();
    $stmt->close();

    // Update seat status
    $stmt = $conn->prepare("UPDATE seats SET seat_status = 'selected' WHERE id = ?");
    $stmt->bind_param("i", $seat_id);
    $stmt->execute();
    $stmt->close();



    // Redirect to confirmation
    header("Location: ./confirm.booking.php?id=" . urlencode($booking_id));
    unset($_POST["email_address"]);
    unset($_POST["first_name"]);
    unset($_POST["last_name"]);

    $_SESSION["issent"] = false;
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
</head>

<body>
    <!-- Include header with database connection -->
    <?php include './includes/header.php'; ?>

    <div class="container col-6 mt-5 p-5" style="box-shadow: 1px 1px 3px black;">
        <div class="text-center fs-3 mb-3"><?php echo $event["event_name"]; ?></div>
        <form class="row g-3" method="post" action="">
            <div class="col-md-6">
                <label for="validationCustom01" class="form-label">First name</label>
                <input type="text" class="form-control" name="first_name" id="" required placeholder="MARK">
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>
            <div class="col-md-6">
                <label for="validationCustom02" class="form-label">Last name</label>
                <input type="text" class="form-control" name="last_name" id="" required placeholder="Otto">
                <div class="valid-feedback">
                    Looks good!
                </div>
            </div>

            <div class="mb-3">
                <label for="exampleFormControlInput1" class="form-label">Email address</label>
                <input type="email" class="form-control" name="email_address" id="exampleFormControlInput1" placeholder="name@example.com">
            </div>

            <div class="mb-3">
                PRCIE: <span><?php echo $seat_price . " ETB"; ?></span>
            </div>


            <div class="mb-3" row>
                SEAT TYPE: <span><?php echo $seat_name; ?></span>
            </div>

            <div class="col-12">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck" required>
                    <label class="form-check-label" for="invalidCheck">
                        Agree to terms and conditions
                    </label>
                    <div class="invalid-feedback">
                        You must agree before submitting.
                    </div>
                </div>
            </div>
            <input type="hidden" name="id" value="<?php echo $booking_id; ?>">
            <div class="col-12">
                <button type="submit" class="mt-2 btn btn-primary">Book</button>
            </div>
        </form>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>