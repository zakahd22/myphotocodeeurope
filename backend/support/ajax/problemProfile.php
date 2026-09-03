<?php
include '../sessio.php';
include '../conexio.php';
$problemID = $_POST['problemId'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON4 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT p.id as IDN , p.dataTiempo, p.comment , p.solved, p.propietari , p.booth_id ,  p.solution , p.info , p.solution_text2 , bt.name as nombre FROM SAT_problems p LEFT JOIN booth_types bt ON bt.char = p.boothType  WHERE p.id=$problemID");
if ($CLD_CON->FetchArray()) {
    
    $data = $CLD_CON->GetArrayField("dataTiempo");
    $boothType = $CLD_CON->GetArrayField("nombre");
    $userComment = stripslashes($CLD_CON->GetArrayField("comment"));
    $solved = $CLD_CON->GetArrayField("solved");
    $solution = $CLD_CON->GetArrayField("solution");
    $info = stripslashes($CLD_CON->GetArrayField("info"));
	$ownerID = $CLD_CON->GetArrayField("propietari");
	$boothID = $CLD_CON->GetArrayField("booth_id");	
    $solution_text = stripslashes($CLD_CON->GetArrayField("solution_text2"));
	
    echo "<span class='left'>";
    echo "<input type='button' class='back' value='BACK' onclick='to(\"oldQuestionaris.php\")'>";
    if($solved == 0 && $_SESSION['USERID']==9999991){
        echo "<input type='button' class='back' value='SET TO SOLVED' onclick='nextSolutionOrQuestion(5, 0, 0, 0)'>";
    }
    echo "</span>";
    echo "<p class='title1'>";
    echo "$data - $boothType";
    if ($solved != 0) {
        echo "<span class='right green'>SOLVED</span>";
    } else {
        echo "<span class='right' style='color:red;'>UNSOLVED</span>";
    }
    echo"</p>";
	echo "<div style='width:50%;display:inline;height:500px;float:right;'>";
    echo "<div class='boxright'  style='min-height:10%;width:90%;margin-right:9%;'>";
    $CLD_CON2->OpenRs("SELECT questionSolution_id , questionOrSolution, answer_id FROM SAT_problemsquestions WHERE problem_id=$problemID ORDER BY id");
    $i = 1;
    $s = 1;
    while ($CLD_CON2->FetchArray()) {
        $questionOrSolution = $CLD_CON2->GetArrayField("questionOrSolution");
        $answer_id = $CLD_CON2->GetArrayField("answer_id");
        $questionSolution_id = $CLD_CON2->GetArrayField("questionSolution_id");
        if ($questionSolution_id == 999999) {
            echo "<p class='title2'>Unsolved questionarie.</p>";
           
        } else {
            if ($questionOrSolution == 1) {
                echo "<p class='title2'>Question $i : </p>";
                $CLD_CON3->OpenRs("SELECT question FROM SAT_questions WHERE id = $questionSolution_id");
                if ($CLD_CON3->FetchArray()) {
                    $question = utf8_encode(stripslashes($CLD_CON3->GetArrayField("question")));
                }
                $CLD_CON4->OpenRs("SELECT answer FROM SAT_answers WHERE id = $answer_id");
                if ($CLD_CON4->FetchArray()) {
                    $answer = stripslashes(utf8_encode($CLD_CON4->GetArrayField("answer")));
                }
                $i++;
            } else {
                echo "<p class='title2'>Solution $s : </p>";
                $CLD_CON3->OpenRs("SELECT solution FROM SAT_solutions WHERE id = $questionSolution_id");
                if ($CLD_CON3->FetchArray()) {
                    $question = utf8_encode(stripslashes($CLD_CON3->GetArrayField("solution"))) . ". The problem has been solved?";
                }
                if ($answer_id == 1) {
                    $answer = "No";
                } else {
                    $answer = "Yes";
                }
                $s++;
            }
            echo "<p class='text'>$question</p>";
            echo "<p class='title2'>Answer :</p>";
            echo "<p class='text'>$answer</p>";
            echo "<hr>";
        }
        
        
    }
        
    echo "<input type='hidden' id='problemID' value='$problemID'>";
    echo "</div>";
    echo "<div class='boxright' style='min-height:10%;width:90%;margin-right:9%;'>";
    echo "<p class='title2'> Solution : </p>";
    if ($solved < 1) {
        echo "<p style='color:red;font-size:11pt;'> No solution yet </p>";
    } else {
            echo "<p style='color:green;font-size:11pt;'>$solution_text</p>";
            $CLD_CON2->OpenRs("SELECT solution FROM SAT_solutions WHERE id = $solution");
            if ($CLD_CON2->FetchArray()) {
                echo "<p style='color:green;font-size:11pt;'>";
                echo stripslashes($CLD_CON2->GetArrayField("solution"));
                echo "</p>";
            }
        }
    
    echo "</div>";
    echo "</div>";
    
    
    
    
    echo "<div style='width:50%;display:inline;height:500px;float:left;'>";
    echo "<div class='boxright' style='height:55%;width:90%;margin-left:9%;'>";
    echo "<p class='title2'> First Comment : </p>";
    if(!empty($info)){
         echo "<p class='text'> $info </p>";
    }else{
        echo "<p class='text'> - </p>";
    }
    echo "<p class='title2'> Last Comment : </p>";
    if(!empty($userComment)){
    echo "<p class='text'> $userComment </p>";
    }else{
        echo "<p class='text'> - </p>";
    }
    echo "</div>";
	 echo "<div class='boxright' style='height:55%;width:90%;margin-left:9%;'>";
    $CLD_CON2->OpenRs("SELECT serialnumber , location FROM App_booths WHERE idBooth=$boothID");
	if($CLD_CON2->FetchArray()){
		echo "<p>Serial Number : " . $CLD_CON2->GetArrayField("serialnumber") . "</p>";
		echo "<p>Location :" . $CLD_CON2->GetArrayField("location"). "</p>";
	}
	$CLD_CON2->OpenRs("SELECT name , App_email FROM rentals WHERE id=$ownerID");
	if($CLD_CON2->FetchArray()){
		echo "<p>Owner : " . $CLD_CON2->GetArrayField("name") . "</p>";
		echo "<p>Owner Email:" . $CLD_CON2->GetArrayField("App_email"). "</p>";
	}
	
	echo "</div>";
    echo "</div>";
    /*Solucio Abans*/

}
?>



