<?php


class DBModel {

  public $data;
  public $table ="events";
  public $pageLimit = 10;
  public $currentPage =1;
  public $pageIndex =1;
  public $totalPages = 1;

  public function __construct($tbl="")
  {
    if($tbl){
      $this->table = $tbl;
    }
  }

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

  public function setTotalPaginationPages($val){
    $this->totalPages = $val;
  }

  public function get_connection(){
    $dsn = "mysql:host=localhost;dbname=wftutorials";
    $user = "root";
    $passwd = "";
    $conn = new PDO($dsn, $user, $passwd);
    return $conn;
  }

  private function getTableColumns(){
    return $this->cols;
  }

  public function getResults($results, $sel=array()){
    $data = [];
    foreach ($results as $row){
      $line = [];
      foreach($row as $key=>$value){
        if(is_numeric($key)){
          continue;
        }
        if(count($sel) > 0){
          if(in_array($key, $sel)){
            //echo $value;
            $line[$key] = $value;
          }
        }else{
          $line[$key] = $value;
        }
      }
      $data[] = $line;
    }
    return $data;
  }

  public function getAllBySQL($sql, $params=array()){
    $conn = $this->get_connection();
    $query = $conn->prepare($sql);
    $query->execute($params);
    $results = $query->fetchAll();
    return $results;
  }

  public function getAllRecords($sel=array()){
    $conn = $this->get_connection();
    $query = $conn->prepare("SELECT * from ". $this->table);
    $query->execute([]);
    $results = $query->fetchAll();
    return $this->getResults($results, $sel);
  }

  public function getOneRecordByPk($id, $pk='id'){
    $conn = $this->get_connection();
    $query = $conn->prepare("SELECT * from ". $this->table. " WHERE $pk=? LIMIT 1");
    $query->execute([$id]);
    $results = $query->fetchAll();
    return $this->getResults($results)[0];
  }

  private function getInsertSQL($data){
    $sql = "INSERT INTO ". $this->table;
    $sql .= " (";
    foreach ($data as $col){
      $sql .= "`$col`,";
    }
    $sql = rtrim($sql,',');
    $sql .= ") VALUES (";
    foreach ($data as $col){
      $sql .= "?,";
    }
    $sql = rtrim($sql,',');
    $sql .= ")";
    return $sql;
  }

  private function getUpdateSQL($data, $pk){
    $sql = "UPDATE ". $this->table . " SET ";
    $sql .= "";
    foreach ($data as $col){
      $sql .= "`$col`=?,";
    }
    $sql = rtrim($sql,',');
    $sql .= " WHERE `$pk`=?";
    return $sql;
  }

  protected function getSearchSQL($cols){
    $sql = "SELECT * from ". $this->table;
    $sql .= " WHERE";
    foreach ($cols as $col){
      $sql .= " $col LIKE ? OR";
    }
    $sql = rtrim($sql, "OR");
    return $sql;
  }

  private function getColsAndValues($data){
    $cols = [];
    $values = [];
    foreach ($data as $key=> $value){
      $cols[] =  $key;
      $values[] =  $value;
    }
    return [
      'cols' => $cols,
      'values' => $values
    ];
  }

  public function insertRecord($data=[]){
    $conn = $this->get_connection();
    $cvs = $this->getColsAndValues($data);
    $sql = $this->getInsertSQL($cvs['cols']);
    $query = $conn->prepare($sql); // prepare
    $query->execute($cvs['values']); // execute
    return $conn->lastInsertId();
  }

  public function updateRecordByPk($id, $data=[], $pk='id'){
    $conn = $this->get_connection();
    $cvs = $this->getColsAndValues($data);
    $sql = $this->getUpdateSQL($cvs['cols'], $pk);
    $query = $conn->prepare($sql); // prepare
    $res = array_merge($cvs['values'],[$id]);
    $query->execute($res); // execute
  }

  public function deleteRecordByPk($id, $pk="id"){
    $conn = $this->get_connection();
    $query = $conn->prepare("DELETE FROM ".$this->table." WHERE $pk=?");
    $query->execute([$id]);
    return true;
  }

  public function searchAllRecords($safe=[], $q, $sel=array()){
    $conn = $this->get_connection();
    $sql = $this->getSearchSQL($safe);
    $query = $conn->prepare($sql); //
    $searchPlaceholders = [];
    foreach ($safe as $s){
      $searchPlaceholders[] = "%$q%";
    }
    $query->execute($searchPlaceholders);
    $results = $query->fetchAll();
    return $this->getResults($results, $sel);
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

  public function getRecords($sel=array()){
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
