<?php
/** @var $model DBModel */
/** @var $task TaskResult */

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


require './backupfiles/Exception.php';
require './backupfiles/PHPMailer.php';
require './backupfiles/SMTP.php';


function insert_table($total= 5){

  $task = new TaskResult();

  $model = new DBModel();
  $task->addLog("Setting table");
  $model->table = "all_tasks";

  for($i =0; $i<= $total; $i++){
    sleep(3);
    $task->addLog("Inserting record");
    $model->insertRecord([
      'task' => 'Task  #' . $i
    ]);
  }
  $task->addLog("Closing task");
  $task->close();
  return $task;

}



function insert_contact($name, $email,$phone){
  $task = new TaskResult();
  sleep(5);
  $model = new DBModel();
  $model->table = "contacts";
  $model->insertRecord([
    'name' => $name,
    'email' => $email,
    'phone' => $phone
  ]);
  $task->close();
  return $task;
}


function make_error(){
  return "test this";
}


function send_a_email($subject, $body, $to, $from){
  $task = new TaskResult();
  if($to && $subject){
    $mail = new PHPMailer(true);
    try {

      $task->addLog('Configuration the mail object');

      //Server settings
      $mail->isSMTP();
      $mail->Host       = 'smtp.mailtrap.io';
      $mail->SMTPAuth   = true;
      $mail->Username   = '521491cb082195';
      $mail->Password   = 'c96e8ce29aa87c';
      $mail->Port       = 2525;

      //Recipients
      $mail->setFrom($from, 'wfTutorials');
      $mail->addAddress($to, $to);     // Name is optional
      $mail->addReplyTo($from, 'wfTutorials');


      // Content
      $mail->isHTML(true);
      $mail->Subject =  $subject;
      $mail->Body    = $body;
      $mail->AltBody = $body;

      $task->addLog('Sending the message');
      $mail->send();
      $task->addLog("Message successfully sent");

      // close task
      $task->close();
      return $task;
    } catch (Exception $e) {
      $task->addLog('Message failed');
      $task->addLog("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
      return $task;
    }
  }
}
