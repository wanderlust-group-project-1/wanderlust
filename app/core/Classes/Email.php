<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;



class EmailSender {
    private $mailer;

    public function __construct() {
        // Include the PHPMailer autoloader
        // require 'vendor/autoload.php';

        // Create a new PHPMailer instance
        $this->mailer = new PHPMailer(true);

        // Set up SMTP
        $this->mailer->isSMTP();
        $this->mailer->Host = $_ENV['MAIL_SERVER'];
        $this->mailer->SMTPAuth = true;
        $this->mailer->Username = $_ENV['MAIL_USERNAME'];
        $this->mailer->Password = $_ENV['MAIL_PASSWORD'];
        $this->mailer->SMTPSecure = 'tls';
        $this->mailer->Port = $_ENV['MAIL_PORT'];

        // Set the sender email address and name
        $this->mailer->setFrom('your_email@example.com', 'Your Name');
    }

    public function sendEmail($to, $subject, $body) {
        try {
            // Add a recipient
            $this->mailer->addAddress($to);

            // Set the subject
            $this->mailer->Subject = $subject;

            // Set the email body
            $this->mailer->Body = $body;

            // Send the email
            $this->mailer->send();

            // Clear recipients and attachments for the next iteration
            $this->mailer->clearAddresses();
            $this->mailer->clearAttachments();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Example usage:
// try {
//     $emailSender = new EmailSender();

//     $to = 'recipient@example.com';
//     $subject = 'Test Email';
//     $body = 'This is a test email sent using PHPMailer.';

//     if ($emailSender->sendEmail($to, $subject, $body)) {
//         echo 'Email sent successfully.';
//     } else {
//         echo 'Error sending email.';
//     }
// } catch (Exception $e) {
//     echo 'Exception caught: ' . $e->getMessage();
// }