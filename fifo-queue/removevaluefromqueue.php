<?php


include "queue.php";

$lastItem = getFirstItem();

if(empty($lastItem)){
    echo "Queue is empty";
}else{
    echo $lastItem;
}