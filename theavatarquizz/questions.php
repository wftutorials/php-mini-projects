<?php
include 'functions.php';

if($_SERVER["REQUEST_METHOD"] == 'POST' && isset($_POST['save-question'])){

    $question = isset($_POST['question']) ? $_POST['question'] : null;
    $answers = isset($_POST['answers']) ? $_POST['answers'] : [];
    $correctResponses = isset($_POST['correct']) ? $_POST['correct'] :  [];
    if($question){
        $id = save_question($question);
        $count = 0;
        foreach ($answers as $answer){
            $isCorrect = ($correctResponses[$count] == 'yes') ? 1 : 0;
            save_answer($id, $answer, $isCorrect );
            $count++;
        }
        echo 'Question Saved: ID is: '. $id;
    }
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Avatar: The last Air Bender Quiz - Questions</title>
    <script
            src="https://code.jquery.com/jquery-3.5.1.min.js"
            integrity="sha256-9/aliU8dGd2tb6OSsuzixeV4y/faTqgFtohetphbbj0="
            crossorigin="anonymous"></script>
</head>
<body>
<h3>Enter your questions</h3>
<p>Start the quiz now <a target="_blank" href="quiz.php">START NOW!!!</a></p>
<form method="post">
Question: <input name="question" type="text" autocomplete="off"/>
<br><br>
    <b>Answers</b> - <a href="javascript:void(0)" id="add-answer">Add new</a>
<div id="answers">

</div>

<br>

<button type="submit" name="save-question">Save Question</button>

</form>

<h3>Questions</h3>
<?php foreach(get_all_questions() as $question):?>
<div style="border-radius: 3px; border: 1px #ccc solid; padding: 5px; margin: 3px;">
    <p>(<?php echo $question["id"];?>) <?php echo $question["question"];?></p>
    <ul>
        <?php foreach(get_all_answers($question["id"]) as $arow):?>
            <li><?php echo $arow["answer"];?>
                <?php if($arow["correct"] == "1"){
                    echo " <span style='color:darkgreen;'>(Correct)</span>";
                };?>
            </li>
        <?Php endforeach;?>
    </ul>
</div>

<?php endforeach; ?>

<script>
    $(document).ready(function(){

        var answerLayout = '<div>Answer: <input name="answers[]" />' +
            '<select name="correct[]">' +
            '<option value="no">No</option><option value="yes">Yes</option></select>' +
            '</div>';


        $('#add-answer').on('click', function(){
            $('#answers').append(answerLayout);
            return false;
        });

    });


</script>
</body>
</html>