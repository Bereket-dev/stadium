<?php
include './includes/header.php';



$event_id  = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $event_id = $_GET['id'];
} else {
    header("Location: users.event.calendar.php");
    exit();
}

if (isset($_GET["seat_price"]) && !empty($_GET["seat_price"])) {
    $seat_price = $_GET['seat_price'];
} else {
    header("Location: users.event.calendar.php");
    exit();
}

if (isset($_GET["seat_name"]) && !empty($_GET["seat_name"])) {
    $seat_name = $_GET['seat_name'];
} else {
    //header("Location: users.event.calendar.php");
    header("Location: users.event.calendar.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "Event not found!";
    exit();
}
$stmt->close();




if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = "";
    $last_name = "";
    $email_address = "";

    if (isset($_POST["first_name"])) {
        $first_name = trim($_POST["first_name"]);
    }
    if (isset($_POST["last_name"])) {
        $last_name = trim($_POST["last_name"]);
    }
    if (isset($_POST["email_address"])) {
        $email_address = trim($_POST["email_address"]);
    }



    if (empty($first_name) || empty($last_name) || empty($email_address)) {
        echo "Data field needed";
        exit();
    }

    $username = $_SESSION["username"];

    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $user_id =  $row["id"];
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM seats WHERE seat_type = ? AND seat_status = 'available' AND event_id = ? LIMIT 1");
    $stmt->bind_param("si", $seat_name, $event_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    if (!$row) {
        echo "seat id is not available";
        exit();
    }
    

    $seat_id = $row["id"];
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO bookings(first_name, last_name, email_address, user_id, event_id, seat_id, seat_type, price) VALUES(?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssiiisi", $first_name, $last_name, $email_address,  $user_id, $event_id, $seat_id, $seat_name, $seat_price);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("UPDATE seats SET seat_status = 'selected' WHERE id = ?");
    $stmt->bind_param("i", $seat_id);
    $stmt->execute();
    $stmt->close();
}

?>
<div class="container col-6 mt-5 p-5" style="box-shadow: 1px 1px 3px black;">
    <div class="text-center fs-3 mb-3"><?php echo $event["event_name"]; ?></div>
    <form class="row g-3 needs-validation" novalidate method="post">
        <div class="col-md-6">
            <label for="validationCustom01" class="form-label">First name</label>
            <input type="text" class="form-control" name="first_name" id="validationCustom01" value="Mark" required>
            <div class="valid-feedback">
                Looks good!
            </div>
        </div>
        <div class="col-md-6">
            <label for="validationCustom02" class="form-label">Last name</label>
            <input type="text" class="form-control" name="last_name" id="validationCustom02" value="Otto" required>
            <div class="valid-feedback">
                Looks good!
            </div>
        </div>

        <div class="mb-3">
            <label for="exampleFormControlInput1" class="form-label">Email address</label>
            <input type="email" class="form-control" name="email_address" id="exampleFormControlInput1" placeholder="name@example.com">
        </div>

        <div class="mb-3 row">
            <label for="staticEmail" class="col-sm-2 col-form-label">Price:</label>
            <div class="col-sm-10">
                <input type="text" name="seat_price" readonly class="form-control-plaintext" id="staticEmail"
                    value="<?php echo $seat_price . " ETB"; ?>">
            </div>
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
        <div class="col-12">
            <button class="btn btn-primary" type="submit">Book</button>
        </div>
    </form>
</div>