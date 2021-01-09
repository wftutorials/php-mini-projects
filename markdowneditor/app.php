<?php

$dir = __DIR__ . '/files';
$currentContent = "";
$currentFile = "";

function clean_string($string){
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

function get_files_listing($dir){
    $files = [];
    if(is_dir($dir)){
        if($dh = opendir($dir)){
            while(($file = readdir($dh)) !== false){
                if(!in_array($file,['.','..'])){
                    $files[] = $file;
                }
            }
            closedir($dh);
        }
    }
    return $files;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save-file'])){

    $filename = isset($_POST['file']) ? $_POST['file'] : null;
    $content = isset($_POST['content']) ? $_POST['content'] : null;
    $filename = clean_string($filename);
    $myfile = fopen($dir. DIRECTORY_SEPARATOR . $filename.'.md','w');
    $currentFile = $filename;
    $currentContent = $content;
    fwrite($myfile, $content);
    fclose($myfile);

}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['view-file'])){
    $selectedfile = isset($_POST['selectedfile']) ? $_POST['selectedfile'] : null;
    if($selectedfile){
        $currentFile = pathinfo($selectedfile, PATHINFO_FILENAME);
        $myfile = file_get_contents($dir.DIRECTORY_SEPARATOR.$selectedfile);
        $currentContent = $myfile;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Markdown Editor</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.css">
    <script src="https://cdn.jsdelivr.net/simplemde/latest/simplemde.min.js"></script>
</head>
<style>
    body {
        background: #CCCCCC;
    }
    h3{
        text-align: center;
    }
    .container {
        width: 650px;
        margin: 0 auto;
        background: #f6f6f6;
        border: 1px solid #2C3E50;
        border-radius: 3px;
        padding: 5px;
    }
</style>
<body>
<h3>Markdown Editor</h3>
<div class="container">
<form method="post">
    View files: <select name="selectedfile">
        <option value="">--Select a file--</option>
        <?php foreach (get_files_listing($dir) as $file):?>
            <option value="<?php echo $file;?>"><?php echo $file;?></option>
        <?php endforeach;?>
    </select>
    <button type="submit" name="view-file">Go</button>
    <br>
    <br>
    New File : <input type="text" value="<?php echo $currentFile;?>" name="file" autocomplete="off"/><br>
    <br>
    <textarea id="file-input" cols="66" rows="15" name="content"><?php echo $currentContent;?></textarea><br>
    <button type="submit" name="save-file">Save Changes</button>
</form>
</div>
<script>
    var simplemde = new SimpleMDE({ element: document.getElementById("file-input") });
</script>

</body>
</html>
