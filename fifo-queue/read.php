<?php 

include "queue.php";

while (true) {
    sleep(2);
    $item = getLastItem();
    if(empty($item)){
            echo "NA \r\n" ;  
    }else{
          echo $item . "\r\n";
    }

}
