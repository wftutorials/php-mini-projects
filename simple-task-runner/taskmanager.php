<?php

include 'DBModel.php';
include 'TaskResult.php';
include 'tasks.php';
/** @var $taskResult TaskResult */


if(!defined('STDIN') ){
  echo("Not Running from CLI"); exit;
}

$model = new DBModel();
$model->table = "sys_tasks";

$tasks = $model->getAllBySQL("SELECT * from sys_tasks WHERE started IS NULL");

foreach ($tasks as $task){
  $started = isset($task["started"]) ? $task["started"] : null;
  if(!$started){
    $func = $task["task"];
    echo "Loading task: " . $func . "\r\n";
    $args = [];
    $arguments = isset($task["arguments"]) ? $task["arguments"] : null;
    if(!empty($arguments)){
      $args = explode(',', $arguments);
    }
    // run task
    $taskResult = call_user_func_array($func, $args);

    if ($taskResult instanceof TaskResult) {
      // show log
      echo $taskResult->getLog() . "\r\n";

      // finish task
      $model->updateRecordByPk($task["id"],[
        'started' => $taskResult->getStarted(),
        'completed' => $taskResult->getCompleted(),
        'logs' => $taskResult->getLog(),
      ],'id');
    }else{
      echo "Task returned incorrectly \r\n";
      $model->updateRecordByPk($task["id"],[
        'started' => date("Y-m-d H:i:s"),
        'completed' => date("Y-m-d H:i:s"),
        'logs' => "Task returned incorrectly. Task failed",
      ],'id');
    }

  }
  sleep(1); // sleep before starting next task
}

