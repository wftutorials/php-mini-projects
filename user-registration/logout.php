<?php
include 'user_authentication_functions.php';

startASession();

session_unset();
session_destroy();

header("Location: login.php");
