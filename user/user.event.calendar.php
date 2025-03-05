<?php
include("../database/db.php");
include("./includes/header.html");



$result = $conn->query("SELECT * FROM events");

echo '<div class="container">';
echo '<div class="row">';

foreach ($result as $row) {
    echo '<div class="col-auto m-2">';
    echo '<div class="card" style="width: 18rem;">';
    echo '      <img src="" style="height: 100px; width: 100%;" class="card-img-top" alt="...">';
    echo '<div class="scard-body p-2">';
    echo '<h5 class="card-title">';

    echo "<br>" . $row["event_name"] . "<br>";
    echo '</h5>';
    echo '<p class="card-text">' . $row["event_description"] . '</p>';
    echo '<a href="./booking.php?id=' . $row["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">Book Ticket</a>';
    echo '</div></div></div>';
}

echo '</div></div>';
?>

