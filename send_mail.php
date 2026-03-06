<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Composer autoload

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Basic validation
    $name    = trim($_POST['name']);
    $email   = trim($_POST['email']);
    $message = trim($_POST['message']);

    $errors = [];

    if (empty($name)) {
        $errors[] = "Name is required.";
    }

    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "A valid email is required.";
    }

    if (empty($message)) {
        $errors[] = "Message cannot be empty.";
    }

    if (!empty($errors)) {
        // Show errors
        foreach ($errors as $error) {
            echo "<div class='alert alert-error'>$error</div>";
        }
        exit;
    }

    // Configure PHPMailer
    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host       = 'smtp.yourdomain.com';   // To replace with DTISOL SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'info@dtisol.co.za'; // Check actual DTISOL email (SMTP username)
        $mail->Password   = 'your_password';             // To replace with DTISOL SMTP assword
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Common SMTP port (ndogona kuyedza 465 if this is tied up)

        // Recipients
        $mail->setFrom('your_email@yourdomain.com', 'Website Contact Form');
        $mail->addAddress('info@dtisol.co.za', 'DIGITECH Innovative Solutions'); // Recipient

        // Reply-to
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->Subject = "New Contact Form Submission from $name";
        $mail->Body    = "<strong>Name:</strong> $name<br>
                          <strong>Email:</strong> $email<br>
                          <strong>Message:</strong><br>" . nl2br($message);
        $mail->AltBody = "Name: $name\nEmail: $email\nMessage:\n$message";

        $mail->send();
        echo "<p class='alert alert-success'>Thank you, your message has been sent successfully!</p>";
    } catch (Exception $e) {
        echo "<p class='alert alert-error'>Message could not be sent. Mailer Error: {$mail->ErrorInfo}</p>";
    }
}
?>
