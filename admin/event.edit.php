<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';


$event_id  = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $event_id = $_GET['id'];
} else {
    header("Location: admin.event.calendar.php");
    exit();
}

if (isset($_GET["seattype_id"])) {
    $seattype_id = $_GET['id'];
} else {
    header("Location: admin.event.calendar.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM events WHERE id = ?");
$stmt->bind_param("s", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stadium_id = $event["stadium_id"];

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
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = $_FILES["image"]["name"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        $folder = '../assets/Images/uploaded/' . $image_name;

        if (move_uploaded_file($tmp_name, $folder)) {
            echo 'file uploaded successfully!';
        } else {
            echo 'file not uploaded!';
        }
    } else {
        echo 'no file input';
    }


    if (isset($_POST["seat_type"])) {
        $seat_type = trim($_POST["seat_type"]);
    }
    if (isset($_POST["seat_amount"])) {
        $seat_amount = (int)($_POST["seat_amount"]);
    }
    if (isset($_POST["seat_price"])) {
        $seat_price = (int)$_POST["seat_price"];
    }



    if (empty($event_name) || empty($event_date) || empty($seat_type) || empty($seat_amount) || empty($seat_price)) {
        echo "Data field needed!";
        exit();
    }



    $stmt->close();
    $result->close();


    $stmt = $conn->prepare("UPDATE events SET event_name = ?, event_date = ?, event_description = ?, layout_image = ? WHERE id = ? ");
    $stmt->bind_param("ssssi", $event_name, $event_date, $event_description, $image_name, $event_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("SELECT seat_amount FROM seattype WHERE id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $seat_amount_before = $stmt->get_result()->fetch_assoc();
    $stmt->close();


    //to manage the number of seats base on seat amount
    if ($seat_amount > $seat_amount_before) {
        $seat_number = $seat_amount_before;
        for ($num = $seat_amount; $num > $seat_amount_before; $num--) {
            $seat_number++;
            $stmt = $conn->prepare("INSERT INTO seats(stadium_id, seattype_id, event_id, seat_number) VALUES(?, ?, ?, ?)");
            $stmt->bind_param("isii", $stadium_id, $seattype_id, $event_id, $seat_number);
            $stmt->execute();
            $stmt->close();
        }
    } else if ($seat_amount < $seat_amount_before) {
        for ($num = $seat_amount; $num < $seat_amount_before; $num++) {
            $stmt = $conn->prepare("DELETE FROM seats WHERE seattype_id = ? AND event_id = ?");
            $stmt->bind_param("ii", $seattype_id, $event_id);
            $stmt->execute();
            $stmt->close();
        }
    }


    $stmt = $conn->prepare("UPDATE seattype SET seat_name = ?, seat_amount = ?, seat_price = ? WHERE id = ? AND event_id = ?");
    $stmt->bind_param("siiii", $seat_type, $seat_amount, $seat_price, $seattype_id, $event_id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header("Location: admin.event.calendar.php");
    exit();
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin| edit event</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <!-- side bar -->
    <?php
    include './includes/sidebar.php';  ?>

    <div class="content">
        <div class="text-center ">
            <h1>Event Edition</h1>
        </div>
        <div class="container  p-3" style="max-width: 80vw;">
            <form class="row g-3" method="post" enctype="multipart/form-data">

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
                    <select name="seat_type" id="inputState" class="form-select" required>
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
                    <input class="form-control" name="image" type="file" id="formFile">
                </div>

                <div class="col-12">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </div>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
</body>

</html>