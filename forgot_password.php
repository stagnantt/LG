<?php
// Include database connection
require 'database.php'; // Ensure this file contains the correct database connection code
require 'vendor/autoload.php'; // Load Composer's autoloader

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

date_default_timezone_set('Asia/Manila');

function sendResetEmail($email, $token) {
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; 
        $mail->SMTPAuth   = true; 
        $mail->Username = 'wsenpai213@gmail.com'; 
        $mail->Password = 'hiukvuuzwfpitvaz'; 
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; 
        $mail->Port = 465;

        // Recipients
        $mail->setFrom('wsenpai213@gmail.com', 'Admin');
        $mail->addAddress($email); // Add a recipient

        // Content
        $resetLink = "http://localhost/lg/reset_password.php?token=$token"; 
        $mail->isHTML(true);
        $mail->Subject = 'Password Reset';
        $mail->Body    = 'Click the link to reset your password: <a href="' . $resetLink . '">Reset Password</a>';
        $mail->AltBody = 'Click the link to reset your password: ' . $resetLink;

        $mail->send();
        echo 'Message has been sent';
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email']; // Assuming you're getting the email from a form

    // Generate a token and set the expiry time
    $token = bin2hex(random_bytes(16));
    $token_expiry = date("Y-m-d H:i:s", strtotime("+1 hour"));

    // Update the usercredentials table with the reset token and expiry
    $stmt = $conn->prepare("UPDATE usercredentials SET reset_token = ?, token_expiry = ? WHERE email = ?");
    $stmt->bind_param("sss", $token, $token_expiry, $email);

    if ($stmt->execute()) {
        // Send reset email
        sendResetEmail($email, $token);
    } else {
        echo "Error updating token: " . $conn->error;
    }
}
?>
