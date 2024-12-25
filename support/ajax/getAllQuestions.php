<?php
include "../sessio.php";
include '../conexio.php';
if(isset($_POST['filtros'])){
    
    $x=0; $f= array();
    $leftJoin = "";
    $where = "";
    $select = "SELECT q.id , q.question ,q.type FROM SAT_questions q ";
    
    $questionF = $_POST['questionF'];
    $answerF = $_POST['answerF'];
    $numOfAnswersF = $_POST['nOfAnswers'];
    $codeQ = $_POST['code'];
    if($codeQ != 0){
        $f[$x] = "q.id=$codeQ";
        $x++;
    }
    if(!empty($questionF)){
        $f[$x]="q.question LIKE '%$questionF%'";
        $x++;
    }
    if(!empty($answerF)){
        $leftJoin = "LEFT JOIN SAT_answers a ON q.id=a.question_id";
        $f[$x]="a.answer LIKE '%$answerF%'";
        $x++;
    }
    if($numOfAnswersF !== "-1"){
        $CLD_CON->OpenRs("SELECT a.question_id , COUNT(a.id) as c FROM SAT_answers a GROUP BY question_id");
        $in = "q.id IN (";
        $noResults = false;
        while($CLD_CON->FetchArray()){
            $qID = $CLD_CON->GetArrayField("question_id");
            $qty = $CLD_CON->GetArrayField("c");
           
            if($qty==$numOfAnswersF){
                $in .= "$qID ,";
                $noResults=true;
            }
        }
        if($noResults){
            $f[$x] = substr($in, 0, -1) . ")"; 
        }else{
            $f[$x] = "1=0";
         }
         $x++;
    }
    $x2 =0;
    while($x2<$x){
        if($x2==0){
            $where .= " WHERE ";
        }
       $where .=  $f[$x2];
       if(($x2+1)<$x){
           $where .= " AND ";
       }
       $x2++;
    }
    
    $select .= $leftJoin . " " . $where;
    
}else{
    $select = "SELECT * FROM SAT_questions q";
}
$select .= " ORDER BY q.id";
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs($select);


while($CLD_CON->FetchArray()){
    $question = stripslashes($CLD_CON->GetArrayField("question"));
    $questionID= $CLD_CON->GetArrayField("id");
    echo "<ul class='primaria' onclick='openCloseAnswers($questionID)' id='q$questionID'>";
    echo "<li >Q$questionID - <span id='qst$questionID'>$question</span>";
        echo "<span onclick='editQS(6, $questionID ,0 , \"qst$questionID\")' class='spanPointer spanEdit'>Edit</span></li>";
    echo "</ul>";
    echo "<div id='question$questionID' class='hiddenAnswers'>";
    $CLD_CON2->OpenRs("SELECT * FROM SAT_answers WHERE question_id=$questionID");
    while($CLD_CON2->FetchArray()){
        $answer = stripslashes($CLD_CON2->GetArrayField("answer"));
        $answer_id = $CLD_CON2->GetArrayField("id");
        $nextQuestion = $CLD_CON2->GetArrayField("nextQuestion");
        $nextSolution = $CLD_CON2->GetArrayField("nextSolution");
        echo "<ul class='secundaria'>";
        echo "<li style='width:70%;' id='ans$answer_id'>$answer<li>";
        if($nextQuestion==0 && $nextSolution==0){
            echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(2,$nextQuestion , $answer_id, $questionID)' class='spanPointer'>No defined</span></li>";
        }else{
        if($nextQuestion!=0){
             echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(0,$nextQuestion , $answer_id , $questionID)' class='spanPointer'>Next : Q$nextQuestion</span></li>";
        }else{
            echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(1,$nextSolution , $answer_id , $questionID)' class='spanPointer'>Next : S$nextSolution</span></li>";
        }
        }
         echo "<li style='width: 4%;'> <span onclick='editQS(7, $answer_id ,0 , \"ans$answer_id\")' class='spanPointer'>Edit</span></li>";
         echo "</ul>";
    }
    
    echo "<ul class='secundaria'><li style='width:70%;'>----------------------------------<li><li><span onclick='nextSolutionOrQuestion(4 , 0 , 0 , $questionID);' class='spanPointer'>Add answer</span></li></ul>";
    $CLD_CON2->OpenRs("SELECT id , ruta , tipo FROM SAT_media WHERE question = $questionID ORDER BY tipo DESC");
    if($CLD_CON2->GetRsRows() > 0){
        echo "<ul class='secundaria'>";        
    while($CLD_CON2->FetchArray()){        
        $idMedia = $CLD_CON2->GetArrayField("id");
        $tipo = $CLD_CON2->GetArrayField("tipo");
        $ruta = $CLD_CON2->GetArrayField("ruta");
        if($tipo==1){
            echo "<li style='width:70%;'>IMAGE<li>";
            echo "<li style='width:15%;'>";
            echo "<span onclick='bigImage2(\"$ruta\");' class='spanPointer'>VIEW</span>";
            echo "</li>";
            echo "<li style='width:8%;COLOR:red;'><span onclick='deleteMedia(this , $idMedia)' style='cursor:pointer;'>DELETE</span></li>";
        }else{
            echo "<li style='width:70%;'>VIDEO<li>";
            echo "<li style='width:15%;'>";
            echo "<span onclick='popupVideo(\"$ruta\");' class='spanPointer'>VIEW</span>";            
            echo "</li>";
             echo "<li style='width:8%;COLOR:red;'><span onclick='deleteMedia(this , $idMedia)' style='cursor:pointer;'>DELETE</span></li>";
        }
    }
    echo "</ul>";
    }
   $link = $URL_BASE . "admin/addMedia.php?id=".$questionID."&QoS=1";
        echo "<ul class='secundaria'><li style='width:70%;'>----------------------------------<li><li>";
        echo "<span onclick='window.open(\"$link\",\"\",\"top=200,left=200,width=400,height=400\");' class='spanPointer'>Add Image/Video</span></li></ul>";


    echo "</div>";
}



?>
