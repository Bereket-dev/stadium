<?php

include './database/db.php';

$result = $conn->query("SELECT * FROM events");
$eventArray = [];
while ($event_date  = $result->fetch_assoc()) {
    $eventArray[] = $event_date;
}

usort($eventArray, function ($a, $b) {
    return strtotime($a["event_date"]) - strtotime($b["event_date"]);
});

//card of the first event
$maxEvents = min(3, count($eventArray));

echo '<div class="d-flex justify-content-center gap-2">';
for ($i = 0; $i < $maxEvents; $i++) {
    $event = $eventArray[$i];
    echo '<div class="col-auto m-2">';
    echo '<div class="card" style="width: 18rem;">';
    echo '<img src="../assets/Images/uploaded/' . $event["layout_image"] . '" style="height: 100px; width: 100%;" class="card-img-top image-fluid" alt="...">';
    echo '<div class="card-body p-2">';
    echo '<h5 class="card-title">';

    echo "<br>" . $event["event_name"] . "<br>";
    echo '</h5>';
    echo '<p class="card-text">' . $event["event_description"] . '</p>';
    echo '<a href="./booking.php?id=' . $event["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">Book Ticket</a>';
    echo '</div></div></div>';
}
echo '</div>';
