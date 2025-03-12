<?php
session_start(); //to check the user was logged in
include '../database/db.php';
//intialize stadium data variable
$stadium_name = "";
$stadium_address = "";
$stadium_city = "";
$stadium_region = "";
$stadium_postal_code = "";

//intialize event data variable
$event_name = "";
$event_date = "";

//initialize seat type variables
$seat_type = "";
$seat_amount = "";
$seat_price = "";


//check form date are setted or not
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["stadium_name"])) {
        $stadium_name = trim($_POST["stadium_name"]);
    }
    if (isset($_POST["stadium_address"])) {
        $stadium_address = trim($_POST["stadium_address"]);
    }
    if (isset($_POST["stadium_city"])) {
        $stadium_city = trim($_POST["stadium_city"]);
    }
    if (isset($_POST["stadium_region"])) {
        $stadium_region = trim($_POST["stadium_region"]);
    }
    if (isset($_POST["stadium_postal_code"])) {
        $stadium_postal_code = trim($_POST["stadium_postal_code"]);
    }


    if (isset($_POST["event_name"])) {
        $event_name = trim($_POST["event_name"]);
    }
    if (isset($_POST["event_date"])) {
        $event_date = trim($_POST["event_date"]);
    }
    if (isset($_POST["event_description"])) {
        $event_description = trim($_POST["event_description"]);
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

    if (!isset($_POST["agree_terms"])) {
        echo "You must agree to terms!";
        exit();
    }

    if (empty($stadium_name) || empty($stadium_address) || empty($event_name) || empty($event_date) || empty($seat_type) || empty($seat_amount) || empty($seat_price)) {
        echo "Data field needed!";
        exit();
    }

    $stmt = $conn->prepare("SELECT * FROM events WHERE stadium_id = (SELECT stadium_id FROM stadiums WHERE stadium_name = ?) AND event_name = ?");
    $stmt->bind_param("ss", $stadium_name, $event_name);
    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "This event has occured first! <br>Please enter the new event!<br> Thanks!";
        goto form;
    }

    //to prevent uploading occured event image first
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = $_FILES["image"]["name"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        $folder = '../assets/Images/uploaded/' . $image_name;

        if (move_uploaded_file($tmp_name, $folder)) {
            echo 'file uploaded successfully!';
        } else {
            echo 'file not uploaded!';
            goto form;
        }
    } else {
        echo 'no file input';
        goto form;
    }

    $stmt->close();
    $result->close();

    $stmt = $conn->prepare("SELECT * FROM Stadiums WHERE stadium_name = ?");
    $stmt->bind_param("s", $stadium_name);
    $stmt->execute();

    $result = $stmt->get_result();

    //if the stadium is new it will be registered rather it will register the event only
    if ($result->num_rows == 0) {

        $stmt->close();
        $result->close();

        $stmt = $conn->prepare("INSERT INTO stadiums(stadium_name, stadium_address, city, region, postal_code) VALUES(?, ?, ?, ?, ?)");
        $stmt->bind_param("sssss", $stadium_name, $stadium_address, $stadium_city, $stadium_region, $stadium_postal_code);
        $stmt->execute();


        $stadium_id = $stmt->insert_id;
        $stmt->close();
    } else {
        //other wise it finds the stadium id by its name
        $row = $result->fetch_assoc();
        $stadium_id = $row["id"];
        $stmt->close();
    }


    $stmt = $conn->prepare("INSERT INTO events(event_name, event_date, stadium_id, event_description, layout_image) VALUES(?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $event_name, $event_date, $stadium_id, $event_description, $image_name);
    $stmt->execute();

    $event_id = $stmt->insert_id;
    $stmt->close();

    $stmt = $conn->prepare("INSERT INTO seattype(seat_name, stadium_id, event_id, seat_amount, seat_price) VALUES(?, ?, ?, ?, ?)");
    $stmt->bind_param("siiii", $seat_type, $stadium_id, $event_id, $seat_amount, $seat_price);
    $stmt->execute();
    $seattype_id = $stmt->insert_id;
    $stmt->close();

    $seat_number = 0;
    for ($num = 0; $num < $seat_amount; $num++) {
        $seat_number++;
        $stmt = $conn->prepare("INSERT INTO seats(stadium_id, seattype_id, event_id, seat_number) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("isii", $stadium_id, $seattype_id, $event_id, $seat_number);
        $stmt->execute();
        $stmt->close();
    }
    $conn->close();
}
form:
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport"="width=device-width, initial-scale=1.0">
    <title>Admin| stadium registration</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <!-- side bar -->
    <?php include './includes/sidebar.html'; ?>

    <div class="content">
        <div class=" text-center">
            <h1>Stadium Registration</h1>
        </div>
        <div class="container  p-3" style="max-width: 80vw;">
            <form class="row g-3" method="post" enctype="multipart/form-data">
                <div class="col-md-6">
                    <label for="inputEmail4" class="form-label">Stadium Name</label>
                    <input type="text" name="stadium_name" class="form-control" id="inputStadiumName" required>
                </div>
                <div class="col-md-6">
                    <label for="inputAddress" class="form-label">Address</label>
                    <input type="text" name="stadium_address" class="form-control" id="inputAddress" placeholder="1234 Main St" required>
                </div>
                <div class="col-md-6">
                    <label for="inputCity" class="form-label">City</label>
                    <input type="text" class="form-control" name="stadium_city" id="inputCity" required>
                </div>
                <div class="col-md-4">
                    <label for="inputCity" class="form-label">Region</label>
                    <input type="text" class="form-control" name="stadium_region" id="inputCity" required>
                </div>
                <div class="col-md-2">
                    <label for="inputZip" class="form-label">Zip</label>
                    <input type="text" class="form-control" name="stadium_postal_code">
                </div>
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
                        <label for="floatingTextarea">Comments</label>
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label for="formFile" class="form-label">Event image</label>
                    <input class="form-control" name="image" type="file" id="formFile" required>
                </div>
                <div class="col-12">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="gridCheck" name="agree_terms" required>
                        <label class="form-check-label" for="gridCheck">
                            Terms and Policy
                        </label>
                    </div>
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