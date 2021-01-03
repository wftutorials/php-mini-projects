<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}


function save_question($question){
    $conn = get_connection();
    $sql = "INSERT INTO questions(`question`) VALUES (?)";
    $query = $conn->prepare($sql);
    $query->execute([$question]);
    return $conn->lastInsertId();
}

function save_answer($questionId, $answer, $isCorrect){
    $conn = get_connection();
    $sql ="INSERT INTO answers(`questionId`, `answer`, `correct`) VALUES (?,?,?)";
    $query = $conn->prepare($sql);
    $query->execute([$questionId, $answer, $isCorrect]);
}

function save_response($question, $answer){
    $conn = get_connection();
    $sql = "INSERT INTO responses(`question`, `answer`) VALUES (?,?)";
    $query = $conn->prepare($sql);
    $query->execute([$question, $answer]);
}

function get_all_questions(){
    $results = [];
    try{
        $conn = get_connection();
        $results = $conn->query("SELECT * from questions");
    }catch (Exception $e){
        // log your error in a file or something
        $results = [];
    }
    return $results;
}

function get_all_answers($id){
    $results = [];
    try{
        $conn = get_connection();
        $sql = "SELECT * from answers WHERE questionId=?";
        $query = $conn->prepare($sql);
        $query->execute([$id]);
        $results = $query->fetchAll();
    }catch (Exception $e){
        // log your error in a file or something
        $results = [];
    }
    return $results;
}

function get_next_question($pointer){
    $results = [];
    try{
        $conn = get_connection();
        $sql = "SELECT * from questions WHERE id >? LIMIT 1";
        $query = $conn->prepare($sql);
        $query->execute([$pointer]);
        $results = $query->fetchAll();
    }catch (Exception $e){
        // log your error in a file or something
        $results = [];
    }
    return $results;
}

function clear_response(){
    try{
        $conn = get_connection();
        $conn->query("TRUNCATE responses");
    }catch(Exception $e){

    }
}

function get_responses(){
    $results = [];
    try{
        $conn = get_connection();
        $sql = "SELECT questions.id, questions.question, answers.answer, answers.correct from responses
JOIN questions on responses.question = questions.id
JOIN answers on answers.id = responses.answer;";
        $query = $conn->prepare($sql);
        $query->execute();
        $results = $query->fetchAll();
    }catch (Exception $e){
        // log your error in a file or something
        $results = [];
    }
    return $results;
}

function get_correct_answer($id){
    $results = [];
    try{
        $conn = get_connection();
        $sql = "SELECT * from answers WHERE correct =1 and questionId=?;";
        $query = $conn->prepare($sql);
        $query->execute([$id]);
        $results = $query->fetchAll();
    }catch (Exception $e){
        // log your error in a file or something
        $results = [];
    }
    return $results;
}
