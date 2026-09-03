<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$questionN = $_POST['question'];
$active = $_POST['active'];

if ($active == 0) {
    if ($CLD_CON->Execute("DELETE FROM CLD_questions WHERE question_number=$questionN AND event = $ID")) {
        echo "OK";
    } else {
        echo "ERROR";
    }
} else {
    switch ($questionN) {
        case 1:
            if($CLD_CON->Execute("INSERT INTO CLD_questions(question_number , event) VALUES(1, $ID)")){
                echo "OK";
            }
            else{
                echo "ERROR";
            }
            break;
        
        case 2:
            $r1 = addslashes($_POST['r1']);
            $r2 = addslashes($_POST['r2']);
            $qText = addslashes($_POST['q']);
            if ($CLD_CON->Execute("INSERT INTO CLD_questions(question_number , question , reply1, reply2, event) VALUES(2 ,'$qText', '$r1' , '$r2' , $ID)")){
                echo "OK";
            } 
            else{
                echo "ERROR";
            }
            break;
            
        case 3:
            $r1 = addslashes($_POST['r1']);
            $r2 = addslashes($_POST['r2']);
            $qText = addslashes($_POST['q']);            
            if($CLD_CON->Execute("INSERT INTO CLD_questions(question_number , question , reply1, reply2, event) VALUES(3 ,'$qText', '$r1' , '$r2' , $ID)")){
                echo "OK";
            } 
            else {
                echo "ERROR";
            }
            break;
    }
}
?>
