<?php
include 'functions.php';

if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['save-song'])){
    $song = isset($_POST['song']) ? $_POST['song'] : null;
    $length = isset($_POST['length']) ? $_POST['length'] : null;
    $artist = isset($_POST['artist']) ? $_POST['artist'] : null;
    if($song && $length && $artist){
        $id = save_song($song, $length, $artist);
        echo "Song Saved: " . $id;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add new songs</title>
</head>
<body>
<h3>Add new songs</h3>
<form method="post">
    Song Name: <input type="text" name="song"/><br>
    Artist: <input type="text" name="artist"/><br>
    Length: <input type="text" name="length"/><br>
    <button type="submit" name="save-song">Save song</button>
</form>

<h3>All Songs</h3>
<?php foreach (get_all_songs() as $song):?>
    <p style="border: 1px solid #ccc; border-radius: 3px; padding: 5px; margin-bottom: 3px;">
        Name: <?php echo $song["name"];?><br>
        Artist: <?php echo $song["artist"];?> <br>
        Length: <?php echo $song["length"];?><br>
        <a target="_blank" href="lyrics.php?song=<?php echo $song['id'];?>">Add Lyrics</a>
        &nbsp;&nbsp;
        <a target="_blank" href="start.php?song=<?php echo $song['id'];?>">Play</a>
    </p>
<?Php endforeach;?>

</body>
</html>
