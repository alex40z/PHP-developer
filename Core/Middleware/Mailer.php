<?php

namespace App\Core\Middleware;

use App\Core\Config\Config;
use App\Core\Exceptions\WrongRequestException;
use Exception;
use PHPMailer\PHPMailer\PHPMailer;

class Mailer
{
    public function sendMail(string $email, string $subject, string $message): void
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new WrongRequestException("Некорректный e-mail ($email).");
        }
        $config = new Config();
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = $config->configParam('mail_host');
        $mail->SMTPAuth = true;
        $mail->Username = $config->configParam('mail_username');
        $mail->Password = $config->configParam('mail_password');
        $mail->SMTPSecure = $config->configParam('mail_smtpsecure');;
        $mail->Port = $config->configParam('mail_port');
        $mail->setFrom($config->configParam('mail_from'));
        $mail->addAddress($email);
        $mail->CharSet = "utf-8";
        $mail->Subject = $subject;
        $mail->msgHTML($message);
        if (!$mail->send()) {
            throw new Exception("Ошибка отправки письма ($mail->ErrorInfo).");
        }
    }
}
