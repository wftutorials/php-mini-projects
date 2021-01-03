<?php
include 'functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hurray You Completed the Last Air Bender Quiz - AVATAR</title>
</head>
<body>
<h3>Congratulations!!!</h3>
<p><i>Lets see your results</i></p>
<?php foreach(get_responses() as $response):?>

<p>
    <b><?php echo $response["question"];?></b><br>
    <?php if($response["correct"] == '1'):?>
    <span style="color:darkgreen"><?php echo $response["answer"];?>  ( You answered correctly)</span>
    <?php else: ?>
        <span style="color:darkred"><?php echo $response["answer"];?></span>
        <?php
            $correctAnswer = get_correct_answer($response["id"]);
            if($correctAnswer){
                echo "<br><span style='color: darkgreen'>" . $correctAnswer[0]["answer"]
                    . " (Is the correct answer) </span>";
            }

        ?>
    <?php endif;?>


</p>

<?php endforeach;?>

</body>
</html>
