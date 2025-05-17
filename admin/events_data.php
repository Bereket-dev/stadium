<?php
include '../database/db.php';

$eventId = $_GET['event_id'] ?? null;

if (!$eventId) {
    echo json_encode(['available' => 0, 'booked' => 0, 'selected' => 0]);
    exit;
}

// 1. Get all seattype IDs for this event
$seattype_stmt = $conn->prepare("SELECT id FROM seattype WHERE event_id = ?");
$seattype_stmt->bind_param("i", $eventId);
$seattype_stmt->execute();
$seattype_result = $seattype_stmt->get_result();

$seattype_ids = [];
while ($row = $seattype_result->fetch_assoc()) {
    $seattype_ids[] = $row['id'];
}

if (empty($seattype_ids)) {
    echo json_encode(['available' => 0, 'booked' => 0, 'selected' => 0]);
    exit;
}

// 2. Fetch seat status counts for those seattype IDs
$placeholders = implode(',', array_fill(0, count($seattype_ids), '?'));
$types = str_repeat('i', count($seattype_ids));
$query = "SELECT seat_status, SUM(`number`) AS total FROM seat WHERE seattype_id IN ($placeholders) GROUP BY `seat_status`";

$stmt = $conn->prepare($query);
$stmt->bind_param($types, ...$seattype_ids);
$stmt->execute();
$result = $stmt->get_result();

$data = ['available' => 0, 'booked' => 0, 'selected' => 0];
while ($row = $result->fetch_assoc()) {
    $status = strtolower($row['seat_status']);
    if (isset($data[$status])) {
        $data[$status] = (int)$row['total'];
    }
}

echo json_encode($data);
$conn->close();
