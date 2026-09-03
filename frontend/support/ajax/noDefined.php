<?php
include '../sessio.php';
include '../conexio.php';
$answer_id = $_POST['answer']; 
$questionID = $_POST['question'];
$CLD_CON->Execute("UPDATE SAT_answers SET nextQuestion = 0 , nextSolution=0 WHERE id=$answer_id");
include 'refreshAnswers.php';
?>
