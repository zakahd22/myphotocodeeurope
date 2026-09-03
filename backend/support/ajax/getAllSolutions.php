<?php

include "../sessio.php";
include '../conexio.php';
if (isset($_POST['filtros'])) {

    $x = 0;
    $f = array();
    $where = "";
    $select = "SELECT s.id , s.solution , s.nextQuestion FROM SAT_solutions s ";

    $solutionF = $_POST['solutionF'];
    $nxtQ = $_POST['nextQuestion'];
    $codeS = $_POST['code'];
    if($codeS != 0){
        $f[$x] = "s.id=$codeS";
        $x++;
    }
    if (!empty($solutionF)) {
        $f[$x] = "s.solution LIKE '%$solutionF%'";
        $x++;
    }
    if ($nxtQ != "0") {
        $f[$x] = "s.nextQuestion = $nxtQ";
        $x++;
    }
    $x2 = 0;
    while ($x2 < $x) {
        if ($x2 == 0) {
            $where .= " WHERE ";
        }
        $where .= $f[$x2];
        if (($x2 + 1) < $x) {
            $where .= " AND ";
        }
        $x2++;
    }

    $select .= " " . $where;
} else {
    $select = "SELECT * FROM SAT_solutions s";
}
$select .= " ORDER BY s.id";
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs($select);
while ($CLD_CON->FetchArray()) {
    $solution = stripslashes($CLD_CON->GetArrayField("solution"));
    $solutionID = $CLD_CON->GetArrayField("id");
    $nextQuestion = $CLD_CON->GetArrayField("nextQuestion");
    echo "<ul class='primaria' onclick='openCloseAnswers($solutionID)' id='q$solutionID'>";
    echo "<li >S$solutionID - <span id='qst$solutionID'>$solution</span>";
    echo "<span onclick='editQS(6, $solutionID ,1 , \"qst$solutionID\")' class='spanPointer spanEdit'>Edit</span></li>";
    echo "</ul>";
    echo "<div id='question$solutionID' class='hiddenAnswers'>";
    echo "<ul class='secundaria'>";
    echo "<li style='width:70%;'>Yes<li>";
    echo "<li style='width:15%;'>Go to Solved</li>";
    echo "</ul>";
    echo "<ul class='secundaria'>";
    echo "<li style='width:70%;'>No<li>";
    echo "<li style='width:15%;'> <span onclick='nextSolutionOrQuestion(3,$nextQuestion , 0 , $solutionID)' class='spanPointer'>";
    if($nextQuestion==999999){
        echo "Next: Usolved Form";
    }else{
        echo "Next Q$nextQuestion";
    }
            echo "</span></li>";
    echo "</ul>";
    $CLD_CON2->OpenRs("SELECT id , ruta , tipo FROM SAT_media WHERE solution = $solutionID ORDER BY tipo DESC");
    if ($CLD_CON2->GetRsRows() > 0) {

        while ($CLD_CON2->FetchArray()) {
            $idMedia = $CLD_CON2->GetArrayField("id");
            $tipo = $CLD_CON2->GetArrayField("tipo");
            $ruta = $CLD_CON2->GetArrayField("ruta");
            echo "<ul class='secundaria'>";
            if ($tipo == 1) {
                echo "<li style='width:70%;'>IMAGE<li>";
                echo "<li style='width:15%;'>";
                echo "<span onclick='bigImage2(\"$ruta\");' class='spanPointer'>VIEW</span>";
                echo "</li>";
                echo "<li style='width:8%;COLOR:red;'><span onclick='deleteMedia(this , $idMedia)'>DELETE</span></li>";
            } else {
                echo "<li style='width:70%;'>VIDEO<li>";
                echo "<li style='width:15%;'>";
                echo "<span onclick='popupVideo(\"$ruta\");' class='spanPointer'>VIEW</span>";
                echo "</li>";
                echo "<li style='width:8%;COLOR:red;'><span onclick='deleteMedia(this , $idMedia)'>DELETE</span></li>";
            }
            echo "</ul>";
        }
    }
    $link = $URL_BASE . "admin/addMedia.php?id=" . $solutionID . "&QoS=0";
    echo "<ul class='secundaria'><li style='width:70%;'>----------------------------------<li><li>";
    echo "<span onclick='window.open(\"$link\",\"\",\"top=200,left=200,width=400,height=400\");' class='spanPointer'>Add Image/Video</span></li></ul>";

    echo "</div>";
}
?>