<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}

function get_parent_tasks(){
    $results = [];
    try{
        $conn = get_connection();
        $results = $conn->query("SELECT * from all_tasks WHERE parent is NULL");
    }catch (Exception $e){

    }
    return $results;
}
function get_child_tasks($id){
    $results = [];
    try{
        $conn = get_connection();
        $results = $conn->query("SELECT * from all_tasks WHERE parent=". $id);

    }catch (Exception $e){

    }
    return $results;
}

if(isset($_POST['save-task'])){
    $task = isset($_POST['task']) ? $_POST['task'] : null;
    $parent = isset($_POST['parent']) ? $_POST['parent'] : null;
    try{
        $conn = get_connection();
        if($parent && is_numeric($parent)){
            $sql = "INSERT INTO all_tasks(`parent`, `task`) VALUES (?,?)";
            $query = $conn->prepare($sql);
            $query->execute([$parent, $task]);

        }else if($task){
            $sql = "INSERT INTO all_tasks(`task`) VALUES (?)";
            $query = $conn->prepare($sql);
            $query->execute([$task]);
        }
    }catch (Exception $e){

    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Task Builder</title>
</head>
<body>
<h3>Add a new task</h3>
<form method="post">
    <select name="parent">
        <option selected value>-- Select a parent --</option>
        <?Php foreach(get_parent_tasks() as $task):?>
        <option value="<?php echo $task["id"];?>"><?Php echo $task["task"];?></option>
        <?php endforeach; ?>

    </select>
    <input name="task" type="text" />
    <button type="submit" name="save-task">Save Task</button>
</form>
<ul>
    <?Php foreach(get_parent_tasks() as $task):?>
        <li><?php echo $task["task"];?>
            <?php foreach(get_child_tasks($task["id"]) as $child):?>
                <br>&nbsp; &#8727; <?php echo $child["task"];?>
            <?php endforeach;?>
        </li>
    <?php endforeach; ?>
</ul>

</body>
</html>