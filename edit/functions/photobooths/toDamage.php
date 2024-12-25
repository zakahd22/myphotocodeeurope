<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$coment = addslashes($_POST['coment']);
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
}
$from= $_POST['from'];
$from_text = $BOOTHS_TYPE_STATUS[$from];
if($from == 4){
$to = $BOOTHS_TYPE_STATUS[5];
$idTo = 5;
}else{
$to = $BOOTHS_TYPE_STATUS[6];
$idTo = 6;
}
$dateTime = date("Y-m-d H:i:s");
$code = "#autoIn";
$status = 0;
$username = "server";
if($CLD_CON2->Execute("UPDATE App_booths SET CLD_Status=$idTo WHERE idBooth=$ID")){
$in = $CLD_CON->ExecuteInsert("INSERT INTO CLD_Incidents (idBooth , coment , datetime , code  , user , status)" . "VALUES($ID , '$coment' , '$dateTime' , '$code' , '$username' , $status)");
$coment = addslashes("Change from $from_text to $to  , and create a new incident #$in#");
$CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment' , '$dateTime' , '$ID' , '$sn')");
echo "OK";
}else{
    echo "ERROR";
}

?>