<?php

$database = "wftutorials";
$db_user = "root";
$db_password = "";
$backupLocation = "C:\Users\wfranklin\Documents\GitHub\\test";
$alltables = null;
$backLimit =0;


function get_connection(){
  global $database;
  global $db_user;
  global $db_password;

  $dsn = "mysql:host=localhost;dbname=$database";
  $user = $db_user;
  $passwd = $db_password;

  $conn = new PDO($dsn, $user, $passwd);

  return $conn;
}

function cleanString($string) {
  $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
  return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}


function back_by_tables(){

  global $database;
  global $backupLocation;
  global $alltables;
  global $backLimit;
  $tables = [];
  // get all table names

  // remove old files
  if($backLimit > 0){
    echo "Old Files check \r\n";
    removeOldFiles();
  }

  $conn = get_connection();
  if(empty($alltables)){
    $sql = "SELECT TABLE_NAME AS tablename
FROM INFORMATION_SCHEMA.TABLES
WHERE TABLE_SCHEMA = '$database'";
    $query = $conn->prepare($sql);
    $query->execute([]);
    $results = $query->fetchAll();
    foreach ($results as $result){
      $tables[] = $result['tablename'];
    }
  }else{
    $tables = explode(',', $alltables);
  }

  $output = "";

  foreach ($tables as $table) {
    echo "Saving the $table \r\n";
    $query = $conn->prepare('SELECT * FROM ' . $table);
    $query->execute([]);
    $result = $query->fetchAll(PDO::FETCH_ASSOC); // important
    $output .= 'DROP TABLE IF EXISTS ' . $table . ';';
    $query2 = $conn->prepare('SHOW CREATE TABLE ' . $table);
    $query2->execute([]);
    $q2result = $query2->fetchAll();
    if(isset($q2result[0]['Create Table'])){
      $output .= "\n\n" . $q2result[0]['Create Table'] . ";\n\n";
    }
    foreach ($result as $row) {
      $output .= 'INSERT INTO ' . $table . ' VALUES(';
      foreach ($row as $data) {
        $data = addslashes($data);

        // Updated to preg_replace to suit PHP5.3 +
        $data = preg_replace("/\n/", "\n", $data);
        if (isset($data)) {
          $output.= '"' . $data . '"';
        } else {
          $output.= '""';
        }
        $output.= ',';
      }
      $output = substr($output, 0, strlen($output) - 1);
      $output.= ");\n";
    }
    $output.="\n\n\n";
  }

  $filepath = $backupLocation . '\db_'.$database. '_' . strtotime(date("D M d, Y G:i:s")).'.sql';
  echo "Writing to file: $filepath \r\n";

  $handle = fopen($filepath, 'w+');
  fwrite($handle, $output);
  fclose($handle);
  echo "Backup complete \r\n";
}

function get_arguments(){
  $arguments = getopt("d:t:l:s:");
  return $arguments;
}

function removeOldFiles(){
  $fCount = 0;
  $firstFile = null;
  global $backLimit;
  global $backupLocation;
  global $database;

  foreach (new DirectoryIterator($backupLocation) as $file) {
    if($file->isDot()) continue;
    if (strpos($file->getFilename(), 'db_'.$database) !== false) {
      if(empty($firstFile)){
        $firstFile = $file->getFilename();
      }
      $fCount++;
    }
  }
  if($fCount >= $backLimit && !empty($firstFile)){
    echo "Removing file $firstFile \r\n";
    unlink($backupLocation . DIRECTORY_SEPARATOR . $firstFile);
  }
}


// not running from cli
if(!defined('STDIN') ){
  echo("Not Running from CLI"); exit;
}

$flags = get_arguments();
//var_dump($flags); exit;

$database = !empty($flags['d']) ? $flags['d'] : $database; // set the database
$alltables = !empty($flags['t']) ? $flags['t'] : null; // set the tables to backup
$backupLocation = !empty($flags['l']) ? $flags['l'] : $backupLocation; // set backup location
$backLimit = !empty($flags['s']) ? $flags['s'] : $backLimit;

$filepath = "";

try{
  echo "Starting backup...\r\n";
  back_by_tables();
}catch(Exception $e){
  echo $e->getMessage() . "\r\n";
}
