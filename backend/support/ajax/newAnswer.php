<?php
include '../sessio.php';
include '../conexio.php';
$questionID = $_POST['question'];
$answer = addslashes($_POST['answer']);
$CLD_CON->ExecuteInsert("INSERT INTO SAT_answers (answer,question_id) VALUES('$answer',$questionID) ");
   
include 'refreshAnswers.php';

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
