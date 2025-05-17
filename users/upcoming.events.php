<?php
include './database/db.php';

$result = $conn->query("SELECT * FROM `event`");
$eventArray = [];
while ($event_date  = $result->fetch_assoc()) {
    $eventArray[] = $event_date;
}

usort($eventArray, function ($a, $b) {
    return strtotime($a["event_date"]) - strtotime($b["event_date"]);
});

//card of the first event
$maxEvents = min(3, count($eventArray));

echo '<div class="row justify-content-center gap-2">';
for ($i = 0; $i < $maxEvents; $i++) {
    $event = $eventArray[$i];


    $stmt = $conn->prepare("SELECT * FROM stadium WHERE id = ?");
    $stmt->bind_param("i", $event["stadium_id"]);
    $stmt->execute();
    $stadium = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    echo '<div class="col-auto m-2">';
    echo '<div class="card" >';
    echo '<img src="../assets/Images/uploaded/' . $event["layout_image"] . '" style="width: 100%;" class="card-img-top image-fluid" alt="...">';
    echo '<div class="card-body p-2">';
    echo '<h5 class="card-title">' . ucwords($event["event_name"]) . '</h5>';
    echo '<div class="stadium-card-name">';
    echo '<img src="../assets/Images/uploaded/' . $event["layout_image"] . '"  class="card-img-icon image-fluid" alt="...">';
    echo '<span class="stadium-name">' . ucwords($stadium["stadium_name"]) . '</span>';
    echo '</div>';
    echo '<p class="card-text" style="font-size: 15px;">';
    echo '<span style="font-weight:bold;">'. ucwords($stadium["region"]) . '. ' . ucfirst($stadium["city"]) .  '</span>';
    echo ' ( ' . $event["event_date"] . ' )' . '</p>';
    echo '<div class="horizontal-line"></div>';
    echo '<div class="d-flex justify-content-between align-items-end">';
    echo '<a href="../users/booking.php?id=' . $event["id"] . '" class="btn btn-primary me-2" style="font-size: 14px;">Book Ticket</a>';
    echo '<a href="../users/users.event.calendar.php">Learn More -></a>';
    echo '</div></div></div></div>';
}
echo '</div>';
