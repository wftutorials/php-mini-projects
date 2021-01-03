<?php
include 'functions.php';

$id = isset($_GET['id']) ? $_GET['id'] : null;

if($id){
    $product = get_product($id);
    $personalMessage = "";
    include "email_template.php";
}
