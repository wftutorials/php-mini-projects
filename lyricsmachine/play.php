<?php
include 'functions.php';

if(isset($_GET['song']) && !empty($_GET['song'])){
    $songId = $_GET['song'];
    $songData = get_song($songId);
    $current = isset($_GET['current']) ? $_GET['current'] : 0;
    $lyric = get_current_lyric($current);
}else{
    echo "No song found"; exit;
}

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['font'])){
    $action =$_POST['font'];
    if($action == "increase"){
        $fs = getFontSize();
        setFontSize($fs + 5);
    }else if($action == "decrease"){
        $fs = getFontSize();
        setFontSize($fs - 5);
    }

}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sing Along</title>
</head>
<body>
<h3><?php echo $songData["name"];?></h3>
<a href="<?php echo get_previous_url($songId, $current);?>">Previous</a>
<a href="<?php echo get_next_url($songId, $current);?>">Next</a>
<br><br>
<form method="post">
    <button type="submit" name="font" value="increase">Increase font Size</button>&nbsp;
    <button type="submit" name="font" value="decrease">Decrease Font size</button>
</form>
<p style="font-size: <?php echo getStoredFontSize()?>; border: 1px solid #ccc; border-radius: 3px; padding: 5px; margin-bottom: 20px;">
    <?php echo nl2br($lyric["content"]);?>
</p>

<a href="<?php echo get_previous_url($songId, $current);?>">Previous</a>
<a href="<?php echo get_next_url($songId, $current);?>">Next</a>
</body>
</html>

