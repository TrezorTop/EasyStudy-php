<?php

require '../classes/PHPMailer/PHPMailer.php';
require '../classes/PHPMailer/SMTP.php';
require '../classes/PHPMailer/Exception.php';

class Mail
{

    public static function sendMail($subject, $body, $address)
    {
        $mail = new PHPMailer\PHPMailer\PHPMailer();
        $mail->isSMTP();
        $mail->SMTPAuth = true;
        $mail->SMTPSecure = 'ssl';
        $mail->Host = 'smtp.gmail.com';
        $mail->Port = '465';
        $mail->isHTML();
        $mail->Username = 'easystudy.socialnetwork@gmail.com';
        $mail->Password = 'TrezorTop92';
        $mail->setFrom('no-reply@easystudy.socialnetwork.com');
        $mail->Subject = $subject;
        $mail->Subject = '=?UTF-8?B?'.base64_encode($subject).'?=';;
        $mail->Body = $body;
        $mail->addAddress($address);

        $mail->send();
    }

}