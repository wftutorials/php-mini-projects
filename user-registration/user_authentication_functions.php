<?php
// https://alexwebdevelop.com/php-password-hashing/

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;


include 'Exception.php';
include "PHPMailer.php";
include "SMTP.php";


function get_connection(){
  $dsn = "mysql:host=localhost;dbname=wftutorials";
  $user = "root";
  $passwd = "";
  $conn = new PDO($dsn, $user, $passwd);
  return $conn;
}


function save_user($username, $email, $password, $repeatpassword){
  if(!($username && $email && $password)){
    throw new Exception("Missing important inputs", 401);
  }
  if($password !== $repeatpassword){
    throw new Exception("Passwords do not match", 401);
  }
  $password = password_hash($password, PASSWORD_DEFAULT);
  $createdAt = date("Y-m-d h:i:s");
  $conn = get_connection();
  $sql = "INSERT INTO users(`name`,`email`, `password`,`created_at`) VALUES (?,?,?,?)"; // create sql
  $query = $conn->prepare($sql); // prepare
  $query->execute([$username, $email, $password, $createdAt]); // execute
  return $conn->lastInsertId();
}


function login_user($email, $password){
  $conn = get_connection();
  $sql = "SELECT * from users WHERE email=? LIMIT 1";
  $query = $conn->prepare($sql);
  $query->execute([$email]);
  $results = $query->fetchAll();
  if(count($results) <= 0){
    throw new Exception("No users found", 401);
  }
  if(isset($results[0])){
    $user = $results[0];
    if (password_verify($password, $user["password"])) {
      startASession();
      $_SESSION["username"] = $user["name"];
      $_SESSION["userid"] = $user["id"];
      return true;
    }else{
      return false;
    }
  }
}

function getUserData($id){
  $conn = get_connection();
  $sql = "SELECT * from users WHERE id=? LIMIT 1";
  $query = $conn->prepare($sql);
  $query->execute([$id]);
  $results = $query->fetchAll();
  if(count($results) > 0){
    return $results[0];
  }
  return [];
}

function getUserByEmail($email){
  $conn = get_connection();
  $sql = "SELECT * from users WHERE email=? LIMIT 1";
  $query = $conn->prepare($sql);
  $query->execute([$email]);
  $results = $query->fetchAll();
  if(count($results) > 0){
    return $results[0];
  }
  return [];
}

function getUserByCode($code){
  $conn = get_connection();
  $sql = "SELECT * from users WHERE remember_token=? LIMIT 1";
  $query = $conn->prepare($sql);
  $query->execute([$code]);
  $results = $query->fetchAll();
  if(count($results) > 0){
    return $results[0];
  }
  return [];
}

function updateUserPassword($id, $password){
  $password = password_hash($password, PASSWORD_DEFAULT);
  $conn = get_connection();
  $sql = "UPDATE users SET `password`=? WHERE id=?";
  $query = $conn->prepare($sql);
  $query->execute([$password, $id]);
  return true;
}

function saveUserVerificationCode($id, $code){
  $conn = get_connection();
  $sql = "UPDATE users SET `remember_token`=? WHERE id=?";
  $query = $conn->prepare($sql);
  $query->execute([$code, $id]);
  return true;
}

function resetPasswordByCode($code, $password){
  $password = password_hash($password, PASSWORD_DEFAULT);
    $user = getUserByCode($code);
    if(count($user) > 0){
      $conn = get_connection();
      $sql = "UPDATE users SET `password`=? WHERE id=?";
      $query = $conn->prepare($sql);
      $query->execute([$password, $user['id']]);
      echo "Password Successfully Reset";
      echo "<a href='login.php'>Login now</a>";
    }
}

function isUserLoggedIn(){
  startASession();
  if(isset($_SESSION["username"]) && $_SESSION["userid"]){
    return true;
  }
  return false;
}

function auth(){
  if(!isUserLoggedIn()){
    header("Location: login.php");
  }
}


function getUserId(){
  startASession();
  if($_SESSION["userid"]){
    return $_SESSION["userid"];
  }
  return null;
}

function get_random_code($num=22){
  $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
  $string = '';
  $max = strlen($characters) - 1;
  for ($i = 0; $i < $num; $i++) {
    $string .= $characters[mt_rand(0, $max)];
  }
  return $string;
}


function startASession(){
  if (session_status() == PHP_SESSION_NONE) {
    session_start();
  }
}

function send_register_email($msg, $email, $subject="Register user"){
  send_mail($subject, $msg, $email);
}


function send_verification_email($code, $email, $name){
  $message =  '<h1>Dear '.$name.',</h1>';
  $message .= '<p>To change your password go to the link here:</p>';
  $message .= '<a href="'.$code.'">'.$code.'</a>';
  send_mail("Verification Email", $message, $email);
}


function send_mail($subject, $body, $address){
  $mail = new PHPMailer(true);

  try {
    //Server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.mailtrap.io';
    $mail->SMTPAuth   = true;
    $mail->Username   = '';
    $mail->Password   = '';
    $mail->Port       = 2525;

    //Recipients
    $mail->setFrom("no-reply@wftutorials.com", 'wfTutorials');
    $mail->addAddress($address, $address);     // Name is optional
    $mail->addReplyTo('no-reply@wftutorials.com', 'wfTutorials');


    // Content
    $mail->isHTML(true);
    $mail->Subject = $subject;
    $mail->Body    = $body;
    $mail->send();
    echo 'Message has been sent';
  } catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
  }
}
