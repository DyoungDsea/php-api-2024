<?php

require __DIR__ . '/PHPMailer/src/Exception.php';
require __DIR__ . '/PHPMailer/src/PHPMailer.php';
require __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;


class EmailSender
{
    public static function sendEmail($toEmail, $toName, $subject, $bodyhtml, $mailTemplate)
    {
        $mail = new PHPMailer(true);


        $swap = array(
            "{SITE_ADDR}" => SITELINK,
            "{SITETITLE}" => SITETITLE,
            "{ICON}" => SITEICON,
            "{LOGO}" => SITELOGO,
            "{TEMPLATE}" => $mailTemplate,
        );



        //create the html message
        if (file_exists($bodyhtml)) {
            $message = file_get_contents($bodyhtml);
        } else {
            die("Unable to locate file");
        }


        foreach (array_keys($swap) as $key) {
            if (strlen($key) > 2 && trim($key) != '') {
                $body = str_replace($key, $swap[$key], $message);
            }
        }


        try {
            // Server settings
            $mail->SMTPDebug = 0;                      // Enable verbose debug output
            $mail->isSMTP();                           // Set mailer to use SMTP
            $mail->Host       = 'smtp.gmail.com';      // Specify main and backup SMTP servers
            $mail->SMTPAuth   = true;                  // Enable SMTP authentication
            $mail->Username   = 'youngelefiku23@gmail.com';    // SMTP username
            $mail->Password   = 'htxakstnhqxlbdgz';    // SMTP password
            $mail->SMTPSecure = 'tls';                 // Enable TLS encryption, `ssl` also accepted
            $mail->Port       = 587;                   // TCP port to connect to

            // Sender information
            $mail->setFrom('support@samogoza.com', 'Samogoza');
            $mail->addReplyTo('support@samogoza.com', 'Samogoza');

            // Recipient
            $mail->addAddress($toEmail, $toName);      // Add a recipient

            // Content
            $mail->isHTML(true);                       // Set email format to HTML
            $mail->Subject = $subject;
            $mail->Body    = $body;
            // $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

            $mail->send();
           return true;
        } catch (Exception $e) {
            return false;
        }
    }
}

// Usage example
// EmailSender::sendEmail('youngelefiku@gmail.com', 'Joe User', 'Here is the subject', 'This is the HTML message body <b>in bold!</b>');
