<?php
include 'functions.php';

if(isset($_GET['song']) && !empty($_GET['song'])){
    $songId = $_GET['song'];
    $songData = get_song($songId);
}else{
    echo "No song found"; exit;
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Start Playing Song</title>
</head>
<body>
<h3>Start Song</h3>
<a>Back to songs</a>
<p style="text-align: center; font-size: 55px;">
    <span>"<?Php echo $songData["name"];?>"</span>
    <br>
    <span><?php echo $songData['artist'];?></span>
    <br>
    <span><?php echo $songData['length'];?>, mins</span>
    <br>
    <a href="<?Php echo get_next_url($songId, 0);?>">Start</a>
</p>
</body>
</html>
