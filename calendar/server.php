<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}


function is_ajax_request(){
    if(!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
        && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'){
        return true;
    }
    return false;
}

function save_goal($name, $startdate, $description, $status, $notes, $id){
    $conn = get_connection();
    if($id){
        $sql = "UPDATE goal_tracker SET `name`=?, `startDate`=?, `description`=?,`status`=?, `notes`=?
        WHERE id=?";
        $query = $conn->prepare($sql); // prepare
        $query->execute([$name, $startdate, $description, $status, $notes, $id]); // execute
        return $id;
    }else{
        $sql = " INSERT INTO goal_tracker(`name`,`startDate`,`description`,`status`,`notes`)
    VALUES (?,?,?,?,?)";
        $query = $conn->prepare($sql);
        $query->execute([$name, $startdate, $description, $status, $notes]);
        return $conn->lastInsertId();
    }
}

function get_one_goal($id){
    $results = [];
    try {
        $conn = get_connection();
        $query = $conn->prepare("SELECT * from goal_tracker WHERE id=? LIMIT 1");
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

function getEvent($id){
 $goal = get_one_goal($id);
 $event = [];
 return json_encode([
     'title' => $goal['name'],
     'description' => $goal['description'],
     'start' => $goal['startDate'],
     'id' => $goal['id'],
     'status' => $goal['status'],
     'notes' => $goal['notes'],
     'notesFormatted' => nl2br($goal["notes"]),
     'dateFormatted' => date('l jS, F Y', strtotime($goal["startDate"]))
 ]);
}

function getGoals(){
    $results = [];
    try {
        $conn = get_connection();
        $query = $conn->prepare("SELECT * from goal_tracker");
        $query->execute([]);
        $results = $query->fetchAll();
    }catch (Exception $e){

    }
    return $results;
}


function getEvents(){
    $goals = getGoals();
    $events = [];
    foreach ($goals as $goal){
        $events[] = [
            'title' => $goal['name'],
            'description' => $goal['description'],
            'start' => $goal['startDate'],
            'id' => $goal['id'],
            'status' => $goal['status'],
            'borderColor' => '#ccc',
            'backgroundColor' => getEventColor($goal["status"])
        ];
    }
    return json_encode($events);
}

function getEventColor($status){
    if($status == 'pending'){
        return "blue";
    }else if($status == 'inprogress'){
        return "darkorange";
    }else if($status == 'completed'){
        return "darkgreen";
    }
    return '#378006';
}



if(is_ajax_request()){

// All my ajax requests goes here

    // saving our goals
    if(isset($_GET['action']) && $_GET['action'] =='save-goal'){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $goal = isset($_POST['goal']) ? $_POST['goal'] : null;
        $goalDate = isset($_POST['startdate']) ? $_POST['startdate'] : null;
        $goalDescription = isset($_POST['description']) ? $_POST['description'] : null;
        $goalStatus = isset($_POST['status']) ? $_POST['status'] : null;
        $goalNotes = isset($_POST['notes']) ? $_POST['notes'] : null;
        if($goal && $goalDate){
           $id =  save_goal($goal, $goalDate,$goalDescription,$goalStatus,$goalNotes, $id);
           echo $id;
        }
    }


    if(isset($_GET['action']) && $_GET['action'] =='get-events'){
        header('Content-Type: application/json');
        echo getEvents();
    }

    if(isset($_GET['action']) && $_GET['action'] =='get-event'){
        $id = isset($_GET['id']) ? $_GET['id'] : null;
        header('Content-Type: application/json');
        if($id){
            echo getEvent($id);
        }
    }

    if(isset($_GET['action']) && $_GET['action'] =='save-notes'){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $notes = isset($_POST['notes']) ? $_POST['notes'] : null;
        $conn = get_connection();
        $sql = "UPDATE goal_tracker SET `notes`=? WHERE id=?"; // create sql
        $query = $conn->prepare($sql); // prepare
        $query->execute([$notes, $id]); // execute
        header('Content-Type: application/json');
        echo json_encode([
            'notes' => nl2br($notes)
        ]);
    }

    if(isset($_GET['action']) && $_GET['action'] =='update-progress'){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $status = isset($_POST['status']) ? $_POST['status'] : null;
        $conn = get_connection();
        $sql = "UPDATE goal_tracker SET `status`=? WHERE id=?"; // create sql
        $query = $conn->prepare($sql); // prepare
        $query->execute([$status, $id]); // execute
        echo 'good';
    }

    if(isset($_GET['action']) && $_GET['action'] =='next-date'){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $next = isset($_POST['next']) ? $_POST['next'] : null;
        $goal = get_one_goal($id);
        $currentDate = $goal['startDate'];
        $nextDate = null;
        if($next == 'day'){
            $nextDate = date("Y-m-d", strtotime($currentDate . " +1 day"));
        }else if($next == 'week'){
            $nextDate = date("Y-m-d", strtotime($currentDate . " +1 week"));
        }else if($next == 'month'){
            $nextDate = date("Y-m-d", strtotime($currentDate . " +1 month"));
        }else if($next == 'year'){
            $nextDate = date("Y-m-d", strtotime($currentDate . " +1 year"));
        }
        $conn = get_connection();
        $sql = "UPDATE goal_tracker SET `startDate`=? WHERE id=?"; // create sql
        $query = $conn->prepare($sql); // prepare
        $query->execute([$nextDate, $id]); // execute
        echo 'good';
    }

    if($_GET['action'] == 'remove-goal'){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $conn = get_connection();
        $sql = "DELETE from goal_tracker WHERE id=?"; // create sql
        $query = $conn->prepare($sql); // prepare
        $query->execute([$id]); // execute
        echo "good";
    }







}