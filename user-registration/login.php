<?php
// https://www.sanwebe.com/2014/08/css-html-forms-designs
include 'user_authentication_functions.php';

if(isUserLoggedIn()){
  header("Location: profile.php");
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user-login'])){

  $email = isset($_POST['email']) ? $_POST['email'] : null;
  $password = isset($_POST['password']) ? $_POST['password'] : null;
  if($email && $password){
    try{
      if(login_user($email, $password)){
        header("Location: profile.php");
        echo "user logged in";
      }else{
        echo "Cannot login user";
      }
    }catch (Exception $e){
        die($e->getMessage());
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Login</title>
  <link rel="stylesheet" href="forms.css"/>
</head>
<body>
<form autocomplete="false" method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
  <ul class="form-style-1">
    <h3>User Login</h3>
    <li>
      <label>Email<span class="required">*</span></label>
      <input type="text" name="email" autocomplete="off"/><br>
    </li>
    <li>
      <label>Password<span class="required">*</span></label>
      <input type="password" name="password" autocomplete="no"/>
    </li>
    <br>
  <input type="submit" name="user-login" value="Login"/>
    <br>

    No Account <a href="register.php">Register here</a>

    <br>

    <a href="forgetpassword.php">Forgot Password</a>
  </ul>
</form>
</body>
</html>

