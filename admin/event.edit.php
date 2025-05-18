<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';


$event_id  = "";

if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $event_id = $_GET['id'];
} else {
    header("Location: event-management.php");
    exit();
}

$stmt = $conn->prepare("SELECT * FROM `event` WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();

$result = $stmt->get_result();
$event = $result->fetch_assoc();
$stadium_id = $event["stadium_id"];

if (!$event) {
    echo "Event not found!";
    exit();
}

//initialize seat type variables
$seat_types = [];
$seat_amounts = [];
$seat_prices = [];

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
        $seat_types = $_POST["seat_type"];
    }
    if (isset($_POST["seat_amount"])) {
        $seat_amounts = $_POST["seat_amount"];
    }
    if (isset($_POST["seat_price"])) {
        $seat_prices = $_POST["seat_price"];
    }

    if (empty($event_name) || empty($event_date) || empty($seat_types) || empty($seat_amounts) || empty($seat_prices)) {
        echo "Data field needed!";
        exit();
    }

    $stmt = $conn->prepare("SELECT COUNT(event_id) AS type_count FROM seattype WHERE event_id = ? GROUP BY event_id");
    $stmt->bind_param("i", $event_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $old_seattype_count = 0;
    if ($row = $result->fetch_assoc()) {
        $old_seattype_count = $row["type_count"];
    }

    for ($i  = 0; $i < count($seat_types); $i++) {
        $stmt = $conn->prepare("SELECT * FROM seattype WHERE seat_name = ? AND event_id = ?");
        $stmt->bind_param("si", $seat_types[$i], $event_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $seat_amount_before = $row["seat_amount"];
            $seattype_id = $row["id"];

            $stmt = $conn->prepare("SELECT * FROM seat WHERE seattype_id = ? AND `seat_status` = 'booked'");
            $stmt->bind_param("i", $seattype_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $booked_number = $result->fetch_assoc()["number"];

            $stmt = $conn->prepare("SELECT * FROM seat WHERE seattype_id = ? AND `seat_status` = 'selected'");
            $stmt->bind_param("i", $seattype_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $selected_number = $result->fetch_assoc()["number"];

            $used_seats = $booked_number + $selected_number;

            if ($seat_amounts[$i] > $seat_amount_before) {
                $stmt = $conn->prepare("UPDATE seattype SET seat_amount = ?, seat_price = ? WHERE id = ?");
                $stmt->bind_param("iii", $seat_amounts[$i], $seat_prices[$i], $seattype_id);
                $stmt->execute();
                $stmt->close();

                $stmt = $conn->prepare("UPDATE seat SET `number` = ? WHERE seattype_id = ? AND `seat_status` = 'available'");
                $stmt->bind_param("ii", $seat_amounts[$i],  $seattype_id);
                $stmt->execute();
                $stmt->close();
            } else if ($seat_amounts[$i] < $seat_amount_before) {

                if ($seat_amounts[$i] <= $used_seats) {
                    $_SESSION["message"] = "These seats are already in use! Seat amount should be greater than " . $used_seats . "!";
                } else {
                    $stmt = $conn->prepare("UPDATE seattype SET seat_amount = ?, seat_price = ? WHERE id = ?");
                    $stmt->bind_param("iii", $seat_amounts[$i], $seat_prices[$i], $seattype_id);
                    $stmt->execute();
                    $stmt->close();

                    $stmt = $conn->prepare("UPDATE seat SET `number` = ? WHERE  seattype_id = ? AND`seat_status` = 'available'");
                    $stmt->bind_param("ii", $seat_amounts[$i],  $seattype_id);
                    $stmt->execute();
                    $stmt->close();
                }
            }
        } else {
            $seat_status = "available";

            $stmt = $conn->prepare("INSERT INTO seattype(seat_name, event_id, `seat_amount`, seat_price) VALUES(?, ?, ?, ?)");
            $stmt->bind_param("siii", $seat_types[$i], $event_id, $seat_amounts[$i], $seat_prices[$i]);
            $stmt->execute();
            $seattype_id = $stmt->insert_id;
            $stmt->close();

            $stmt = $conn->prepare("INSERT INTO seat(seattype_id, seat_status, `number`) VALUES(?, ?, ?)");
            $stmt->bind_param("isi", $seattype_id, $seat_status, $seat_amounts[$i]);
            $stmt->execute();
            $stmt->close();

            $seat_status = "selected";
            $seat_number = 0;
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
        }
    }

    if ($old_seattype_count > count($seat_types)) {
        $remove_count = $old_seattype_count - count($seat_types);
        $stmt = $conn->prepare("DELETE FROM seattype WHERE event_id = ? ORDER BY updated_at DESC  LIMIT ?");
        $stmt->bind_param("ii", $event_id, $remove_count);
        $stmt->execute();
    }

    if (isset($_FILES["image"]) && $_FILES["image"]["error"] == 0) {
        // Delete old image if exists
        $sql = "SELECT layout_image FROM `event` WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $event_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $old_image_name = $row['layout_image'];
            $image_path = '../assets/Images/uploaded/' . $old_image_name;
            // 3. Delete the image file if it exists
            if (file_exists($image_path)) {
                if (unlink($image_path)) {
                    $_SESSION['message'] = "Event and image deleted successfully";
                } else {
                    $_SESSION['error'] = "Event deleted but failed to remove image";
                }
            } else {
                $_SESSION['message'] = "Event deleted (image not found)";
            }
        } else {
            $_SESSION['error'] = "Failed to delete event";
        };
    }



    $stmt = $conn->prepare("UPDATE `event` SET event_name = ?, event_date = ?, event_description = ?, layout_image = ? WHERE id = ? ");
    $stmt->bind_param("ssssi", $event_name, $event_date, $event_description, $image_name, $event_id);
    $stmt->execute();
    $stmt->close();

    header("Location: event-management.php");
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
        <?php if (isset($_SESSION['message'])): ?>
            <div class="alert alert-success"><?= $_SESSION['message'];
                                                unset($_SESSION['message']); ?></div>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= $_SESSION['error'];
                                            unset($_SESSION['error']); ?></div>
        <?php endif; ?>

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