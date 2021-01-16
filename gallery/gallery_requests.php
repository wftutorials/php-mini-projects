<?php

$baseUrl = "http://dev.gallery.com/";


function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
}

function get_random_name($num = 6){
    $characters = 'abcdefghijklmnopqrstuvwxyz0123456789';
    $string = '';
    $max = strlen($characters) - 1;
    for ($i = 0; $i < $num; $i++) {
        $string .= $characters[mt_rand(0, $max)];
    }
    return $string;
}


function save_to_gallery($filename, $originalname){
    $conn = get_connection();
    $sql = "INSERT INTO gallery(`filename`, `title`) VALUES (?,?)";
    $query = $conn->prepare($sql);
    $query->execute([$filename, $originalname]);
}

function save_gallery_folder($name){
    $conn = get_connection();
    $sql = "INSERT INTO gallery_folders(`name`) VALUES (?)";
    $query = $conn->prepare($sql);
    $query->execute([$name]);
}



function get_media($gallery =""){
    $results = [];
    try{
        $conn = get_connection();
        if($gallery){
            $query = $conn->prepare("SELECT * from gallery WHERE folder=? ");
            $query->execute([$gallery]);
            $results = $query->fetchAll();
        }else{
            $results = $conn->query("SELECT * from gallery");
        }
    }catch (Exception $e){

    }
    return $results;
}

function get_folders(){
    $results = [];
    try{
        $conn = get_connection();
        $results = $conn->query("SELECT * from gallery_folders");
    }catch (Exception $e){

    }
    return $results;
}

if($_GET['action'] == 'upload'){
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $uploadDir = "./uploads/";
        if(isset($_FILES["file"]) && $_FILES["file"]["error"] == 0){
            $filename = $_FILES["file"]["name"];
            $filetype = $_FILES["file"]["type"];
            $filesize = $_FILES["file"]["size"];
            $random_name = get_random_name() . "." . pathinfo($filename, PATHINFO_EXTENSION);
            $fullPath = $uploadDir . $random_name;
            if(file_exists($fullPath)){
                // error
            }else{
                move_uploaded_file($_FILES['file']['tmp_name'], $fullPath);
                save_to_gallery($random_name, $filename);
                echo "Your file was upload successfully";
            }
        }

    }
}

if($_GET['action'] == "get-files"){
    $o = "";
    $currentGallery = isset($_GET['gallery']) ? $_GET['gallery'] : null;
    $media = get_media($currentGallery);
    foreach ($media as $file){
        $imgUrl = $baseUrl . "uploads/" . $file["filename"];
        $o .= '<div class="gallery-block" data-url="'.$imgUrl.'" data-id="'.$file["id"].'">';
        $o .= '<input name="gid[]" type="checkbox" value="'.$file["id"].'"/>';
        $o .= '<div class="gallery-img" style=\'background-image: url("'.$imgUrl.'")\'>';
        $o .= "</div></div>";
    }
    echo $o;
}

if($_GET['action'] == 'save-gallery'){
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $gname = isset($_POST['gallery']) ? $_POST['gallery'] : null;
        if($gname){
            save_gallery_folder($gname);
            echo "good";
        }
    }
}


if($_GET['action'] == 'get-folders'){

    $o = "<select id='folders-list'>";
    $o .= "<option value=''>All</option>";
    foreach (get_folders() as $folder){
        $o .= "<option>" . $folder["name"] . "</option>";
    }
    $o .= "</select>";
    echo $o;
}

if($_GET['action'] == 'switch-gallery'){
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $ids = isset($_POST['ids']) ? $_POST['ids'] : [];
        $ids = explode(",", $ids);
        $gallery = isset($_POST['gallery']) ? $_POST['gallery'] : null;
        $conn = get_connection();
        foreach ($ids as $id){
            $sql = "UPDATE gallery SET `folder`=? WHERE id = ?";
            $query = $conn->prepare($sql);
            $query->execute([$gallery, $id]);
        }
        echo "good";

    }
}


if($_GET['action'] == 'delete-file'){
    if($_SERVER["REQUEST_METHOD"] == "POST"){
        $id = isset($_POST['id']) ? $_POST['id'] : null;
        $conn = get_connection();
        $sql = "SELECT filename FROM gallery WHERE id=? LIMIT 1";
        $query = $conn->prepare($sql);
        $query->execute([$id]);
        $results = $query->fetchAll();
        if(count($results) > 0 ){
          //  echo "here";
            $filename = $results[0]["filename"];
            $filePath = $_SERVER["DOCUMENT_ROOT"]
                . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR
                . $filename;
         //   echo $filePath;
            if(file_exists($filePath)){
            //    echo "exits";
                unlink($filePath);
                $sql = "DELETE from gallery WHERE id=?";
                $query = $conn->prepare($sql);
                $query->execute([$id]);
                echo "good";
            }
        }
    }

}
