<?php
function getEmailTemplate($fullName, $stadium_name, $event_name, $event_date, $seat_type, $seat_number, $booking_qr, $booking_id): string
{
    return "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; margin: 20px; padding: 0; background-color: #f4f4f4; }
            .container { max-width: 600px; margin: 0 auto; background: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0px 0px 10px #ccc; }
            h2 { color: #333; }
            p { font-size: 16px; line-height: 1.5; color: #555; }
            .details { background: #f9f9f9; padding: 10px; border-radius: 5px; margin-top: 10px; }
            .footer { text-align: center; margin-top: 20px; font-size: 14px; color: #777; }
            .btn { display: inline-block; padding: 10px 20px; background: #28a745; color: white; text-decoration: none; border-radius: 5px; margin-top: 10px; }
        </style>
    </head>
    <body>
        <div class='container'>
            <h2>Booking Confirmation</h2>
            <p>Hello <strong>$fullName</strong>,</p>
            <p>Thank you for booking your ticket. Below are your booking details:</p>
            
            <div class='details'>
                <p><strong>Event:</strong> $event_name</p>
                <p><strong>Stadium:</strong>$stadium_name </p>
                <p><strong>Date:</strong> $event_date</p>
                <p><strong>Seat Type:</strong> $seat_type</p>
                <p><strong>Seat Number:</strong> $seat_number</p>
                <br>
                <img src = '$booking_qr' alt = 'qr code'/>
            </div>
            
            <p>If you have any questions, please contact our support team.</p>
            
            <a href='" . $_SERVER['HOST'] . "/users/confirm.booking.php?id=$booking_id' class='btn text-white text-decoration-none'>View Booking</a>
            
            <p class='footer'>&copy; " . date('Y') . " Your Company. All Rights Reserved.</p>
        </div>
    </body>
    </html>";
}
