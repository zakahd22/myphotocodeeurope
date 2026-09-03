<?php
 $CLD_CON->OpenRs("SELECT * FROM SAT_answers WHERE question_id=$questionID");
    while($CLD_CON->FetchArray()){
        $answer = stripslashes($CLD_CON->GetArrayField("answer"));
        $answer_id = $CLD_CON->GetArrayField("id");
        $nextQuestion = $CLD_CON->GetArrayField("nextQuestion");
        $nextSolution = $CLD_CON->GetArrayField("nextSolution");
        echo "<ul class='secundaria'>";
        echo "<li style='width:70%;'>$answer<li>";
        if($nextQuestion==0 && $nextSolution==0){
            echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(2,$nextQuestion , $answer_id, $questionID)' class='spanPointer'>No defined</span></li>";
        }else{
        if($nextQuestion!=0){
             echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(0,$nextQuestion , $answer_id , $questionID)' class='spanPointer'>Next : Question</span></li>";
        }else{
            echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(1,$nextSolution , $answer_id , $questionID)' class='spanPointer'>Next : Solution</span></li>";
        }
        }
         echo "</ul>";
    }
    echo "<ul class='secundaria'><li style='width:70%;'>----------------------------------<li><li><span onclick='nextSolutionOrQuestion(4 , 0 , 0 , $questionID);' class='spanPointer'>Add answer</span></li></ul>";

?>
