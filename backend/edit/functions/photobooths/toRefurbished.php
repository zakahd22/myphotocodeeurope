<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$coment = addslashes($_POST['coment']);
$incidencia = $_POST['inc'];
$from = $_POST['from'];
$date = date("Y-m-d H:i:s");
$text1 = addslashes("Changed from " . $BOOTHS_TYPE_STATUS[8] ." to ". $BOOTHS_TYPE_STATUS[$from]);
$uID = $_SESSION['USERID'];
$uType = $_SESSION['USERTYPE'];
$CLD_CON->OpenRs("SELECT username FROM CLD_Login WHERE id_user=$uID AND userType=$uType");
if($CLD_CON->FetchArray()){
    $username = $CLD_CON->GetArrayField("username");
}
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}

if($CLD_CON->Execute("UPDATE App_booths SET CLD_Status=8 WHERE idBooth=$ID")){
    $CLD_CON->Execute("INSERT INTO CLD_Inc_coments (coment , incident , datetime , user) VALUES('$coment' , $incidencia , '$date' , '$username')");
    $CLD_CON->Execute("UPDATE CLD_Incidents SET status=2 WHERE id=$incidencia");
    $CLD_CON->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth, sn) VALUES('$text1' , '$date' , $ID , $sn)");
    echo "OK";
}else{
        echo "ERROR";
}



?>
