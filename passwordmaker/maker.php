<?php
include 'PasswordCreator.php';

$modes = ["easy","medium","hard"];

$difficulty = "medium";
$limit = 8;
$password = "Your password here";
$special = false;

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['gen-pass'])) {

    $difficulty = isset($_POST['mode']) ? $_POST['mode'] : "easy";
    $limit = isset($_POST['limit']) ? $_POST['limit'] : 8;
    $special = isset($_POST['special']) ? $_POST['special'] : null;
    $passwordCreator = new PasswordCreator($difficulty, $limit);
    if($special){
        $passwordCreator->setSpecialCharacters(true);
    }
    $password = $passwordCreator->generate(); // generate password here

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Password</title>
</head>
<body>
<h3>Password Maker</h3>
<form method="post">
    Mode: <select name="mode">
        <?Php foreach($modes as $mode):?>
            <?php if($mode == $difficulty):?>
                <option value="<?php echo $mode;?>" selected="selected"><?php echo strtoupper($mode);?></option>
            <?php else:?>
                <option value="<?php echo $mode;?>"><?php echo strtoupper($mode);?></option>
            <?php endif;?>
        <?php endforeach;?>
    </select>
    <br>
    Length: <input name="limit" type="number" style="width:50px;" value="<?php echo $limit;?>"/>
    <br>
    <?php if($special):?>
        <input type="checkbox" name="special" checked="checked"/> Special Characters
    <?Php else:?>
        <input type="checkbox" name="special"/> Special Characters
    <?php endif;?>
    <br><br>
    <button type="submit" name="gen-pass">Generate Password</button>
</form>
<h3>Your Password</h3>
<p style="margin:0px; padding: 5px; border: 1px solid #ccc; font-size: 50px;"><?php echo $password;?></p>
</body>
</html>
