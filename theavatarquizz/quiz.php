<?php
include 'functions.php';
$cursor = null;

if($_SERVER["REQUEST_METHOD"] ==  "POST" && isset($_POST['quiz']) ){

    $action = $_POST['quiz'];
    if($action == 'start'){
        clear_response();
        $cursor = 0;
    }else if($action =="continue"){
        $cursor = isset($_POST['cursor']) ? $_POST['cursor'] : 0;
        $cursor++;
    }else if($action == 'startover'){
        $cursor = null;
        $response = null;
    }
    $ques = isset($_POST['question']) ? $_POST['question'] : null;
    $resp = isset($_POST['response']) ? $_POST['response'] : null;
    if($resp && $ques){
        save_response($ques, $resp);
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AVATAR - THE LAST AIR BENDER QUIZ - START NOW</title>
</head>
<body>
<h3>Start the quiz</h3>
<form method="post">
    <input type="hidden" name="cursor" value="<?php echo $cursor;?>" />
    <?php
    $response = null;

    if($cursor !== null){
        $response = get_next_question($cursor);
        if(count($response) > 0){
            $nextQuestion = $response[0];
        }else{
            $response = null;
        }
        if($response != null){
            echo "<p>" . $nextQuestion["question"] . "</p>";
            echo "<input type='hidden' name='question' value='".$nextQuestion["id"]."'/>";
            foreach (get_all_answers($nextQuestion["id"]) as $answer){
                echo "
                 <input type='radio' name='response' value='".$answer["id"]."'/>
                 <label>". $answer["answer"] . "</label><br>
                ";
            }
            echo "<br>";
        }
    }
    ?>
    <?php if($response === null && $cursor > 0):?>
        <p>Hurray! Your quiz is complete. View your results. <a target="_blank" href="results.php">RESULTS HERE!!!</a></p>
        <br><br>
        <button type="submit" name="quiz" value="startover">Start Over</button>
    <?php endif;?>
    <?php if($response != null):?>
    <button type="submit" name="quiz" value="continue">Continue</button>
    <?php endif;?>

    <?php if($cursor === null):?>
    <button type="submit" name="quiz" value="start">Start this quiz</button>
    <?Php endif;?>

</form>

</body>
</html>