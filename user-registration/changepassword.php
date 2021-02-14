<?php
include 'user_authentication_functions.php';

auth();

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change-password'])) {

    $oldPassword = isset($_POST['oldpassword']) ? $_POST['oldpassword'] : null;
    $password = isset($_POST['password']) ? $_POST['password'] : null;
    $repeatPassword = isset($_POST['repeatpassword']) ? $_POST['repeatpassword'] : null;

    $userdata = getUserData(getUserId());
    if($userdata){
      if (password_verify($oldPassword, $userdata["password"])) {
          // old password is correct.
        if($password !== $repeatPassword){
            die("You passwords are not the same");
        }else{
            if(updateUserPassword(getUserId(), $password)){
              echo "password updated";
            }
        }
      }
    }
}
?>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Change you Password</title>
  <link rel="stylesheet" href="forms.css"/>
</head>
<body>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <ul class="form-style-1">
    <h3>Change your password</h3>
    <li><label>Old Password:</label>
      <input type="password" name="oldpassword"/>
    </li>
    <li>
      <label>New Password:</label>
      <input type="password" name="password"/>
    </li>
    <li>
      <label>Repeat Password:</label>
      <input type="password" name="repeatpassword"/><br>
    </li>
    <br>
    <input type="submit" name="change-password" value="Reset Password" />
    <br>
    <a href="profile.php">Back to profile</a>
    or
    <a href="logout.php">Logout</a>
  </ul>
</form>
</body>
</html>

