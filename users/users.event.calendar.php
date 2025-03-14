<?php
session_start(); //to check the user was logged in
include '../database/db.php'; //include database connection
include './includes/auth.user.php';

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Stadium|events</title>
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC"
        crossorigin="anonymous" />
    <link rel="stylesheet" href="../assets/CSS/styles.css">
</head>

<body>
    <?php include './includes/header.php'; ?>
    <div class="event-banner event-banner-calendar">
        <div class="container">
            <div class="calling-text" style="color: white;width: 50%;">
                <h1>YOUR&nbsp;ENTERTAINMENT IS OUR GOAL</h1>
            </div>
        </div>
    </div>
    <?php
    $event_result = $conn->query("SELECT * FROM events");

    echo '<div class="container frames">';
    echo '<div class = " row justify-content-center ">';
    while ($event_row = $event_result->fetch_assoc()) { 

        $stmt = $conn->prepare("SELECT * FROM seattype WHERE event_id = ?");
        $stmt->bind_param("i", $event_row["id"]);
        $stmt->execute();
        $seat_result = $stmt->get_result();

        while ($seat_row = $seat_result->fetch_assoc()) {  
            $stmt = $conn->prepare("SELECT * FROM stadiums WHERE id = ?");
            $stmt->bind_param("i", $event_row["stadium_id"]);
            $stmt->execute();
            $stadium = $stmt->get_result()->fetch_assoc();
            $stmt->close();

            echo '<div class="col-auto m-2">';
            echo '<div class="card" >';
            echo '<img src="../assets/Images/uploaded/' . $event_row["layout_image"] . '" style="width: 100%;" class="card-img-top image-fluid" alt="...">';
            echo '<div class="card-body p-2">';
            echo '<h5 class="card-title">' . ucwords($event_row["event_name"]) . '</h5>';
            echo '<div class="stadium-card-name">';
            echo '<img src="../assets/Images/uploaded/' . $event_row["layout_image"] . '"  class="card-img-icon image-fluid" alt="...">';
            echo '<span class="stadium-name">' . ucwords($stadium["stadium_name"]) . '</span>';
            echo '</div>';
            echo '<p class="card-text" style="font-size: 15px;">';
            echo '<span style="font-weight:bold;">' . ucwords($stadium["region"]) . '. ' . ucfirst($stadium["city"]) .  '</span>';
            echo ' ( ' . $event_row["event_date"] . ' )' . '</p>';
            echo '<div class="horizontal-line"></div>';
            echo '<div class="d-flex justify-content-between align-items-end">';
            echo '<a href="../users/booking.php?id=' . $event_row["id"] . "&seattype_id=" . $seat_row["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">Book Ticket</a>';
            echo '<a href="../users/users.event.calendar.php">Learn More -></a>';
            echo '</div></div></div></div>';
        }
    }

    echo '</div></div></div>';

    include '../includes/footer.php';
    ?>
    <script
        src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM"
        crossorigin="anonymous"></script>
    <script src="../assets/js/main.js"></script>

</body>

</html>