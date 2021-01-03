<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


include 'functions.php';
include 'Exception.php';
include "PHPMailer.php";
include "SMTP.php";


$id = isset($_GET['id']) ? $_GET['id'] : null;

if($id){
    $product = get_product($id);
    $personalMessage = "";
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['send-message'])){

    $email = isset($_POST['email']) ? $_POST['email'] : null;
    $subject = isset($_POST['subject']) ? $_POST['subject'] : null;
    $personalMessage = isset($_POST['personal']) ? $_POST['personal'] : null;
    if($email && $subject){

        if(!$product){
            $product = get_product($id);
        }
        ob_start();
        include 'email_template.php';
        $msg = ob_get_clean();

        $mail = new PHPMailer(true);

        try {
            //Server settings
            $mail->isSMTP();
            $mail->Host       = 'smtp.mailtrap.io';
            $mail->SMTPAuth   = true;
            $mail->Username   = '33486b9de38bf6';
            $mail->Password   = 'c518b03b831dfb';
            $mail->Port       = 2525;

            //Recipients
            $mail->setFrom("christmaslist@wftutorials.com", 'wfTutorials');
            $mail->addAddress($email, $email);     // Name is optional
            $mail->addReplyTo('email@wftutorials.com', 'wfTutorials');


            // Content
            $mail->isHTML(true);
            $mail->Subject = $subject;
            $mail->Body    = $msg;
            $mail->AltBody = 'My Shopping lists';

            $mail->send();
            echo 'Message has been sent';
        } catch (Exception $e) {
            echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        }

    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Share your wish list</title>
</head>
<body>
<h3>Share your wish list via email</h3>
<form  method="post">
 Send To: <input type="text" name="email" autocomplete="off"/><br>
Subject: <input type="text" name="subject" autocomplete="off"/><br>
Personal Messsage: <input type="text" name="personal" autocomplete="off"/><br>
    <iframe height="500" width="500" src="email_preview.php?id=<?php echo $id;?>"></iframe><br>
    <button type="submit" name="send-message">Send Message</button>
</form>
<div style="margin-bottom: 100px;"></div>
</body>
</html>
