<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$cQ = $_POST['cQ'];
$from = $_POST['from'];
$from_txt = $BOOTHS_TYPE_STATUS[$from];
$to = $BOOTHS_TYPE_STATUS[2];
$coment = addslashes("Change from $from_txt to $to");
$date = date("Y-m-d H:i:s");
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}


if($CLD_CON->Execute("UPDATE App_booths SET CLD_Status=2 , CLD_date_sold='$date' , CLD_ControlQuality='$cQ' WHERE idBooth=$ID")){
     echo "OK";
     $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment' , '$date' , $ID , '$sn')");
}else{
    echo "ERROR:  I have a error , please try again";
}
?>
