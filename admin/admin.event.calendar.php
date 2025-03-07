<?php
include "./includes/header.admin.php";


$event_result = $conn->query("SELECT * FROM events");

echo '<div class="container">';

foreach ($event_result as $event_row) {

    $stmt = $conn->prepare("SELECT * FROM seattype WHERE event_id = ?");
    $stmt->bind_param("i", $event_row["id"]);
    $stmt->execute();

    $seat_result = $stmt->get_result();

    echo '<div class="container d-flex justify-content-center my-5 p-3" style="border: 1px solid black">';
    echo '<div class="row">';

    foreach ($seat_result as $seat_row) {
        echo '<div class="col-auto m-2">';
        echo '<div class="card" style="width: 18rem;">';
        echo '      <img src="" style="height: 100px; width: 100%;" class="card-img-top" alt="...">';
        echo '<div class="scard-body p-2">';
        echo '<h5 class="card-title">';

        echo "<br>" . $event_row["event_name"] . "<br>";
        echo "<br>" .  $seat_row["seat_name"] . "<br>";
        echo '</h5>';
        echo '<p class="card-text">' . $event_row["event_description"] . '</p>';
        echo '<a href="../admin/event.edit.php?id=' . $event_row["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">EDIT EVENT</a>';
        echo '<a href="../admin/event.delete.php?id=' . $event_row["id"] . '" class="btn btn-danger" style="font-size: 14px;">REMOVE EVENT</a>';
        echo '</div></div></div>';
    }
    echo '</div></div>';
}

echo '</div></div>';
