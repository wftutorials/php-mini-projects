<?php

class TableModel {

  public $data;
  public $table ="events";

  public function __construct($tbl="")
  {
    if($tbl){
      $this->table = $tbl;
    }
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
    return true;
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

}
