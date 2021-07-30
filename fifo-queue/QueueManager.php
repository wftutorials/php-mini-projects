<?php

class QueueManager {
    
	public $sourceFilename = "queue_2.php";

	function __construct() {
	
	}
	
    function writeToQueue($input){
        $output = "";
        $output .= "<?php ";
        $output .= "return [";
        foreach($input as $value){
            $output .= '"' . $value . '",' ;
        }
        $output .= "] ";
        $output .= "?>";
        $myfile = fopen($this->sourceFilename, "w+") or die("Unable to open file!");
        fwrite($myfile, $output);
        fclose($myfile);
    }

    function getQueue(){
        $baseQueue = include $this->sourceFilename;
        if(count($baseQueue) <= 0){
            $baseQueue = [];
        }
        return $baseQueue;
    }
    
    function addToQueue($value){
        $queue = $this->getQueue();
        array_push($queue,$value); // push into the array
        var_dump($queue); // see results
        $this->writeToQueue($queue);
        return $this->getQueue(); // return the current results
    }
    
    // lifo
    function getLastItem(){
     $queue = getQueue();
     $value = array_pop($queue);
     $this->writeToQueue($queue);
     return $value;
    }
    
    // fifo function
    function getFirstItem(){
        $queue = getQueue();
        $value = array_shift($queue);
        $this->writeToQueue($queue);
        return $value; 
    }
    
    function emptyQueue(){
     $this->writeToQueue([]);
    }
    
    function isQueueEmpty(){
     $queue = getQueue();
     if(count($queue) <= 0){
         return true;
     }
     return false;
    }
}