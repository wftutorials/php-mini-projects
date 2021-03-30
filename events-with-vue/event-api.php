<?php

include 'DBModel.php';

class TableModel extends DBModel {

  public $table = 'events';

  public function getCurrentPage(){
    if(isset($_GET['page']))
    {
      $page = $_GET['page'];
    }
    else
    {
      $page = 1;
    }
    $this->currentPage = $page;
    return $page;
  }

  public function runPagination(){
    $this->getCurrentPage();
    $this->getPageIndex();
  }

  public function getPageIndex(){
    $start_from = ($this->getCurrentPage()-1)*($this->pageLimit);
    $this->pageIndex = $start_from;
    return $start_from;
  }

  private function setTotalPaginationPages($val){
    $this->totalPages = $val;
  }

  private function fetchAll($sql, $attributes=array()){
    $this->runPagination(); // set pagination values from request
    $conn = $this->get_connection();
    $query = $conn->prepare($sql);
    $query->execute($attributes);
    $results = $query->fetchAll();
    $total_records =count($results);
    $total_pages =ceil($total_records/$this->pageLimit);
    $this->setTotalPaginationPages($total_pages);
    $query = $conn->prepare($sql . " LIMIT $this->pageIndex, $this->pageLimit");
    $query->execute($attributes);
    $results = $query->fetchAll();
    return $results;
  }

  public function getAllRecords($sel=array()){
    $results = $this->fetchAll("SELECT * from ". $this->table);
    return $this->getResults($results, $sel);
  }

  public function searchRecords($safe=[], $q, $sel=array()){
    $sql = $this->getSearchSQL($safe);
    $searchPlaceholders = [];
    foreach ($safe as $s){
      $searchPlaceholders[] = "%$q%";
    }
    $results = $this->fetchAll($sql, $searchPlaceholders);
    return $this->getResults($results, $sel);
  }

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



if($_GET['action'] == 'save-event'){

  $model = new DBModel();
  $id = isset($_POST['id']) ? $_POST['id'] : null;
  if($id){
    $model->updateRecordByPk($id, [
      'title' => isset($_POST['title']) ? $_POST['title'] : null,
      'description' => isset($_POST['description']) ? $_POST['description'] : null,
      'venue' => isset($_POST['venue']) ? $_POST['venue'] : null,
      'eventDate' => isset($_POST['eventDate']) ? $_POST['eventDate'] : null,
      'eventTime' => isset($_POST['eventTime']) ? $_POST['eventTime'] : null,
      'contactName' => isset($_POST['contactName']) ? $_POST['contactName'] : null,
      'contactNumber' => isset($_POST['contactNumber']) ? $_POST['contactNumber'] : null,
    ]);
  }else{
    $id = $model->insertRecord([
      'title' => isset($_POST['title']) ? $_POST['title'] : null,
      'description' => isset($_POST['description']) ? $_POST['description'] : null,
      'venue' => isset($_POST['venue']) ? $_POST['venue'] : null,
      'eventDate' => isset($_POST['eventDate']) ? $_POST['eventDate'] : null,
      'eventTime' => isset($_POST['eventTime']) ? $_POST['eventTime'] : null,
      'contactName' => isset($_POST['contactName']) ? $_POST['contactName'] : null,
      'contactNumber' => isset($_POST['contactNumber']) ? $_POST['contactNumber'] : null,
    ]);
  }
  jsonResponse('good','good',['id'=>$id]);
}



if($_GET['action'] == 'get-events'){
  $model = new DBModel();
  $data = $model->getAllRecords([]);
  jsonResponse('good','good',$data);
}

if($_GET['action'] == 'search-events'){
  $model = new DBModel();
  $query = isset($_GET['query']) ? $_GET['query'] : "";
  $data = $model->searchRecords(
    ['title','description','venue',
      'contactName','contactNumber'
    ], $query);
  jsonResponse('good','good',$data);
}


if($_GET['action'] == 'delete-event'){

  $data =  [];
  $id = isset($_GET['id']) ? $_GET['id'] : null;
  $model = new TableModel();
  if($model->deleteRecordByPk($id)){
    jsonResponse('good','good',[]);
  }

}
