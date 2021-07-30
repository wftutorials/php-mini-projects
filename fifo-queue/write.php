<?php

include "queue.php";

for($i=1; $i<=35; $i++){
    sleep(1);
    addToQueue("Hell_world_" . $i);
}