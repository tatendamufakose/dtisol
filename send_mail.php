<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
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
        //$mail->Host       = 'smtp.dtisol.co.za';   // To replace with DTISOL SMTP server
        $mail->Host       = 'mail.dtisol.co.za';   // To replace with DTISOL SMTP server
        $mail->SMTPAuth   = true;
        $mail->Username   = 'sales@dtisol.co.za'; // Check actual DTISOL email (SMTP username)
        $mail->Password   = 'digitech@2019';             // To replace with DTISOL SMTP assword
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587; // Common SMTP port for SSL (TO DO: ndogona kuyedza 465 yeTLS if this is tied up)


        //For Production
        $mail->SMTPAutoTLS = true;
        $mail->Timeout = 30;
        $mail->SMTPKeepAlive = true;

        // Recipients
        //$mail->setFrom('your_email@yourdomain.com', 'Website Contact Form');
        $mail->setFrom($email, $name . ' (Via Website)');
        $mail->addAddress('sales@dtisol.co.za', 'DIGITECH Innovative Solutions'); // Recipient

        // Reply-to
        $mail->addReplyTo($email, $name);

        // Content
        $mail->isHTML(true);
        $mail->CharSet = 'UTF-8';   //Testing specific CharSet. TO DO: Chech for compatibility with isHTML

        $mail->Subject = "New Contact Form Submission from $name";
        $mail->Body    = "<strong>Name:</strong> $name<br>
                          <strong>Email:</strong> $email<br>
                          <strong>Message:</strong><br>" . nl2br($message);
        $mail->AltBody = "Name: $name\nEmail: $email\nMessage:\n$message";

        $mail->send();
        echo "<p class='alert alert-success'>Thank you, your message has been sent successfully!</p>";
    } catch (Exception $e) {
        echo "<p class='alert alert-error'>Uh-oh. It appears your message could not be sent at this time. Mailer Error: {$mail->ErrorInfo}</p><p>Please try again at a later time, or contact us via phone on: <a href='tel:0027814458003' class='button fit'>(+27) 81-445-8003</a></p>";
        error_log("Mailer Error: " . $mail->ErrorInfo);
    }
}
?>
