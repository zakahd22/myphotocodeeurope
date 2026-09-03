<?php
include "../sessio.php";
include '../conexio.php';
$answers = $_POST['answers'];
$question = addslashes($_POST['question']);
$question_id= $CLD_CON->ExecuteInsert("INSERT INTO SAT_questions (question , type) VALUES('$question',0)");
echo $question_id;
foreach ($answers as $answer){
    $a = addslashes ($answer);
    $CLD_CON->ExecuteInsert("INSERT INTO SAT_answers (answer , question_id, nextQuestion , nextSolution) VALUES('$a',$question_id , 0 , 0)");
}
?>
