<?php

function sendMail(string $address, string $subject, string $body): void
{
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = config("mail.host");
    $mail->SMTPAuth = true;
    $mail->Username = config("mail.username");
    $mail->Password = config("mail.password");
    $mail->Port = config("mail.port");
    $mail->CharSet = "UTF-8";

    $mail->setFrom(config("mail.from_address"), config("mail.from_name"));
    $mail->addAddress($address);

    $mail->isHTML();
    $mail->Subject = $subject;
    $mail->Body = $body;

    $mail->send();
}