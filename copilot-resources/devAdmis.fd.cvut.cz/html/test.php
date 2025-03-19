<?php
/**
 * Created by PhpStorm.
 * User: ondrac
 * Date: 25.07.2018
 * Time: 10:44
 */
require_once __DIR__."/conf/config.inc";
require_once SYSTEMINCLUDES."function.php";
//require_once SYSTEMINCLUDES."authenticateUser.php";
//require_once SYSTEMINCLUDES . "autoLoader.php";
//overUzivatele($pristup_zakazan);

/*use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;*/

//Load Composer's autoloader
/*
//Create an instance; passing `true` enables exceptions
$mail = new PHPMailer(true);

try {
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'mailgwfd.cvut.cz';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = false;                                   //Enable SMTP authentication     //SMTP username
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;            //Enable implicit TLS encryption
    $mail->Port       = 25;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom('admis@fd.cvut.cz', 'Služebíček Admis');
    $mail->addAddress('hnykpetr@fd.cvut.cz', 'Petr Hnyk');     //Add a recipient
    $mail->addAddress('ondra@fd.cvut.cz');               //Name is optional
    $mail->addReplyTo('admis@fd.cvut.cz', 'Information');


    //Content
    $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = 'Here is the subject';
    $mail->Body    = 'This is the HTML message body <b>in bold!</b> jůůůůů';
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->send();
    echo 'Message has been sent';
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
*/
 geteZakStatus();