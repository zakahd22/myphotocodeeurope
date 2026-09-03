<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$cQ = $_POST['cQ'];
$date = date("Y-m-d H:i:s");
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}


if($CLD_CON->Execute("UPDATE App_booths SET CLD_Status=1 , CLD_date_production='$date' , CLD_ControlQuality='$cQ' WHERE idBooth=$ID")){
     echo "OK";
     $coment = addslashes("The Photobooth( SN : $sn ) has changed from production to finished factory product");
     $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment' , '$date' , $ID , '$sn')");
}else{
    echo "ERROR:  I have a error , please try again";
}
?>
