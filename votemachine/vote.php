<?php

$cacheA = 0;
$cacheB = 0;


$candidates = [
    '1' => [
      'title' => 'Party A',
      'color' => 'red',
      'tag' => 'party_a',
      'headline' => 'Party to liberate PHP',
      'img' => 'https://www.drodd.com/images16/red-letters5.png'
    ],
    '2' => [
        'title' => 'Party B',
        'color' => 'blue',
        'tag' => 'party_b',
        'headline' => 'Party to liberate JAVA',
        'img' => 'https://www.shareicon.net/data/512x512/2016/10/20/846458_blue_512x512.png'
    ],
];

function getLabels($candidates){
    $data = [];
    foreach ($candidates as $candidate){
        $data[] = $candidate["title"];
    }
    return json_encode($data);
}

function getColors($candidates){
    $data = [];
    foreach ($candidates as $candidate){
        $data[] = $candidate["color"];
    }
    return json_encode($data);
}


if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['cast'])){

    $vote = isset($_POST['vote']) ? $_POST['vote'] : null;
    $cacheA = isset($_POST['cache_a']) ? $_POST['cache_a'] : 0;
    $cacheB = isset($_POST['cache_b']) ? $_POST['cache_b'] : 0;

    if($vote =='party_a'){
        $cacheA++;
    }elseif($vote == 'party_b'){
        $cacheB++;
    }
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear'])){
    $cacheA = 0;
    $cacheB = 0;
}



?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Voting Machine</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.css" integrity="sha512-C7hOmCgGzihKXzyPU/z4nv97W0d9bv4ALuuEbSf6hm93myico9qa0hv4dODThvCsqQUmKmLcJmlpRmCaApr83g==" crossorigin="anonymous" />

    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.4/Chart.min.js" integrity="sha512-d9xgZrVZpmmQlfonhQUvTR7lMPtO7NkZMkA0ABN3PHCbKA5nqylQ/yWlFAyY6hYgdF1Qh6nYiuADWwKB4C2WSw==" crossorigin="anonymous"></script>

</head>
<body>
<h3>Vote Now!</h3>
<form method="post">
    <?php foreach($candidates as $candidate):?>
        <div style="border:1px solid #ccc; border-radius: 3px; padding: 5px; margin-bottom: 5px;">
            <img src="<?php echo $candidate["img"];?>" width="25px"  height="25px">
            <input type="radio" name="vote" value="<?php echo $candidate["tag"];?>"/>
            <label><?php echo $candidate["title"];?></label><br>
            <p><?php echo $candidate["headline"];?></p>
        </div>
    <?php endforeach;?>
    <br>
    <input name="cache_a" type="hidden" value="<?php echo $cacheA;?>"/>
    <input name="cache_b" type="hidden" value="<?php echo $cacheB;?>"/>
    <button name="cast" type="submit">Cast Vote</button>
    <button name="clear" type="submit">Clear Ballots</button>
</form>
<br>
<h3>Results</h3>
<p><?php
        $total = $cacheA + $cacheB;
        echo "Total Votes: " . $total;
    ?>
</p>
<div style="height: 300px;">
    <canvas id="myChart"></canvas>

</div>
<script>
    var ctx = $('#myChart');
    var myPieChart = new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo getLabels($candidates);?>,
            datasets: [{
                data: [<?php echo $cacheA;?>, <?php echo $cacheB;?>],
                backgroundColor: <?php echo getColors($candidates);?>,
            }]
        },
        options: {
            lengend: {
                display: true
            }
        }
    });
</script>
</body>
</html>