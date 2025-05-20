<?php

require '../includes/db.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

function sendMail($email, $code) {
    $mail = new PHPMailer(true);

    try {
        // SMTP settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'unodos2354@gmail.com';
        $mail->Password   = 'olbw fdup nopq ejeo';  // Use Gmail App Password
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;

        // Sender and recipient
        $mail->setFrom('unodos2354@gmail.com', 'JJC Car Rental');
        $mail->addAddress($email);

        // Email content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';
        $mail->Subject = 'Password Reset Code';
        $mail->Body = "<p>Hello,</p>
        <p>Your password reset code is: <strong>{$code}</strong></p>
        <p>If you didn’t request this, please ignore this email.
        <br>Need help? Contact us at support@jjcrental.com</p>";
        $mail->AltBody = "Hello,\n\nYour password reset code is: {$code}\n\nIf you didn't request this, ignore this email.";

        // Send
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Mailer Error: {$mail->ErrorInfo}");
        return false;
    }
}
