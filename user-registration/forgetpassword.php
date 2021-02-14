<?php
include 'user_authentication_functions.php';

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['forget-password'])){

  $email = isset($_POST['email']) ? $_POST['email'] : null;
  if($email){
      $user = getUserByEmail($email);
      if(count($user) > 0 ){
        $vCode = get_random_code();
        saveUserVerificationCode($user["id"], $vCode);
        $verificationUrl = "http://$_SERVER[HTTP_HOST]"
          ."/passwordreset.php"."?v=". $vCode;
        send_verification_email($verificationUrl,$email,$user["name"]);
        echo "Verification email sent";
      }else{
        echo "No user found";
      }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Forget Password</title>
  <link rel="stylesheet" href="forms.css"/>
</head>
<body>
<form method="post">
  <ul class="form-style-1">
    <h3>Did you forget your password</h3>
    <li>
      Enter you email address: <input name="email" type="text" autocomplete="off"/>
    </li>
    <br>
    <input type="submit" name="forget-password" value="Reset my password"/>
    <br>
    <a href="login.php">Back to login</a>
  </ul>
</form>

</body>
</html>

