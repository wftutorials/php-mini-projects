<?php
include 'user_authentication_functions.php';


if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['user-register'])){

  $username = isset($_POST['username']) ? $_POST['username'] : null;
  $email = isset($_POST['email']) ? $_POST['email'] : null;
  $password = isset($_POST['password']) ? $_POST['password'] : null;
  $repeatPassword = isset($_POST['repeatpassword']) ? $_POST['repeatpassword'] : null;
  try {
    save_user($username, $email, $password, $repeatPassword);
    send_register_email("Welcome " . $username. ", to the platform. ", $email);
    echo "User Saved";
  }catch (Exception $e){
    die($e->getMessage());
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Registration</title>
  <link rel="stylesheet" href="forms.css"/>
</head>
<body>
<form method="post" action="<?php echo $_SERVER['PHP_SELF'];?>">
  <ul class="form-style-1">
    <h3>User Registration</h3>
    <li>
      <label>Username:</label>
      <input type="text" name="username" autocomplete="off"/><br>
    </li>
    <li>
      <label>Email: </label>
      <input type="text" name="email" autocomplete="off"/><br>
    </li>
    <li>
      <label>Password: </label>
      <input type="password" name="password"/><br>
    </li>
    <li>
      <label>Repeat Password:</label>
      <input type="password" name="repeatpassword"/><br>
    </li>
    <br>
    <input type="submit" name="user-register" value="Register" /><br>
    Already have an account <a href="login.php">login here</a>
  </ul>
</form>

</body>
</html>
