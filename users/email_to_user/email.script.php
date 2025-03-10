<?php
// Include PHPMailer files manually
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';
require './email_to_user/email.template.php';

// Use PHPMailer namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'berudere036@gmail.com';
    $mail->Password = 'owxospgoouedsezu'; // Use your App Password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Try switching to ENCRYPTION_SMTPS and port 465 if needed
    $mail->Port = 587;


    // Ensure variables are defined
    $fullName = $_SESSION["full_name"];
    $email_address = $_SESSION["email_address"];
    $event_name = $_SESSION["event_name"];
    $event_date = $_SESSION["event_date"];
    $seat_type = $_SESSION["seat_type"];
    $seat_number = $_SESSION["seat_number"];
    $booking_qr = $_SESSION["booking_qr"];

    // Sender & Recipient
    $mail->setFrom('berudere036@gmail.com', 'beruder036');
    $mail->addAddress($email_address, $fullName);

    // Load Email Template
    $emailBody = getEmailTemplate($fullName, $event_name, $event_date, $seat_type, $seat_number, $booking_qr);

    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation';
    $mail->Body = $emailBody;
    $mail->AltBody = 'Your booking is confirmed!';

    // Send Email
    if (!$mail->send()) {
        error_log("Mail Error: " . $mail->ErrorInfo, 3, "error_log.txt");
    } else {
        echo 'Confirmation email sent!';
        $_SESSION["full_name"] = "";
        $_SESSION["email_address"] = "";
        $_SESSION["event_name"] = "";
        $_SESSION["event_date"] = "";
        $_SESSION["seat_type"] = "";
        $_SESSION["seat_number"] = "";

        $_SESSION["issent"] = true; //true prevent repetion of mail sending
    }
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
