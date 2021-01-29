<?php

function get_connection(){
  $dsn = "mysql:host=localhost;dbname=wftutorials";
  $user = "root";
  $passwd = "";
  $conn = new PDO($dsn, $user, $passwd);
  return $conn;
}

function save_task($type, $task, $id){
  $conn = get_connection();
  if($id){
    $sql = "UPDATE kaban_board SET `task`=? WHERE id=?"; // create sql
    $query = $conn->prepare($sql); // prepare
    $query->execute([$task, $id]); // execute
    return $id;
  }else{
    $sql = "INSERT INTO kaban_board(`task`,`type`) VALUES (?,?)"; // create sql
    $query = $conn->prepare($sql); // prepare
    $query->execute([$task,$type]); // execute
    return $conn->lastInsertId();
  }
}

function move_task($id, $position){
  $conn = get_connection();
  $sql = "UPDATE kaban_board SET `type`=? WHERE id=?"; // create sql
  $query = $conn->prepare($sql); // prepare
  $query->execute([$position,$id]); // execute
}

function get_tasks($type){
  $results = [];
  try {
    $conn = get_connection();
    $query = $conn->prepare("SELECT * from kaban_board WHERE type=? order by id desc");
    $query->execute([$type]);
    $results = $query->fetchAll();
  }catch (Exception $e){

  }
  return $results;
}

function get_task($id){
  $results = [];
  try {
    $conn = get_connection();
    $query = $conn->prepare("SELECT * from kaban_board WHERE id=?");
    $query->execute([$id]);
    $results = $query->fetchAll();
    $results = $results[0];
  }catch (Exception $e){

  }
  return $results;
}


function show_tile($taskObject, $type=""){
  $baseUrl = $_SERVER["PHP_SELF"]."?shift&id=".$taskObject["id"]."&type=";
  $editUrl = $_SERVER["PHP_SELF"] . "?edit&id=".$taskObject["id"]."&type=". $type;
  $deleteUrl = $_SERVER["PHP_SELF"] . "?delete&id=".$taskObject["id"];
  $o = '<span class="board">'.$taskObject["task"].'
      <hr>
      <span>
        <a href="'.$baseUrl.'backlog">B</a> |
        <a href="'.$baseUrl.'pending">P</a> |
        <a href="'.$baseUrl.'progress">IP</a> |
        <a href="'.$baseUrl.'completed">C</a> |
      </span>
      <a href="'.$editUrl.'">Edit</a> | <a href="'.$deleteUrl.'">Delete</a>
      </span>';
  return $o;
}

function get_active_value($type, $content){
  $currentType = isset($_GET['type']) ? $_GET['type']:  null;
  if($currentType == $type){
    return $content;
  }
  return "";
}


$activeId = "";
$activeTask = "";


if(isset($_GET['shift'])){
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $type = isset($_GET['type']) ? $_GET['type'] : null;
  if($id){
    move_task($id, $type);
    header("Location: ". $_SERVER['PHP_SELF']);
    exit();
  }else{
    // redirect take no action.
    header("Location: ". $_SERVER['PHP_SELF']);
  }
}

if(isset($_GET['edit'])){
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $activeId = $id;
  $type = isset($_GET['type']) ? $_GET['type'] : null;
  if($id){
    $taskObject = get_task($id);
    $activeTask = $taskObject["task"];
  }
}

if(isset($_GET['delete'])){
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  if($id){
    try {
      $conn = get_connection();
      $query = $conn->prepare("DELETE from kaban_board WHERE id=?");
      $query->execute([$id]);
      header("Location: ". $_SERVER['PHP_SELF']);
    }catch (Exception $e){

    }
  }
}

if($_SERVER["REQUEST_METHOD"] == "POST"){

  $backlog = "";
  $pending = "";
  $progress = "";
  $completed = "";
  $taskId = isset($_POST['task']) ? $_POST['task'] : null;

  if(isset($_POST['save-backlog'])){
    $backlog = isset($_POST['backlog']) ? $_POST['backlog'] : null;
    save_task('backlog', $backlog, $activeId);

  }else if(isset($_POST['save-pending'])){
    $pending = isset($_POST['pending']) ? $_POST['pending'] : null;
    save_task('pending', $pending, $activeId);
  }else if(isset($_POST['save-progress'])){
    $progress = isset($_POST['progress']) ? $_POST['progress'] : null;
    save_task('progress', $progress, $activeId);
  }else if(isset($_POST['save-completed'])){
    $completed = isset($_POST['completed']) ? $_POST['completed'] : null;
    save_task('completed', $completed, $activeId);
  }
  if($activeId){
    header("Location: ". $_SERVER['PHP_SELF']);
  }

}



?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Kaban Board</title>
</head>
<body>
<style>
  body {
    background: cornflowerblue;
    min-width: 1500px;
    width: 100%;
  }
  table td {
    height: 50px;
    padding: 5px;
  }

  table td button {
    height: 35px;
  }

  table {
    background: #CCCCCC;
  }

  .board-column{
    float: left;
    width: 20%;
    background:#ebecf0;
    padding: 5px;
    margin-right: 5px;
    min-height: 924px;

  }

  .board-column h3{
    margin-left: 5px;
  }

  .board {
    min-height: 30px;
    padding: 10px;
    background: white;
    box-shadow:4px 4px 10px rgba(0,0,0,0.06);
    width: 91%;
    display: inline-block;
    margin: 5px;
    border-radius: 3px;
  }
  .board-form  button {
    height: 35px;
  }

  .board-form {
    margin-left: 5px;
  }

  .board-items{
    overflow-y: scroll;
    overflow-x: hidden;
    min-height: 700px;
  }
</style>
<h2 style="text-align: center">Kanban Board</h2>
<div class="bottom" style="margin-bottom: 100px;">
  <form method="post">
    <input type="hidden" value="<?php echo $activeId;?>" name="task"/>
  <div class="board-column">
    <h3>Backlog</h3>
    <div class="board-form">
      <input value="<?php echo get_active_value("backlog", $activeTask);?>" type="text" name="backlog" style="height: 30px; width: 70%" autocomplete="off"/>
      <button type="submit" name="save-backlog">Save</button>
    </div>
    <div class="board-items">
      <?php foreach (get_tasks('backlog') as $task):?>
          <?php echo show_tile($task,'backlog');?>
      <?php endforeach;?>
    </div>
  </div>

  <div class="board-column">
    <h3>Pending</h3>
    <div class="board-form">
      <input value="<?php echo get_active_value("pending", $activeTask);?>" type="text" name="pending" style="height: 30px; width: 70%" autocomplete="off"/>
      <button type="submit" name="save-pending">Save</button>
    </div>
    <div class="board-items">
      <?php foreach (get_tasks('pending') as $task):?>
        <?php echo show_tile($task,'pending');?>
      <?php endforeach;?>
    </div>
  </div>

  <div class="board-column">
    <h3>In Progress</h3>
    <div class="board-form">
      <input value="<?php echo get_active_value("progress", $activeTask);?>" type="text" name="progress" style="height: 30px; width: 70%" autocomplete="off"/>
      <button type="submit" name="save-progress">Save</button>
    </div>
    <div class="board-items">
      <?php foreach (get_tasks('progress') as $task):?>
        <?php echo show_tile($task,'progress');?>
      <?php endforeach;?>
    </div>
  </div>

  <div class="board-column">
    <h3>Completed</h3>
    <div class="board-form">
      <input value="<?php echo get_active_value("completed", $activeTask);?>" type="text" name="completed" style="height: 30px; width: 70%" autocomplete="off"/>
      <button type="submit" name="save-completed">Save</button>
    </div>
    <div class="board-items">
      <?php foreach (get_tasks('completed') as $task):?>
        <?php echo show_tile($task,'completed');?>
      <?php endforeach;?>
    </div>
  </div>
  </form>
</div>

</body>
</html>


