<?php
include 'TableModel.php';

function jsonResponse($status, $message, $data=""){
  header("Access-Control-Allow-Origin: *");
  header('Content-Type: application/json');
  echo json_encode(['response'=>[
    'message' => $message,
    'status' => $status,
    'data' => $data
  ]]);
}


$model = new TableModel('contacts');


$results = $model->getAllRecords();

jsonResponse('','', $results);
