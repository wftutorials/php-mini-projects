<?php

function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}


function save_song($name, $length, $artist){
    $conn = get_connection();
    $sql = "INSERT INTO songs(`name`, `length`, `artist`) VALUES (?,?,?)";
    $query = $conn->prepare($sql);
    $query->execute([$name, $length, $artist]);
    return $conn->lastInsertId();
}

function save_lyric($song, $content, $order, $id=""){
    try{
        $conn = get_connection();
        if($id){
            $sql = "UPDATE song_lyrics set `content`=?, `priority`=? WHERE id=?";
            $query = $conn->prepare($sql);
            $query->execute([$content, $order, $id]);
            return $id;
        }else{
            $sql = "INSERT INTO song_lyrics(`song_id`,`content`,`priority`) VALUES (?,?,?)";
            $query = $conn->prepare($sql);
            $query->execute([$song, $content, $order]);
            return $conn->lastInsertId();
        }

    }catch (Exception $e){
        echo $e->getMessage();
    };
}



function get_all_songs(){
    $results = [];
    try{
        $conn = get_connection();
        $results = $conn->query("SELECT * from songs");
    }catch (Exception $e){

    }
    return $results;
}

function get_all_lyrics($id){
    $results = [];
    try{
        $conn = get_connection();
        $query  = $conn->prepare("SELECT * from song_lyrics WHERE song_id=?");
        $query->execute([$id]);
        $results = $query->fetchAll();
    }catch (Exception $e){

    }
    return $results;
}

function get_lyric($id){
    $results = [];
    try{
        $conn = get_connection();
        $query  = $conn->prepare("SELECT * from song_lyrics WHERE id=? LIMIT 1");
        $query->execute([$id]);
        $results = $query->fetchAll();
        if(isset($results[0])){
            $results = $results[0];
        }
    }catch (Exception $e){

    }
    return $results;
}

function delete_song_lyric($id){
    $conn = get_connection();
    $sql= "DELETE FROM song_lyrics WHERE id=?";
    $query = $conn->prepare($sql);
    $query->execute([$id]);
}

function redirectPage($page){
    header("Location: $page");
    exit();
}

function get_song($id){
    $results = [];
    try{
        $conn = get_connection();
        $query  = $conn->prepare("SELECT * from songs WHERE id=? LIMIT 1");
        $query->execute([$id]);
        $results = $query->fetchAll();
        if(isset($results[0])){
            $results = $results[0];
        }
    }catch (Exception $e){

    }
    return $results;
}

function get_current_lyric($id){
    $results = get_lyric($id);
    return $results;
}

function get_next_url($song, $current){
    $nextId = 0;
    $conn = get_connection();
    $query =  $conn->prepare("SELECT * from song_lyrics WHERE song_id=? and id > ? order by priority desc LIMIT 1");
    $query->execute([$song,$current]);
    $results = $query->fetchAll();
    if(count($results) > 0){
        $nextId = $results[0]["id"];
        return 'play.php?song='.$song.'&current='.$nextId;
    }else{
        return 'end.php?song='.$song;
    }
}

function get_previous_url($song, $current){
    $nextId = 0;
    $conn = get_connection();
    $query =  $conn->prepare("SELECT * from song_lyrics WHERE song_id=? and id < ? order by id desc LIMIT 1");
    $query->execute([$song,$current]);
    $results = $query->fetchAll();
    if(count($results) > 0){
        $nextId = $results[0]["id"];
        return 'play.php?song='.$song.'&current='.$nextId;
    }else{
        return 'start.php?song='.$song;
    }
}

function setFontSize($val){
    setcookie("FONTSIZE", $val, time() + (60*60*24*10));
}

function getFontSize(){
    $fs = isset($_COOKIE['FONTSIZE']) ? $_COOKIE["FONTSIZE"] : "55";
    return $fs;
}

function getStoredFontSize(){
    $fs = getFontSize();
    return $fs . "px";
}