<?php


function writeToQueue($input){
    $output = "";
    $output .= "<?php ";
    $output .= "return [";
    foreach($input as $value){
        $output .= '"' . $value . '",' ;
    }
    $output .= "] ";
    $output .= "?>";
    $myfile = fopen("queue_1.php", "w+") or die("Unable to open file!");
    fwrite($myfile, $output);
    fclose($myfile);
}

function getQueue(){
    $baseQueue = include "queue_1.php";
    if(count($baseQueue) <= 0){
        $baseQueue = [];
    }
    return $baseQueue;
}

function addToQueue($value){
    $queue = getQueue();
    array_push($queue,$value); // push into the array
    var_dump($queue); // see results
    writeToQueue($queue);
    return getQueue(); // return the current results
}

// lifo
function getLastItem(){
 $queue = getQueue();
 $value = array_pop($queue);
 writeToQueue($queue);
 return $value;
}

// fifo function
function getFirstItem(){
    $queue = getQueue();
    $value = array_shift($queue);
    writeToQueue($queue);
    return $value; 
}

function emptyQueue(){
 writeToQueue([]);
}

function isQueueEmpty(){
 $queue = getQueue();
 if(count($queue) <= 0){
     return true;
 }
 return false;
}

