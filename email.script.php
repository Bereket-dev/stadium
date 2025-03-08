<?php
// Include PHPMailer files manually
require '../PHPMailer-master/src/PHPMailer.php';
require '../PHPMailer-master/src/SMTP.php';
require '../PHPMailer-master/src/Exception.php';
require '../email.template.php';

// Use PHPMailer namespace
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$mail = new PHPMailer(true);

try {
    // SMTP Configuration
    $mail->isSMTP();
    $mail->Host = 'live.smtp.mailtrap.io';
    $mail->SMTPAuth = true;
    $mail->Username = 'smtp@mailtrap.io';
    $mail->Password = '0cb6b861e79012bcad09027cc9e744c2'; // Use App Password for Gmail
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->SMTPDebug = 2; // Show debug output for troubleshooting
    
    // Sender & Recipient
    $mail->setFrom('smtp@mailtrap.io', 'bereket');
    $mail->addAddress($email_address, 'User Name');

    $emailBody = getEmailTemplate($fullName, $event_name, $event_id, $seat_number);
    // Email Content
    $mail->isHTML(true);
    $mail->Subject = 'Booking Confirmation';
    $mail->Body = $emailBody;
    $mail->AltBody = 'Your booking is confirmed!';
    // Load Email Template

    // Send Email
    $mail->send();
    echo 'Confirmation email sent!';
} catch (Exception $e) {
    echo "Error: {$mail->ErrorInfo}";
}
