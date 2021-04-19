<?php
// https://stackoverflow.com/questions/4914750/how-to-zip-a-whole-folder-using-php
// https://davidwalsh.name/create-zip-php
// https://www.tutorialrepublic.com/php-tutorial/php-file-download.php
// https://blog.logrocket.com/how-to-style-forms-with-css-a-beginners-guide/
// https://stackoverflow.com/questions/39123986/how-to-fix-invalid-file-error-after-downlading-zip-file-using-php

$basedir = 'C:\Users\wfranklin\Documents\Repos\backups';

function get_connection(){
  $dsn = "mysql:host=localhost;dbname=wftutorials";
  $user = "root";
  $passwd = "";
  $conn = new PDO($dsn, $user, $passwd);
  return $conn;
}

function save_backup($project, $location, $comment, $backup){
  $conn = get_connection();
  $sql = "INSERT INTO backups(`project`,`project_path`,`comment`,`backup_path`) VALUES (?,?,?,?)"; // create sql
  $query = $conn->prepare($sql); // prepare
  $query->execute([$project, $location, $comment, $backup]); // execute
  return $conn->lastInsertId();
}

function get_saved_backup($id){
  $results = [];
  try {
    $conn = get_connection();
    $query = $conn->prepare("SELECT * from backups WHERE id=? LIMIT 1");
    $query->execute([$id]);
    $results = $query->fetchAll();
    if(isset($results[0])){
      $results = $results[0];
    }else{
      $results = [];
    }
  }catch (Exception $e){

  }
  return $results;
}

function get_all_backups(){
  $results = [];
  try {
    $conn = get_connection();
    $results = $conn->query("SELECT * from backups order by created_at desc");
  }catch (Exception $e){

  }
  return $results;
}

function get_unique_backups(){
  $results = [];
  try {
    $conn = get_connection();
    $results = $conn->query("SELECT id, project, project_path from backups group by project order by created_at desc");
  }catch (Exception $e){

  }
  return $results;
}

if(isset($_GET['download'])){
  $id = $_GET['download'];
  $currentBackup = get_saved_backup($id);
  $file = $currentBackup["backup_path"];
  if(file_exists($file)) {
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="'.basename($file).'"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    ob_clean();
    readfile($file);
    die();
  }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['save-backup'])){

  $path = isset($_POST['path']) ? $_POST['path'] : null;
  $comments = isset($_POST['comment']) ? $_POST['comment'] : null;
  $project = isset($_POST['project']) ? $_POST['project'] : null;
  $backup = isset($_POST['backup']) ? $_POST['backup'] : null;
  if($backup){
    $currentBackup = get_saved_backup($backup);
    $path = $currentBackup["project_path"];
    $project = $currentBackup["project"];
  }

  $dir = $path;
  $zipcreated =  $basedir . DIRECTORY_SEPARATOR . "backup".time().".zip";
  $files = new RecursiveIteratorIterator(
    new RecursiveDirectoryIterator($path),
    RecursiveIteratorIterator::LEAVES_ONLY
  );
  $zip = new ZipArchive();
  $zip->open($zipcreated, ZipArchive::CREATE | ZipArchive::OVERWRITE);
  foreach ($files as $name => $file)
  {
    // Skip directories (they would be added automatically)
    if (!$file->isDir())
    {
      // Get real and relative path for current file
      $filePath = $file->getRealPath();
      $relativePath = substr($filePath, strlen($path) + 1);

      // Add current file to archive
      $zip->addFile($filePath, $relativePath);
    }
  }
  $zip ->close();
  save_backup($project, $path, $comments, $zipcreated);


}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Backup App</title>
</head>
<style>
  body {
    background: #CCCCCC;
    font-family: Arial, Helvetica, sans-serif;
  }
  .form-container {
    background: whitesmoke;
    border: 1px solid deepskyblue;
    padding: 20px;
    width:40%;
    margin: 0 auto;
    border-radius: 3px;
  }

  .form-container  form {
    margin: 0 auto;
  }

  .backup-listing {
    margin: 0 auto;
    background: whitesmoke;
    border: 1px solid deepskyblue;
    width:40%;
    padding: 20px;
    margin-top: 20px;
  }

  .backup-listing-container{
    overflow: auto;
    height: 250px;
  }

  .backup-listing-item{
    margin-bottom: 3px;
    padding: 5px;
    box-shadow:4px 4px 10px rgba(0,0,0,0.06);
  }

  .form-container input {
    width:200px;
    padding:5px;
    margin:3px 0;
    border-radius:3px;
    box-shadow:4px 4px 10px rgba(0,0,0,0.06);
  }

  .form-container textarea {
    width:90%;
    resize: vertical;
    padding:15px;
    border-radius:15px;
    border:0;
    box-shadow:4px 4px 10px rgba(0,0,0,0.06);
    height:150px;
  }

  .form-container select {
    padding:10px;
    border-radius:10px;
  }

  .form-container button {
    appearance:none;
    -webkit-appearance:none;
    cursor: pointer;
    /* usual styles */
    padding:10px;
    border:none;
    background-color:#3F51B5;
    color:#fff;
    font-weight:600;
    border-radius:5px;
    width:100%;
  }
</style>
<body>
<h2 style="text-align: center;">Backup App</h2>
<div class="form-container">
  <h3 >Add a backup</h3>
  <form method="post">
    Choose a previous backup: <select name="backup">
      <option value="">--select backup--</option>
      <?php foreach (get_unique_backups() as $bk):?>
        <option title="<?php echo $bk["project_path"];?>" value="<?php echo $bk["id"];?>"><?php echo $bk["project"];?></option>
      <?Php endforeach;?>
    </select> OR
    <hr>
    New Project: <input type="text" name="project" placeholder="Project Name" autocomplete="off"/>
    <br>
    Project Path: <input type="text" name="path" placeholder="Project Path"/><br><br>
    Add Comment: <br>
    <textarea cols="35" rows="7" type="text" name="comment" placeholder="Comment"></textarea><br>
    <button name="save-backup">Save Backup</button>

  </form>
</div>

<div class="backup-listing">
  <h3>Listing of Backups</h3>
  <div class="backup-listing-container">
    <?php foreach (get_all_backups() as $bk):?>
      <div class="backup-listing-item">
        <p><b>#<?php echo $bk["id"];?> - <?php echo $bk["project"];?></b>
          (<?php echo $bk["project_path"];?>)
          <br>
          <span><?php echo $bk["comment"];?></span>
          <br>
          <a href="<?php echo $_SERVER["PHP_SELF"];?>?download=<?php echo $bk["id"];?>">Download Backup</a>
          <br>
          <span>Created On: <?php echo $bk['created_at'];?></span>
        </p>
      </div>
    <?Php endforeach;?>
  </div>
</div>
</body>
</html>
