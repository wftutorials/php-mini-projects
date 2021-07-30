<?php

include "QueueManager.php";

$queue = new QueueManager();
$queue->addToQueue("hello");
$queue->addToQueue("hello2");
$queue->addToQueue("hello3");
$queue->addToQueue("hello4");
