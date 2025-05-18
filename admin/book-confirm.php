<?php
session_start(); // Ensure this is the first line


// Include necessary files
include '../database/db.php';
include './includes/auth.admin.php';

// Import PHPMailer at the top (fixing the issue)
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Initialize variables
$booking_id = "";
$first_name = "";
$last_name = "";
$stadium_name = "";
$event_name = "";
$seat_type = "";
$seat_number = "";
$seat_price = "";
$issent = false; // Ensure it's always initialized

// Validate and get the booking ID from URL
if (isset($_GET["id"]) && filter_var($_GET["id"], FILTER_VALIDATE_INT)) {
    $booking_id = $_GET["id"];
} else {
    die("Confirm page doesn't get ID");
}

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM booking WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$book_status = $row["status"];
$seattype_id  = $row["seattype_id"];
$stmt->close();


if (!$row) {
    die("Booking not found.");
}

// Check booking status and update
if ($book_status == 'pending') {
    $stmt = $conn->prepare("UPDATE booking SET `status` = 'confirmed' WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->close();    // Re-fetch the updated status

    $stmt = $conn->prepare("SELECT `status` FROM booking WHERE id = ?");
    $stmt->bind_param("i", $booking_id);
    $stmt->execute();
    $stmt->bind_result($book_status);
    $stmt->fetch();
    $stmt->close();
    $issent = false;
} elseif ($book_status === 'confirmed') {
    $issent = true;
}

if (!$issent) {
    // Update seat number based on status
    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'selected' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $selected_number = $seat["number"];
    $selected_number--;

    $stmt = $conn->prepare("UPDATE `seat` SET `number` = ?  WHERE id = ? AND seat_status = 'selected'");
    $stmt->bind_param("ii", $selected_number, $seat_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("SELECT * FROM `seat` WHERE seat_status = 'booked' AND seattype_id = ?");
    $stmt->bind_param("i", $seattype_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $seat = $result->fetch_assoc();
    $seat_id = $seat["id"];
    $booked_number = $seat["number"];
    $booked_number++;

    $stmt = $conn->prepare("UPDATE `seat` SET `number` = ?  WHERE id = ? AND seat_status = 'booked'");
    $stmt->bind_param("ii", $booked_number, $seat_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("UPDATE booking SET seat_number = ? WHERE id = ?");
    $stmt->bind_param("ii", $booked_number, $booking_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch booking details
$stmt = $conn->prepare("SELECT * FROM booking WHERE id = ?");
$stmt->bind_param("i", $booking_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$book_status = $row["status"];
$seattype_id = $row["seattype_id"];
$user_id = $row["user_id"];
$seat_number = $row["seat_number"];
$booking_qr = $row["qr_code"];
$stmt->close();

$stmt = $conn->prepare("SELECT * FROM user WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$first_name = $row["first_name"];
$first_name = $row["first_name"];
$email_address = $row["email"];
$stmt->close();

$fullName = $first_name . " " . $last_name;

$stmt = $conn->prepare("SELECT * FROM seattype WHERE id = ?");
$stmt->bind_param("i", $seattype_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$event_id = $row["event_id"];
$seat_type = $row["seat_name"];
$stmt->close();

// Fetch event details
$stmt = $conn->prepare("SELECT * FROM `event` WHERE id = ?");
$stmt->bind_param("i", $event_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("Event not found.");
}

$event_name = $row["event_name"];
$event_date = $row["event_date"];
$stadium_id = $row["stadium_id"];

// Fetch stadium details
$stmt = $conn->prepare("SELECT * FROM stadium WHERE id = ?");
$stmt->bind_param("i", $stadium_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();
$conn->close();

if (!$row) {
    die("Stadium not found.");
}

$stadium_name = $row["stadium_name"];

// Send confirmation email if not already sent
if ($book_status == "confirmed" && !$issent) {

    // Include PHPMailer files
    require '../PHPMailer-master/src/PHPMailer.php';
    require '../PHPMailer-master/src/SMTP.php';
    require '../PHPMailer-master/src/Exception.php';
    require './email_to_user/email.template.php';

    try {
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = 'berudere036@gmail.com';
        $mail->Password = 'owxospgoouedsezu'; // Use App Password
        $mail->SMTPSecure = 'tls'; // Changed from PHPMailer::ENCRYPTION_STARTTLS to 'tls'
        $mail->Port = 587;



        // Sender & Recipient
        $mail->setFrom('berudere036@gmail.com', 'Bekina Dev');
        $mail->addAddress($email_address, $fullName);

        // Load email template
        $emailBody = getEmailTemplate($fullName, $stadium_name, $event_name, $event_date, $seat_type, $seat_number, $booking_qr, $booking_id);

        // Email Content
        $mail->isHTML(true);
        $mail->Subject = 'Booking Confirmation';
        $mail->Body = $emailBody;
        $mail->AltBody = 'Your booking is confirmed!';

        // Send Email
        if (!$mail->send()) {
            error_log("Mail Error: " . $mail->ErrorInfo, 3, "error_log.txt");
        } else {
            // Clear session variables
            $_SESSION["full_name"] = "";
            $_SESSION["email_address"] = "";
            $_SESSION["event_name"] = "";
            $_SESSION["event_date"] = "";
            $_SESSION["seat_type"] = "";
            $_SESSION["seat_number"] = "";
            $_SESSION["message"] = '';
            // Redirect to book-management page
            header("Location: ./book-management.php");
            exit();
        }
    } catch (Exception $e) {
        echo "Error: {$mail->ErrorInfo}";
    }
} else {
    $_SESSION["message"] = 'Confirmation email has already been sent!';
    header("Location: ./book-management.php");
    exit();
}
