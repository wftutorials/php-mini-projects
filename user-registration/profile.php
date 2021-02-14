<?php
include 'user_authentication_functions.php';

auth();

if(isUserLoggedIn()){
  $user = getUserData(getUserId());

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>User Profile</title>
  <link rel="stylesheet" href="forms.css"/>
</head>
<body>
<ul class="form-style-1">
  <h1>User Profile</h1>
  <h3><?php echo strtoupper($user["name"]);?></h3>
  <p>Email: <?php echo $user["email"];?></p>
  <p>Created At: <?php echo $user["created_at"];?></p>
  <a href="changepassword.php">Change Password</a>
  <hr>
  <a href="logout.php">Logout me out</a>
</ul>

</body>
</html>
