<?php
include 'functions.php';

$lyricId = "";
$order = "";
$content ="";


if(isset($_GET['song']) && !empty($_GET['song'])){
    $id = $_GET['song'];
}else{
    echo "Need to know song"; exit;
}

if(isset($_GET['lyric']) && !empty($_GET['lyric'])){
    $lyricId = $_GET['lyric'];
    $lyricsData = get_lyric($lyricId);
    $content = $lyricsData["content"];
    $order = $lyricsData['priority'];
}

if(isset($_GET['action']) && $_GET['action'] =='delete'){
    if($lyricId){
        delete_song_lyric($lyricId);
        redirectPage("lyrics.php?song=".$id);
    }
}


if($_SERVER['REQUEST_METHOD'] == "POST" && isset($_POST['save-lyric'])){

    $order = isset($_POST['order']) ? $_POST['order'] : 0;
    $content = isset($_POST['content']) ? $_POST['content'] : null;

    if( $content){
        $lyricId = save_lyric($id, $content, $order, $lyricId);
        $lyricId = '';
        $content = '';
        $order = '';
    }

}


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Song</title>
</head>
<body>
<h3>Add Song Lyrics</h3>
<a href="lyrics.php?song=<?php echo $id;?>">Add new lyrics</a>
<form method="post">
    <input type="hidden" name="song" value="<?php echo $id;?>"  />
    <input type="hidden" name="lyric" value="<?php echo $lyricId;?>"/>
    Order: <input type="number" name="order" value="<?php echo $order;?>"/><br>
    Song Content:<br>
    <textarea name="content" cols="55" rows="12"><?php echo $content;?></textarea>
    <br>
    <button type="submit" name="save-lyric">Save Lyric</button>
</form>
<h3>Lyrics</h3>
<?Php foreach (get_all_lyrics($id) as $lyric):?>
    <p style="border: 1px solid #ccc; border-radius: 3px; padding:5px; margin-bottom: 3px;">
        <?php echo $lyric["content"];?>
        <br>
        <a href="lyrics.php?song=<?php echo $id;?>&lyric=<?php echo $lyric["id"];?>">Edit</a>
        | <a href="lyrics.php?song=<?php echo $id;?>&lyric=<?php echo $lyric["id"];?>&action=delete">Delete</a>
    </p>
<?php endforeach; ?>
</body>
</html>
