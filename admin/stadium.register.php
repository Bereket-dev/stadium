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
$seat_types = [];
$seat_amounts = [];
$seat_prices = [];

$message = "";
$message2 = "";
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
        $seat_types = $_POST["seat_type"];
    }
    if (isset($_POST["seat_amount"])) {
        $seat_amounts = $_POST["seat_amount"];
    }
    if (isset($_POST["seat_price"])) {
        $seat_prices = $_POST["seat_price"];
    }


    if (empty($stadium_name) || empty($stadium_address) || empty($event_name) || empty($event_date) || empty($seat_types) || empty($seat_amounts) || empty($seat_prices)) {
        $message = "Data field needed!";
        goto form;
    }

    $stmt = $conn->prepare("SELECT * FROM `event` WHERE stadium_id = (SELECT stadium_id FROM stadium WHERE stadium_name = ?) AND event_name = ?");
    $stmt->bind_param("ss", $stadium_name, $event_name);
    $stmt->execute();


    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $message = "This event has occured first! Please enter the new event! Thanks!";
        goto form;
    }

    //to prevent uploading occured event image first
    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        $image_name = $_FILES["image"]["name"];
        $tmp_name = $_FILES["image"]["tmp_name"];
        $folder = '../assets/Images/uploaded/' . $image_name;

        if (move_uploaded_file($tmp_name, $folder)) {
            $message2 = 'file uploaded successfully!';
        } else {
            $message = 'file not uploaded!';
            goto form;
        }
    } else {
        $message = 'no file input';
        goto form;
    }

    $stmt->close();
    $result->close();

    $stmt = $conn->prepare("SELECT * FROM Stadium WHERE stadium_name = ?");
    $stmt->bind_param("s", $stadium_name);
    $stmt->execute();

    $result = $stmt->get_result();

    //if the stadium is new it will be registered rather it will register the event only
    if ($result->num_rows == 0) {

        $stmt->close();
        $result->close();

        $stmt = $conn->prepare("INSERT INTO stadium(stadium_name, stadium_address, city, region, postal_code) VALUES(?, ?, ?, ?, ?)");
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


    $stmt = $conn->prepare("INSERT INTO event(event_name, event_date, stadium_id, event_description, layout_image) VALUES(?, ?, ?, ?, ?)");
    $stmt->bind_param("ssiss", $event_name, $event_date, $stadium_id, $event_description, $image_name);
    $stmt->execute();

    $event_id = $stmt->insert_id;
    $stmt->close();

    for ($i = 0; $i < count($seat_types); $i++) {
        $stmt = $conn->prepare("INSERT INTO seattype(seat_name, event_id, seat_amount, seat_price) VALUES(?, ?, ?, ?)");
        $stmt->bind_param("siii", $seat_types[$i], $event_id, $seat_amounts[$i], $seat_prices[$i]);
        $stmt->execute();
        $seattype_id = $stmt->insert_id;
        $stmt->close();

        $seat_status = "available";
        $seat_number = $seat_amounts[$i];
        $stmt = $conn->prepare("INSERT INTO seat(seattype_id, seat_status, `number`) VALUES(?, ?, ?)");
        $stmt->bind_param("isi", $seattype_id, $seat_status, $seat_number);
        $stmt->execute();
        $stmt->close();

        $seat_status = "booked";
        $seat_number = 0;
        $stmt = $conn->prepare("INSERT INTO seat(seattype_id, seat_status, `number`) VALUES(?, ?, ?)");
        $stmt->bind_param("isi", $seattype_id, $seat_status, $seat_number);
        $stmt->execute();
        $stmt->close();

        $seat_status = "selected";
        $seat_number = 0;
        $stmt = $conn->prepare("INSERT INTO seat(seattype_id, seat_status, `number`) VALUES(?, ?, ?)");
        $stmt->bind_param("isi", $seattype_id, $seat_status, $seat_number);
        $stmt->execute();
        $stmt->close();
    }
}
form:
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
    <?php
    include './includes/sidebar.php';  ?>

    <div class="content">
        <div class=" text-center">
            <p><?php echo $message; ?></p>
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
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-floating">
                            <textarea class="form-control mt-3" name="event_description" placeholder="Leave a description here" id="floatingTextarea"></textarea>
                            <label for="floatingTextarea">Event description</label>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="formFile" class="form-label">Event image</label>
                        <input class="form-control" name="image" type="file" id="formFile" required>
                        <p><?php echo $message2; ?></p>
                    </div>
                </div>

                <div class="seatContainer"></div>

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
    <script src="../assets/js/main.js">
    </script>
    <script>
        window.onload = () => addSeatRow();
    </script>
</body>

</html>