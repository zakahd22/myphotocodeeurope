<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$coment = addslashes($_POST['coment']);
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber , CLD_Distributor FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $distributor = $CLD_CON->GetArrayField("CLD_Distributor");
}
$from = $_POST['from'];
$from_text = $BOOTHS_TYPE_STATUS[$from];
$to = $BOOTHS_TYPE_STATUS[2];
$dateTime = date("Y-m-d H:i:s");

$CLD_CON->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$distributor");
if ($CLD_CON->FetchArray()) {
    $disName = $CLD_CON->GetArrayField("Name");
}

$code = "#autoIn";
$status = 0;
$username = "server";
if ($CLD_CON2->Execute("UPDATE App_booths SET CLD_Status=2 WHERE idBooth=$ID")) {
    $CLD_CON2->Execute("UPDATE CLD_components SET   owner = NULL , data_owner= NULL  , Status =2 WHERE booth=$ID");
    echo "OK";
} else {
    echo "ERROR";
}
?>