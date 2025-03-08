<?php
include './includes/header.php'; // Include header with database connection

if (isset($_GET["email_address"])) {
    $_SERVER["REQUEST_METHOD"] = "POST"; //IMPROPER USAGE DUE TO UNABLE OF POST METHOD FUNCTION
}

if ($_SERVER["REQUEST_METHOD"] == "GET") {

    if (!isset($_GET["id"]) || !filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
        exit("Missing or invalid event ID.");
    }
    if (!isset($_GET["seat_price"]) || empty($_GET["seat_price"])) {
        exit("Missing seat price.");
    }
    if (!isset($_GET["seat_name"]) || empty($_GET["seat_name"])) {
        exit("Missing seat name.");
    }

    $_SESSION["event_id"] = $_GET['id'];
    $_SESSION["seat_price"] = $_GET['seat_price'];
    $_SESSION["seat_name"] = $_GET['seat_name'];
}

$event_id = $_SESSION["event_id"] ?? null;
$seat_price = $_SESSION["seat_price"] ?? null;
$seat_name = $_SESSION["seat_name"] ?? null;

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

    $first_name = trim($_GET["first_name"] ?? "");
    $last_name = trim($_GET["last_name"] ?? "");
    $email_address = trim($_GET["email_address"] ?? "");

    if (empty($first_name) || empty($last_name) || empty($email_address)) {
        exit("All fields are required.");
    }

    $username = $_SESSION["username"] ?? null;
    if (!$username) {
        exit("User not logged in.");
    }

    $event_id = $_SESSION["event_id"] ?? null;
    $seat_price = $_SESSION["seat_price"] ?? null;
    $seat_name = $_SESSION["seat_name"] ?? null;

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
    $stmt = $conn->prepare("SELECT id, stadium_id FROM seats WHERE seat_type = ? AND seat_status = 'available' AND event_id = ? LIMIT 1");
    $stmt->bind_param("si", $seat_name, $event_id);
    $stmt->execute();
    $seat = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$seat) {
        exit("No available seats.");
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
    $_SESSION["event_id"] = "";
    $_SESSION["seat_name"] = "";
    $_SESSION["seat_price"] = "";
    unset($_GET["email_address"]);
    exit();
}
?>
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