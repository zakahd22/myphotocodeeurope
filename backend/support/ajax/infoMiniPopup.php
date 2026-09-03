<?php

include "../sessio.php";
include '../conexio.php';
$QorS = $_POST['QorS'];
switch($QorS){
    case 0 :
        $QuestionID = $_POST['id'];
        $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$QuestionID");
        if ($CLD_CON->FetchArray()) {
            $question_text = stripslashes($CLD_CON->GetArrayField("question"));
            echo "<p class='title2' style='margin-top:20px;'>Next Question</p>";
            echo "<p class='text'>$question_text</p>";
        }
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(1)'>Set assigned question or solution</span><p>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='noDefined()'>Set to no defined</span><p>";
        echo "<p class='text' id='QorS'></p>";
    break;
    case 1:
         $solucioID = $_POST['id'];
        $CLD_CON->OpenRs("SELECT solution FROM SAT_solutions WHERE id=$solucioID");
        if ($CLD_CON->FetchArray()) {
            $solution_text = stripslashes($CLD_CON->GetArrayField("solution"));
            echo "<p class='title2' style='margin-top:20px;'>Next Solution</p>";
            echo "<p class='text'>$solution_text</p>";
        }
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(1)'>Set assigned question or solution</span><p>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='noDefined()'>Set to no defined</span><p>";
        echo "<p class='text' id='QorS'></p>";
    break;
    case 2: 
        echo "<p class='title2' style='margin-top:30px;'>Already is not defined the next step of this answer</p>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(1)'>Assign question or solution</span><p>";
        echo "<p class='text' id='QorS'></p>";    
    break;
    case 3:
       $nextQuestionID = $_POST['id'];
     echo "<p class='title2'>Next Question</p>";
    if ($nextQuestionID == 999999) {
        echo "<p class='text'>Unsolved questionarie.</p>";
        
    } else {
        $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$nextQuestionID");
        if ($CLD_CON->FetchArray()) {
            $nextQuestion_text = stripslashes($CLD_CON->GetArrayField("question"));
            echo "<p class='text'>$nextQuestion_text</p>";          
        }
    }
     echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='AjaxOpenQorSList(2)'>Set assigned question</span><p>";
      echo "<p class='text' id='QorS'></p>";  
    break;
    case 4:
        echo "<p class='title2'>New Answer</p>";
        echo "<textarea id='textAreaAnswer'></textarea>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='addNewAnswer();'>Add new answer</span></p>";
    break;
    case 5 :
        echo "<p class='title2'>Set to solved</p>";
        echo "<p class='text'>Writes as has solved the problem?</p>";
        echo "<textarea id='txtMiniPopup' class='txtAreaPop'></textarea>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='setToSolved();'>Set to solved</span></p>";
    break;
    case 6:
        $QS = $_POST['QoS'];
        $QSID = $_POST['QS'];
        
        if($QS == 0){
             echo "<p class='title2'>Edit Question</p>";
             $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id= $QSID");
             if($CLD_CON->FetchArray()){
                 $text = $CLD_CON->GetArrayField("question");                 
             }
        }
        if($QS==1){
            echo "<p class='title2'>Edit Solution</p>";
            $CLD_CON->OpenRs("SELECT solution FROM SAT_solutions WHERE id= $QSID");
             if($CLD_CON->FetchArray()){
                 $text = $CLD_CON->GetArrayField("solution");                 
             }
        }
        echo "<textarea id='txtEdit'>$text</textarea>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='updateQS($QSID , $QS);'>Save changes</span></p>";
       
    break;
    case 7:
        $QSID = $_POST['QS'];
        echo "<p class='title2'>Edit Answer</p>";
             $CLD_CON->OpenRs("SELECT answer FROM SAT_answers WHERE id= $QSID");
             if($CLD_CON->FetchArray()){
                 $text = $CLD_CON->GetArrayField("answer");                 
         }
        echo "<textarea id='txtEdit'>$text</textarea>";
        echo "<p class='title2' style='text-align:center;'><span style='color:yellowgreen;cursor:pointer;text-shadow:1px 1px 1px green;' onclick='updateAnswer($QSID);'>Save changes</span></p>";
    break;
}
?>
<p id='errPOP'></p> 