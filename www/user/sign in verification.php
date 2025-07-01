<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/../../vendor/autoload.php';

function sendVerification($toEmail, $code) {
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();
        $mail->Host       = 'smtp.gmail.com';
        $mail->SMTPAuth   = true;
        $mail->Username   = 'faqcoding@gmail.com';
        $mail->Password   = 'vxsw jgfu omqg nfgh';
        $mail->SMTPSecure = 'tls';
        $mail->Port       = 587;

        $mail->setFrom('faqcoding@gmail.com', 'Coding FAQ');
        $mail->addAddress($toEmail);

        $mail->Subject = 'CODING FAQ VERIFICATION';
        $mail->Body    = "Votre code est : $code";

        $mail->send();
        return true;
    } catch (Exception $e) {
        return "Erreur : le mail n'a pas pu être envoyé. $mail->ErrorInfo";
    }
}
?>