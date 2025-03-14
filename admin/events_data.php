<?php
include '../database/db.php';
$event_id = $_GET['event_id'];
// Query to count seat statuses
$stmt = $conn->prepare("SELECT seat_status, COUNT(*) AS total FROM seats  WHERE event_id = ? GROUP BY seat_status");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$seatData = [
    "available" => 0,
    "booked" => 0,
    "selected" => 0
];

while ($row = $result->fetch_assoc()) {
    $seatData[$row["seat_status"]] = $row["total"];
}

echo json_encode($seatData);

$conn->close();
