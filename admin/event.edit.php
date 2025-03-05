<?php
include("../database/db.php");
include("./includes/header.admin.html");

$event_id  = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $event_id = $_GET['id'];
} else {
    header("Location: admin.event.calendar.php");
    exit();
}
$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("s", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();

if (!$event) {
    echo "Event not found!";
    exit();
}

//check form date are setted or not
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["event_name"])) {
        $event_name = trim($_POST["event_name"]);
    }
    if (isset($_POST["event_date"])) {
        $event_date = trim($_POST["event_date"]);
    }
    if (isset($_POST["event_description"])) {
        $event_description = trim($_POST["event_description"]);
    }
    if (isset($_POST["layout_image"])) {
        $layout_image = $_POST["layout_image"];
    }


    if (isset($_POST["seat_name"])) {
        $seat_name = trim($_POST["seat_name"]);
    }
    if (isset($_POST["seat_amount"])) {
        $seat_amount = (int)($_POST["seat_amount"]);
    }
    if (isset($_POST["seat_price"])) {
        $seat_price = (int)$_POST["seat_price"];
    }



    if (empty($event_name) || empty($event_date) || empty($seat_name) || empty($seat_amount) || empty($seat_price)) {
        echo "Data field needed!";
        exit();
    }



    $stmt->close();
    $result->close();

    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, event_description = ? WHERE id = ? ");
    $stmt->bind_param("sssi", $event_name, $event_date, $event_description, $event_id);
    $stmt->execute();
    $stmt->close();


    $stmt = $conn->prepare("UPDATE seattype SET seat_name = ?, seat_amount = ?, seat_price = ? WHERE event_id = ? ");
    $stmt->bind_param("sssi", $seat_name, $seat_amount, $seat_price, $event_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: admin.event.calendar.php");
    exit();
}

?>

<div class="text-center">
    <h1>Event Edition</h1>
</div>
<div class="container p-3" style="max-width: 80vw;">
    <form class="row g-3" method="post">

        <div class="col-md-6">
            <label for="inputAddress" class="form-label">Event</label>
            <input type="text" class="form-control" name="event_name" id="inputEvent" placeholder="World Cup" required>
        </div>
        <div class="col-md-6">
            <label for="inputAddress2" class="form-label">Event Date</label>
            <input type="datetime-local" class="form-control" name="event_date" id="inputAddress2" placeholder="Apartment, studio, or floor" required>
        </div>

        <div class="col-md-2">
            <label for="inputState" class="form-label">Seat Type</label>
            <select name="seat_name" id="inputState" class="form-select" required>
                <option selected>Choose...</option>
                <option value="vip">VIP</option>
                <option value="viip">VIIP</option>
                <option value="normal">NORMAL</option>
            </select>
        </div>
        <div class="col-md-2">
            <label for="inputZip" class="form-label">Amount</label>
            <input type="number" name="seat_amount" placeholder="total seat" min="0" class="form-control" id="" required>
        </div>
        <div class="col-md-2">
            <label for="inputZip" class="form-label">PRICE</label>
            <input type="number" name="seat_price" placeholder="each price" min="0" class="form-control" id="" required>
        </div>
        <div class="col-md-6">
            <div class="form-floating">
                <textarea class="form-control mt-3" name="event_description" placeholder="Leave a description here" id="floatingTextarea"></textarea>
                <label for="floatingTextarea">Event Description</label>
            </div>
        </div>
        <div class="col-md-6 mb-3">
            <label for="formFile" class="form-label">Event image</label>
            <input class="form-control" name="layout_image" type="file" id="formFile">
        </div>

        <div class="col-12">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </form>
</div>