<?php
include '../sessio.php';
include '../conexio.php';

$id = $_POST['id'];
$assign = $_POST['id_assign'];
$action = $_POST['action'];

if($action ==1){
    $CLD_CON->Execute("UPDATE SAT_answers SET nextSolution=$assign , nextQuestion=0 WHERE id=$id");
    $CLD_CON->OpenRs("SELECT solution FROM SAT_solutions WHERE id=$assign");
    if($CLD_CON->FetchArray()){
        echo stripslashes("Solution assigned : " . $CLD_CON->GetArrayField("solution"));
    }else{
        return;
    }
}
if($action ==2){
    $CLD_CON->Execute("UPDATE SAT_answers SET nextQuestion=$assign , nextSolution=0 WHERE id=$id");
    $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$assign");
    if($CLD_CON->FetchArray()){
        echo stripslashes("Question assigned : " . $CLD_CON->GetArrayField("question"));
    }else{
        return;
    }
}
if($action ==3){
    $CLD_CON->Execute("UPDATE SAT_solutions SET nextQuestion=$assign WHERE id=$id");
    $CLD_CON->OpenRs("SELECT question FROM SAT_questions WHERE id=$assign");
    if($CLD_CON->FetchArray()){
        echo stripslashes("New question assigned : " . $CLD_CON->GetArrayField("question"));
    }else{
        return;
    }
}
if($action == 4){
    
    $CLD_CON->OpenRs("SELECT * FROM SAT_firstquestion WHERE boothType='$id'");
    if($CLD_CON->FetchArray()){
        $CLD_CON->Execute("UPDATE SAT_firstquestion SET question_id=$assign WHERE boothType='$id'");
    }else{
        $CLD_CON->Execute("INSERT INTO SAT_firstquestion VALUES('$id' , $assign);");
        
    }
}

?>
