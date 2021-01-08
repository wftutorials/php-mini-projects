<?php

$last = null;
$message = "";
$defaultboard = [
    [3,3,3],
    [3,3,3],
    [3,3,3]
];

$board = [
   [3,3,3],
   [3,3,3],
  [3,3,3]
];

$win_conditions = [
    [[0,0],[0,1],[0,2]],
    [[1,0],[1,1],[1,2]],
    [[2,0],[2,1],[2,2]],
    [[0,0],[1,0],[2,0]],
    [[0,1],[1,1],[2,1]],
    [[0,2],[1,2],[2,2]],
    [[0,0],[1,1],[2,2]],
    [[0,2],[1,1],[2,0]],
];

function create_tile($value, $id){
    $o = "";
    $name = "row_". $id;
    $realValue = null;
    if($value == 0){
        $realValue = "O";
    }else if($value == 1){
        $realValue = "X";
    }else{
        $realValue = "select";
    }
    if($value == 0 || $value == 1){
        $o .= "<input type='hidden' name='".$name."' value='".$realValue."'/>";
        $o .= "<select disabled='disabled'>";
    }else{
        $o .= "<select name='".$name."'>";
    }
    $o .= "<option>select</option>";
    if($value == 0){
        $o .= "<option selected='selected'>O</option>";
    }else{
        $o .= "<option>O</option>";
    }
    if($value ==1){
        $o .= "<option selected='selected'>X</option>";
    }else{
        $o .= "<option>X</option>";
    }
    $o .= "</select>";
    return $o;
}

function check_winner($conditions, $response){
    $lr = null;
    $matches = [];
    for($i=0; $i<=count($conditions) - 1; $i++){
        foreach ($conditions[$i] as $rows){
            $x = $rows[0];
            $y = $rows[1];
            if($response[$x][$y] !=3){
                if($lr == $response[$x][$y] || $lr == null){
                    $matches[] = $response[$x][$y];
                    $lr = $response[$x][$y];
                }else{
                    $lr = null;
                    $matches = [];
                    continue;
                }
            }
        }
        if(count($matches) == 3){
            if($matches[0] == $matches[1] && $matches[1]== $matches[2]){
                return true;
            }else{
                $matches = [];
                $lr = null;
            }
        }else{
            $matches = [];
            $lr = null;
        }
    }
    return false;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['start'])){
    $board = $defaultboard;
}

if($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['play'])){
    $board = isset($_POST['board']) ? json_decode($_POST['board']) : [];
    $last = isset($_POST['last']) ? $_POST['last'] : null;
    $responses = [];
    $rowarray = [];
    $counter = 0;

    foreach ($_POST as $key=>$value){
        if(!in_array($key,["board","play",'last'])){
            if($value == 'O'){
               // echo $value;
                $rowarray[] = 0;
            }else if($value == 'X'){
                $rowarray[]= 1;
            }else{
                $rowarray[] = 3;
            }
            $counter++;
            if($counter % 3 == 0){
                $responses[] = $rowarray;
                $rowarray = [];
            }
        }
    }

    $changes = [];
    for($i =0; $i<=count($board) -1; $i++){
        foreach ($board[$i] as $key=>$value){
            if($value != $responses[$i][$key]){
                $changes[] = $responses[$i][$key];
            }
        }
    }

    if(count($changes) > 1){
        $message .= "Cant play more than once";
    }else if($last !=  null  && $last == $changes[0]){
        $message .= "You cannot play twice";
    }else if(check_winner($win_conditions, $responses)){
        $last = $changes[0];
        $winner = null;
        if($last == 1){
            $winner = "X";
        }else if($last == 0){
            $winner = "O";
        }
        $board = $responses;
        $message .= 'WE HAVE A WINNER!  ';
        $message .= "THE WINNER IS :" .  $winner;
    }else{
        $last = $changes[0];
        $board = $responses;
    }

}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tic Tac Toe</title>
</head>
<style>
    body {
        background: #ccc;
        text-align: center;
    }

    h3{
        font-size: 50px;
    }
    .tct-container {
        min-height: 500px;
        margin: 0 auto;
        display: inline-block;
        width: 100%;
        text-align: center;
    }
    .tct-table{
        border-collapse: separate;
    }

    .tct-table th, td {
        padding: 20px;
    }

    .tct-form {
        background: crimson;
        border: 1px solid #ccc;
        margin: 0 auto;
        display: inline-block;
        padding: 20px;
    }

    .tct-form button {
        font-size: 30px;
    }

    .tct-form select {
        font-size: 50px;
    }

    .tct-message {
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        font-size: 30px;
        background: #f6f6f6;
        font-weight: bold;
        border: 1px solid #ccc;
        border-radius: 3px;
        padding: 20px;
        color: darkred;
    }
</style>
<body>
<h3>Tic Tac Toe Games</h3>
<div class="tct-container">
    <form method="post" class="tct-form">
        <?php if($message):?>
            <p class="tct-message"><?php echo $message;?></p>
        <?php endif;?>
        <button type="submit" name="start">Start New Game</button><br><br>
        <input name="board" type="hidden" value="<?php echo json_encode($board);?>"/>
        <input name="last" type="hidden" value="<?php echo $last;?>"/>
        <table class="tct-table" border="1">
            <?php $count=1; foreach($board as $row): ?>

                <tr>
                    <?php foreach($row as $tile):?>
                        <td>
                            <?php echo create_tile($tile, $count);?>
                        </td>
                        <?php $count++; endforeach;?>
                </tr>

            <?php endforeach;?>
        </table>
        <br>
        <button name="play">End Turn</button>
    </form>
</div>

</body>
</html>
