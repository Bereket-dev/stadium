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
// $seat_name = $_SESSION["seat_name"] ?? null;
// $seattype_id = $_SESSION["seattype_id"] ?? null;

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
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

    $first_name = trim($_POST["first_name"] ?? "");
    $last_name = trim($_POST["last_name"] ?? "");
    $email_address = trim($_POST["email_address"] ?? "");
    $transactionRef = trim($_POST["transactionRef"] ?? "");
    $quantity = $_POST["quantity"] ?? null;
    if (!$quantity) {
        $message = "no quantity post";
    }
    $price = $_POST["price"] ?? null;
    if (!$price) {
        $message = 'no price post';
        goto form;
    }
    if (empty($first_name) || empty($last_name) || empty($email_address) || empty($transactionRef)) {
        $message = "All fields are required";
        goto form;
    }

    $username = $_SESSION["username"] ?? null;
    if (!$username) {
        exit("User not logged in.");
    }

    $event_id = $_SESSION["event_id"] ?? null;
    $seat_price = $_SESSION["seat_price"] ?? null;
    $seat_price *= $quantity;
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
    $stmt = $conn->prepare("SELECT * FROM seats WHERE seattype_id = ? AND seat_status = 'available' AND event_id = ? LIMIT ?");
    $stmt->bind_param("iii", $seattype_id, $event_id, $quantity);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) {
        $message = "<div class='text-center'>No available seats.</div>";
        goto form;
    } else if ($result->num_rows < $quantity) {
        $message = 'only ' . $result->num_rows . ' ' . $seat_name . ' seats are available!';
        goto form;
    } //initiallize
    $seat_numArray = [];
    $seat_idArray = [];
    $stadium_ids = [];
    while ($seat = $result->fetch_assoc()) {
        $seat_numArray[] = $seat["seat_number"];
        $seat_idArray[] = $seat["id"];
        $stadium_ids[] = $seat["stadium_id"];
    }

    $seat_numbers = implode(", ", $seat_numArray); // changing array in to string

    $stadium_id = $stadium_ids[0]; //just using only one id since all are similar
    $stmt->close();


    // Update seat status
    foreach ($seat_idArray as $seat_id) {
        $stmt = $conn->prepare("UPDATE seats SET seat_status = 'selected' WHERE id = ?");
        $stmt->bind_param("i", $seat_id);
        $stmt->execute();
        $stmt->close();
    }
    $serilizedData = json_encode($seat_idArray); //serilizing to store it in booking table

    // Fetch stadium name
    $stmt = $conn->prepare("SELECT stadium_name FROM stadiums WHERE id = ?");
    $stmt->bind_param("i", $stadium_id);
    $stmt->execute();
    $stadium_name = $stmt->get_result()->fetch_assoc()["stadium_name"] ?? "Unknown";
    $stmt->close();


    if ($seat_price != $price) {
        exit("Don't try to cheat!");
    }

    // Insert booking record
    $stmt = $conn->prepare("INSERT INTO bookings (first_name, last_name, email_address, user_id, event_id, seat_number, seat_id_data, seat_type, quantity, price, transactionRef) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiisssiis", $first_name, $last_name, $email_address, $user_id, $event_id, $seat_numbers, $serilizedData, $seat_name, $quantity, $seat_price, $transactionRef);
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


    $stmt = $conn->prepare("SELECT * FROM bookings WHERE  id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();


    // Redirect to confirmation
    header("Location: ./confirm.booking.php?id=" . urlencode($booking_id));
    unset($_POST["email_address"]);
    unset($_POST["first_name"]);
    unset($_POST["last_name"]);


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
    <?php echo '<p class ="text-center">' . $message . '</p>'; ?>
    <!--ticket selecting area-->
    <div class="container frames col-6">
        <div class="row border border-primary p-3">
            <div class="col-3">Seat Type</div>
            <div class="col-3">Price</div>
            <div class="col-3">Quantity</div>
        </div>

        <?php
        $stmt = $conn->prepare('SELECT * FROM seattype WHERE event_id = ?');
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $results = $stmt->get_result();
        while ($row = $results->fetch_assoc()) {
            echo '<div class="row border-bottom border-primary p-3 input-area">';
            echo '<div class="col-3 stattype-area">' . $row["seat_name"] . '</div>';
            echo '<div class="col-3">' . $row["seat_price"] . '</div>';
            echo '<input type="number" class="price-area" value="' . $row["seat_price"] . '" hidden>';
            echo '<div class="col-3 quantity-area">0</div>';
            echo '<div class="col-3 row">';
            echo '<div class="col-4 btn btn-outline-primary remove-ticket">-</div>';
            echo '<div class="col-4 ms-2 btn btn-primary add-ticket">+</div>';
            echo '</div></div>';
        };
        $stmt->close();
        ?>

        <div class="mt-3">Total Price <span id="totalPrice">0</span></div>
    </div>
    <!-- booking form -->
    <div class="container col-6 mt-5 p-5 banner-container" style="box-shadow: 1px 1px 3px black;">
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
                <input type="email" class="form-control" name="email_address" id="exampleFormControlInput1" placeholder="name@example.com" required>
            </div>
            <div class="mb-3">
                <label for="exampleFormControlInput2" class="form-label">Transaction Ref:</label>
                <input type="text" class="form-control" name="transactionRef" id="exampleFormControlInput2" placeholder="" required>
            </div>
            <input type="hidden" name="price" id="priceInput">
            <input type="hidden" name="quantity" id="quantityInput">


            <input type="hidden" name="id" value="<?php echo $booking_id; ?>" required>
            <div class="col-12">
                <button type="submit" class="mt-2 btn btn-primary">Book</button>
            </div>
        </form>
    </div>

    <!-- footer -->
    <?php include '../includes/footer.php'; ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>
</body>

</html>