<?php
include '../sessio.php';
include '../conexio.php';
$QorS = $_POST['QorS'];
$id = $_POST['question'];
if ($QorS==3) {        
                $boothType = $_POST['boothType'];
            }
if($QorS==1){

  
    echo "<p class='title2' style='color:white;background-color:black;margin:0;'> Solutions </p>";
    echo "<div class='llista' style='height:50%;overflow:auto;overflow-x:hidden;'>";
    $CLD_CON->OpenRs("SELECT id , solution FROM SAT_solutions");
    while($CLD_CON->FetchArray()){
        $solution_id = $CLD_CON->GetArrayField("id");
        $solution_text = stripslashes($CLD_CON->GetArrayField("solution"));
        echo "<ul class='primaria' ondblclick='assign($solution_id , 1);'>";
        echo "<li>S$solution_id - $solution_text</li>";
        echo "</ul>";
    }
     echo "<ul class='primaria' ondblclick='assign(999998 , 2);'>";
     echo "<li>Q999998 - Solved</li>";
       echo "</ul>";
    echo "</div>";
}

    echo "<p class='title2'  style='color:white;background-color:black;margin:0;'> Questions </p>";
   if($QorS==1){ 

       echo "<div class='llista' style='height:40%;overflow:auto;overflow-x:hidden;bottom:0'>";
  }else{

        echo "<div class='llista' style='height:90%;overflow:auto;overflow-x:hidden;top:0'>";
   }
    $CLD_CON->OpenRs("SELECT id , question FROM SAT_questions WHERE id!=$id");
    while($CLD_CON->FetchArray()){
        $question_id = $CLD_CON->GetArrayField("id");
        $question_text = stripslashes($CLD_CON->GetArrayField("question"));
        echo "<ul class='primaria'";
        if($QorS==1){
                echo "ondblclick='assign($question_id , 2);'>";
            }elseif($QorS==2){
                echo "ondblclick='assign($question_id , 3);'>";
            }elseif ($QorS==3) {        
                echo "ondblclick='assign($question_id , 4 , \"$boothType\");'>";
            }
        echo "<li>Q$question_id - $question_text</li>";
        echo "</ul>";
    }
 
         if($QorS==1){
               echo "<ul class='primaria' ondblclick='assign(999999 , 2);'>";
            }elseif($QorS==2){
                 echo "<ul class='primaria' ondblclick='assign(999999 , 3);'>";
            }elseif ($QorS==3) {       
                  echo "<ul class='primaria' ondblclick='assign(999999 , 4 , \"$boothType\");'>";
            }
       echo "<li>Q999999 - Unsolved Form</li>";
       echo "</ul>";
 
     echo "</div>";

?>
