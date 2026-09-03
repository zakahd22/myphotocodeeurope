<?php
include '../sessio.php';
include '../conexio.php';
?>
<div id='inicio' style='min-height:680px;'>
    <div id='question'>
        <?php
        $VIDEOS = "";
        $IMAGES = "";
        $tryAgain = true;
        if (isset($_POST['question'])) {

            $problema = $_SESSION['enquesta'];
            //$problema = 1;
            $isSolved = false;
            $actualQuestion = $_POST['question'];
            $actualSolution = $_POST['solution'];
            $answer = $_POST['answer'];
            $CLD_CON2 = clone($CLD_CON);
            /* Ens indicara si la pregunta actual era una pregunta o una solució  false=solucio true=question */

            if ($actualQuestion == 0) {
                $isActualQuestion = false;
            } else {
                $isActualQuestion = true;
            }



            // si la answer es zero significa que va a busca les primera pregunta. Si no va a busca la següent
            if ($answer != 0) {
                //Si Actual es una question guarda la resposta i va a mira que té de mostra segons la resposta donada
                if ($isActualQuestion) {
                    $CLD_CON->ExecuteInsert("INSERT INTO SAT_problemsquestions (problem_id , questionSolution_id ,questionOrSolution , answer_id) VALUES ($problema , $actualQuestion , 1 , $answer)");
                    $CLD_CON->OpenRs("SELECT nextQuestion , nextSolution FROM SAT_answers WHERE id=$answer");
                    $CLD_CON->FetchArray();
                    $nextSolution = $CLD_CON->GetArrayField("nextSolution");
                    $nextQuestion = $CLD_CON->GetArrayField("nextQuestion");
                    if ($nextQuestion == 0) {
                        $isNextQuestion = false;
                    } else {
                        $isNextQuestion = true;
                    }
                }// Aixo vol dir que era una solució i segons la resposta que serà si s'ha solucionat o no mostrara una cosa o un altre.
                else {
                    $CLD_CON->ExecuteInsert("INSERT INTO SAT_problemsquestions (problem_id , questionSolution_id ,questionOrSolution , answer_id) VALUES ($problema , $actualSolution , 2 , $answer)");
                    if ($answer == 2) {
                        $CLD_CON->Execute("UPDATE SAT_problems SET solved=1 , solution=$actualSolution WHERE id =$problema");
                        $isSolved = true;
                    } else {
                        $CLD_CON->OpenRs("SELECT nextQuestion FROM SAT_solutions WHERE id=$actualSolution");
                        $CLD_CON->FetchArray();
                        $nextQuestion = $CLD_CON->GetArrayField("nextQuestion");
                        $isNextQuestion = true;
                    }
                }
            } else {
                $isNextQuestion = true;
                $BT = $_SESSION['boothType'];
                $CLD_CON->OpenRs("SELECT question_id FROM SAT_firstquestion WHERE  boothType = '$BT'");
                if ($CLD_CON->FetchArray()) {
                    $nextQuestion = $CLD_CON->GetArrayField("question_id");
                } else {
                    $nextQuestion = 1;
                }
            }

            if ($isSolved) { // A una solució ha respost que si.
                echo "<p class='questionText'>Your issue has been solved!  Please leave any comments that you may have below which in addition will help us to improve our support application. Thank you for your confidence in Digital Centre, it has been our pleasure to help you.</p>";
                echo "<textarea id='ownerComment' class='areaText'></textarea>";
                echo "<p class='questionText'> Thank you very much </p>";
                $finish =  "<input type='button' class='finish' onclick='Solved($problema , 1);'>";
                $tryAgain = false;
            } else {
                if ($isNextQuestion) { //Lo següent a mostrar es una pregunta
                    if ($nextQuestion == 999999) {
                        $tryAgain = false;
                        include './noSolvedForm.php';
                        $CLD_CON->ExecuteInsert("INSERT INTO SAT_problemsquestions (problem_id, questionSolution_id , questionOrSolution , answer_id) VALUES($problema , $nextQuestion , 3 , 999999)");
                    } else {
                        if($nextQuestion == 999998){
                            echo "<p class='questionText'>Your issue has been solved!  Please leave any comments that you may have below which in addition will help us to improve our support application. Thank you for your confidence in Digital Centre, it has been our pleasure to help you.</p>";
                             echo "<textarea id='ownerComment' class='areaText'></textarea>";
                             echo "<p class='questionText'> Thank you very much </p>";
                              $finish =  "<input type='button' class='finish' onclick='Solved($problema , 1);'>";
                                 $tryAgain = false;
                        }else{
                        $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$nextQuestion");
                        $CLD_CON->FetchArray();
                        $question_text = stripslashes($CLD_CON->GetArrayField("question"));
                        //$question_text = htmlspecialchars($question_text, ENT_QUOTES);
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
                        $CLD_CON->OpenRs("SELECT id , answer , nextQuestion , nextSolution FROM SAT_answers WHERE question_id=$nextQuestion");
                        while ($CLD_CON->FetchArray()) {
                            $answer_text = stripslashes($CLD_CON->GetArrayField("answer"));
                            $answer_text = htmlspecialchars($answer_text, ENT_QUOTES);
                            $answer_id = $CLD_CON->GetArrayField("id");
                            $nQ = $CLD_CON->GetArrayField("nextQuestion");
                            $nS = $CLD_CON->GetArrayField("nextSolution");
                            if ($nQ != 0 || $nS != 0) {
                                /*  echo "<p class='answerText'>";
                                  echo "<input type='radio' name='answer' value='$answer_id'>$answer_text";
                                  echo "</p>"; */

                                echo "<input type='button' onclick='nextQuestion($answer_id)' value='$answer_text' class='answer'>";
                            }
                        }
                        echo "</p>";
                        $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE question=$nextQuestion AND  tipo=0");
                        if ($CLD_CON2->FetchArray()) {
                            $ruta = $CLD_CON2->GetArrayField("ruta");
                            $VIDEOS .= "<img src='../images/buttons/videoH.png' class='videos' onClick='popupVideo(\"$ruta\")'>";
                        } else {
                            $VIDEOS .= "<img src='../images/buttons/videoNA.png' class='videos'>";
                        }
                        $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE question=$nextQuestion AND  tipo=1");

                        if ($CLD_CON2->GetRsRows() > 0) {
                            if ($CLD_CON2->FetchArray()) {
                                $ruta = $CLD_CON2->GetArrayField("ruta");
                                $IMAGES .= "<img src ='$ruta' onclick='bigImage(1,$nextQuestion)' class='imgH' style='cursor:pointer;'>";
                            }
                        } else {
                            $IMAGES .= "<img src ='../images/buttons/imgH.png'  class='imgH'>";
                        }
                        $last = "<input type='button' class='lastQuestion' onclick='lastQuestion()'>";
                        //   echo "<input type='button' class='nextButton' onclick='nextQuestion();' value='NEXT'>";
                        echo "<input type='hidden' id='actualQuestion' value='$nextQuestion'>";
                        echo "<input type='hidden' id='actualSolution' value='0'>";
                    }
                    }
                } else { // Lo seguent a mostrar es una solució
                    $CLD_CON->OpenRs("SELECT solution FROM SAT_solutions WHERE id=$nextSolution");
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
                    /* echo "<p class='answerText'><input type='radio' name='answer' value='2'>Yes</p>";
                      echo "<p class='answerText'><input type='radio' name='answer' value='1'>No</p>"; */
                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE solution=$nextSolution AND  tipo=0");
                    if ($CLD_CON2->FetchArray()) {
                        $ruta = $CLD_CON2->GetArrayField("ruta");
                        $VIDEOS .= "<img src='../images/buttons/videoH.png' class='videos' onClick='popupVideo(\"$ruta\")'>";
                    } else {
                        $VIDEOS .= "<img src='../images/buttons/videoNA.png' class='videos'>";
                    }
                    $CLD_CON2->OpenRs("SELECT ruta FROM SAT_media WHERE solution=$nextSolution AND  tipo=1");
                    if ($CLD_CON2->GetRsRows() > 0) {
                        if ($CLD_CON2->FetchArray()) {
                            $ruta = $CLD_CON2->GetArrayField("ruta");
                            $IMAGES .= "<img src ='$ruta' onclick='bigImage(2,$nextSolution)' class='imgH'>";
                        }
                    } else {
                        $IMAGES .= "<img src ='../images/buttons/imgH.png' class='imgH'>";
                    }

                    $last = "<input type='button' class='lastQuestion' onclick='lastQuestion()'>";
                    // echo "<input type='button' class='nextButton' onclick='nextQuestion();' value='NEXT'>";
                    echo "<input type='hidden' id='actualQuestion' value='0'>";
                    echo "<input type='hidden' id='actualSolution' value='$nextSolution'>";
                }
            }
        } else {
            echo "fail";
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
        if ($tryAgain) {
            ?>
            <button onclick="tryAgain();"  class='tryAgain'></button>
            <?php
        } if(isset($finish)){
        echo $finish;
        }
            ?>
            </div>
</div>
<p id="errors">

</p>
<!--<div id="helpVideos"><center>HELP VIDEOS</center><?php echo $VIDEOS; ?></div>
<div id="helpIMG"><center>HELP IMAGES</center><?php echo $IMAGES; ?></div>-->