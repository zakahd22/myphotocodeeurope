<?php
include "../sessio.php";
include '../conexio.php';
$QoS = $_POST['QoS'];
$QSID = $_POST['QSID'];
$text= addslashes($_POST['txt']);
if($QoS == 0){
    $CLD_CON->Execute("UPDATE SAT_questions SET question='$text' WHERE id=$QSID");
}
if($QoS == 1){
    $CLD_CON->Execute("UPDATE SAT_solutions SET solution='$text' WHERE id=$QSID");
}
?>
