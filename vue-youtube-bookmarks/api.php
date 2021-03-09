<?php


function get_connection(){
  $dsn = "mysql:host=localhost;dbname=wftutorials";
  $user = "root";
  $passwd = "";
  $conn = new PDO($dsn, $user, $passwd);
  return $conn;
}


function jsonResponse($status, $message, $data=""){
  header("Access-Control-Allow-Origin: *");
  header('Content-Type: application/json');
  echo json_encode(['response'=>[
    'message' => $message,
    'status' => $status,
    'data' => $data
  ]]);
}


if($_GET['action'] == 'sync-links'){

  $data =  [];
  $links = isset($_POST['urls']) ? $_POST['urls'] : null;
  $conn = get_connection();
  if($links){
    foreach ($links as $link){
      $sql = "INSERT INTO youtube_bookmarks(`url`) VALUES (?)"; // create sql
      $query = $conn->prepare($sql); // prepare
      $query->execute([$link]); // execute
      $id = $conn->lastInsertId();
      $data[] = [
        'id' => $id,
        'url' => $link
      ];
    }
  }
  jsonResponse('good','good',$data);
}



if($_GET['action'] == 'update-links'){

  $data =  [];
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $links = isset($_POST['urls']) ? $_POST['urls'] : null;
  $conn = get_connection();
  if($links){
    foreach ($links as $link){
      $sql = "UPDATE youtube_bookmarks SET `url`=? WHERE id=?"; // create sql
      $query = $conn->prepare($sql); // prepare
      $query->execute([$link, $id]); // execute
      $data[] = [
        'id' => $id,
        'url' => $link
      ];
    }
  }
  jsonResponse('good','good',$data);
}



if($_GET['action'] == 'get-links'){
  $conn = get_connection();
  $query = $conn->prepare("SELECT * from youtube_bookmarks");
  $query->execute([]);
  $results = $query->fetchAll();
  $data = [];
  foreach ($results as $result){
    $data[] = [
      'id' => $result['id'],
      'url' => $result['url']
    ];
  }
  jsonResponse('good','good', $data);
}


if($_GET['action'] == 'delete-links'){

  $data =  [];
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $conn = get_connection();
  if($id){
    $sql = "DELETE FROM youtube_bookmarks WHERE id=?"; // create sql
    $query = $conn->prepare($sql); // prepare
    $query->execute([$id]); // execute
  }
  jsonResponse('good','good',[]);

}
