<?php
include '../sessio.php';
include '../conexio.php';

if (isset($_SESSION['enquesta'])) {
    $x = false;
    $enquesta = $_SESSION['enquesta'];
    $CLD_CON2 = clone($CLD_CON);
    $CLD_CON->OpenRs("SELECT id , questionOrSolution ,  questionSolution_id FROM SAT_problemsquestions WHERE problem_id=$enquesta AND questionSolution_id!=999999 ORDER BY id DESC LIMIT 1");
    if ($CLD_CON->FetchArray()) {
        $QorS = $CLD_CON->GetArrayField("questionOrSolution");
        $QS_ID = $CLD_CON->GetArrayField("questionSolution_id");
        $FieldID = $CLD_CON->GetArrayField("id");
        $CLD_CON2->Execute("DELETE FROM SAT_problemsquestions WHERE id=$FieldID");
        $x = true;
    }

    if ($x) {
        ?>
        <div id='inicio' style='min-height:680px;'>
            <div id='question'>
                <?php
                if ($QorS == 1) { // Es una pregunta.
                    $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$QS_ID");
                    $CLD_CON->FetchArray();
                    $question_text = stripslashes($CLD_CON->GetArrayField("question"));
                    list($instrucctions, $question) = explode("###", $question_text);
                    if(!empty($instrucctions)){
                        echo "<div class='instrucctions'>";
                        echo "<img src='https://myphotocode.com/support/images/titles/ins_boto.png' class='insTitle'>";
                        echo "<p>$instrucctions</p>";
                        echo "<input type='button' value='next' onclick='showQuestion()' class='nextInst'>";
                        echo "<input type='button' value='next' onclick='showInstructtions()' class='showIns'>";
                        echo "</div>";
                        $sty2 = "display:none;";
                        }
                        echo "<p class='questionText' style='$sty2'>$question</p>";
                        echo "<p class='responses' style='$sty2'>";
                    $CLD_CON->OpenRs("SELECT id , answer , nextQuestion , nextSolution FROM SAT_answers WHERE question_id=$QS_ID");
                    while ($CLD_CON->FetchArray()) {
                        $answer_text = utf8_encode($CLD_CON->GetArrayField("answer"));
                        $answer_text = htmlspecialchars($answer_text, ENT_QUOTES);
                        $answer_id = $CLD_CON->GetArrayField("id");
                        $nQ = $CLD_CON->GetArrayField("nextQuestion");
                        $nS = $CLD_CON->GetArrayField("nextSolution");

                        if ($nQ != 0 || $nS != 0) {
                            echo "<input type='button' onclick='nextQuestion($answer_id)' value='$answer_text' class='answer'>";
                        }
                    }
                    echo "</p>";

                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE question=$QS_ID AND  tipo=0");
                    if ($CLD_CON2->FetchArray()) {
                        $ruta = $CLD_CON2->GetArrayField("ruta");
                        $VIDEOS .= "<img src='../images/buttons/videoH.png' class='videos' onClick='popupVideo(\"$ruta\")'>";
                    } else {
                        $VIDEOS .= "<img src='../images/buttons/videoNA.png' class='videos'>";
                    }

                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE question=$QS_ID AND  tipo=1");
                    if ($CLD_CON2->GetRsRows() > 0) {
                        if ($CLD_CON2->FetchArray()) {
                            $ruta = $CLD_CON2->GetArrayField("ruta");
                            $IMAGES .= "<img src ='$ruta' onclick='bigImage(1,$QS_ID)' class='imgH'>";
                        }
                    } else {
                        $IMAGES .= "<img src ='../images/buttons/imgH.png'  class='imgH'>";
                    }

                    $last = "<input type='button' class='lastQuestion' onclick='lastQuestion()'>";
                    //echo "<input type='button' class='nextButton' onclick='nextQuestion();' value='NEXT'>";
                    echo "<input type='hidden' id='actualQuestion' value='$QS_ID'>";
                    echo "<input type='hidden' id='actualSolution' value='0'>";
                } else { //Es una solució
                    $CLD_CON->OpenRs("SELECT solution FROM SAT_solutions WHERE id=$QS_ID");
                    $CLD_CON->FetchArray();
                    $solution_text = stripslashes($CLD_CON->GetArrayField("solution"));
                    list($solution_t, $questionS) = explode("###", $solution_text);
                                        if(!empty($solution_t)){
                        echo "<div class='instrucctions'>";
                        echo "<img src='https://myphotocode.com/support/images/titles/ins_boto.png' class='insTitle'>";
                        echo "<p>$solution_t</p>";
                        echo "<input type='button' value='next' onclick='showQuestion()' class='nextInst'>";
                        echo "<input type='button' value='next' onclick='showInstructtions()' class='showIns'>";
                        echo "</div>";
                        $sty = "display:none;";
                    }              
                    
                    echo "<p class='questionText' style='$sty'> $questionS</p>";
                    echo "<p class='responses' style='$sty'>";
                    echo "<input type='button' onclick='nextQuestion(2)' value='Yes' class='answer'>";
                    echo "<input type='button' onclick='nextQuestion(1)' value='No' class='answer'>";
                    echo "</p>";
                    // echo "<p class='answerText'><input type='radio' name='answer' value='2'>Yes</p>";
                    //echo "<p class='answerText'><input type='radio' name='answer' value='1'>No</p>";
                    // echo "<br>";
                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE solution=$QS_ID AND  tipo=0");
                    if ($CLD_CON2->FetchArray()) {
                        $ruta = $CLD_CON2->GetArrayField("ruta");
                        $VIDEOS .= "<img src='../images/buttons/videoH.png' class='videos' onClick='popupVideo(\"$ruta\")'>";
                    } else {
                        $VIDEOS .= "<img src='../images/buttons/videoNA.png' class='videos'>";
                    }
                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE solution=$QS_ID AND  tipo=1");
                    if ($CLD_CON2->GetRsRows() > 0) {
                        if ($CLD_CON2->FetchArray()) {
                            $ruta = $CLD_CON2->GetArrayField("ruta");
                            $IMAGES .= "<img src ='$ruta' onclick='bigImage(1,$QS_ID)' class='imgH ajustat'>";
                        }
                    } else {
                        $IMAGES .= "<img src ='../images/buttons/imgH.png'  class='imgH'>";
                    }
                    $last = "<input type='button' class='lastQuestion' onclick='lastQuestion()'>";
                    // echo "<input type='button' class='nextButton' onclick='nextQuestion();' value='NEXT'>";
                    echo "<input type='hidden' id='actualQuestion' value='0'>";
                    echo "<input type='hidden' id='actualSolution' value='$QS_ID'>";
                }
                ?>
            </div>
        

        <div id="help">
            <img src="../images/titles/videoT.png" class="helpTitle">
            <?php echo $VIDEOS; ?>
            <img src="../images/titles/imgT.png" class="helpTitle">
            <?php echo $IMAGES; ?>
        </div>
        <div id="lastAgain">

            <?php
            echo $last;
            ?>
            <button onclick="tryAgain();"  class='tryAgain'></button>

        </div>

        <p id="errors">

        </p>
        </div>
        <?php
    } else {

        $CLD_CON->Execute("DELETE FROM SAT_problems WHERE id=$enquesta");
        echo 0;
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
         