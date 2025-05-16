<?php
session_start(); //to check the user was logged in
include '../database/db.php';
include './includes/auth.admin.php';

$event_id = "";

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

if (!$event) {
    echo "Event not found!";
    exit();
}
$stmt->close();


// Check if event ID is provided

// 1. First get the image filename from the database
$sql = "SELECT layout_image FROM `event` WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $image_name = $row['layout_image'];
    $image_path = '../assets/Images/uploaded/' . $image_name;

    // 2. Delete the event from database
    $delete_sql = "DELETE FROM `event` WHERE id = ?";
    $delete_stmt = $conn->prepare($delete_sql);
    $delete_stmt->bind_param("i", $event_id);

    if ($delete_stmt->execute()) {
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
    }

    $delete_stmt->close();
}

header("Location: event-management.php");
exit();
