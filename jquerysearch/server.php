<?php
$dsn = "mysql:host=localhost;dbname=wftutorials";
$user = "root";
$passwd = "";

$conn = new PDO($dsn, $user, $passwd);

function rowItem($id){
    return
        "&nbsp; <a data-id='".$id."' class='remove-task' href='javascript:void(0);'>&#215;</a>";
}

if($_GET["action"] == 'get_items'){
    $data = "";
    try {
        $results =  $conn->query("SELECT * from my_tasks");
        foreach($results as $result){
                $data .= "<li>";
                $data .= $result["name"];
                $data .= rowItem($result["id"]);
                $data .= "</li>";
        }
        echo $data;
    }catch (Exception $e){
        echo $e->getMessage();
    }

}


if($_GET["action"] == 'save_task'){
    $task = isset($_POST['task']) ? $_POST['task'] : null;
    if(!empty($task)){
        try{
            $sql = "INSERT INTO my_tasks(`name`) VALUES (?)";
            $query = $conn->prepare($sql);
            $query->execute([$task]);
            echo "good";
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
}

if($_GET["action"] == 'search_items'){
    $data = "";
    $query = isset($_GET['query']) ? $_GET['query'] : null;
    try{
        $db = $conn->prepare("SELECT * FROM my_tasks WHERE name LIKE ?");
        $db->execute(["%" . $query . "%"]);
        $results = $db->fetchAll();
        foreach ($results as $result){
            $data .= "<li>";
            $data .= $result["name"];
            $data .= rowItem($result["id"]);
            $data .= "</li>";
        }
        echo $data;
    }catch (Exception $e){
        echo $e->getMessage();
    }
}

if($_GET["action"] == 'remove_task'){
    $taskId = isset($_POST['id']) ? $_POST['id'] : null;
    if(!empty($taskId)){
        try{
            $sql = "DELETE from my_tasks WHERE id=?";
            $query = $conn->prepare($sql);
            $query->execute([$taskId]);
            echo "good";
        }catch (Exception $e){
            echo $e->getMessage();
        }
    }
}