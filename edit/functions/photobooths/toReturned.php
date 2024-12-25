<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$coment = addslashes($_POST['coment']);
$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT serialnumber , owner FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $owner = $CLD_CON->GetArrayField("owner");
}
$CLD_CON->OpenRs("SELECT name FROM rentals WHERE id = $owner");
if ($CLD_CON->FetchArray()) {

    $ownerName = $CLD_CON->GetArrayField("name");
}
if ($_POST['damage'] == 1) {
    $toID = 5;
} else {
    $toID = 4;
}

$from = $_POST['from'];
$from_text = $BOOTHS_TYPE_STATUS[$from];
$to = $BOOTHS_TYPE_STATUS[$toID];
$dateTime = date("Y-m-d H:i:s");
$code = "#autoIn";
$status = 0;
$username = "server";
if ($CLD_CON2->Execute("UPDATE App_booths SET CLD_Status=$toID , owner=1 WHERE idBooth=$ID")) {
    $in = $CLD_CON->ExecuteInsert("INSERT INTO CLD_Incidents (idBooth , coment , datetime , code  , user , status)" . "VALUES($ID , '$coment' , '$dateTime' , '$code' , '$username' , $status)");
    $coment = addslashes("$to  from Owner $ownerName  , incident created:  #$in#");
    $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment' , '$dateTime' , '$ID' , '$sn')");
    $CLD_CON2->Execute("UPDATE CLD_components SET owner=NULL WHERE booth=$ID");
    $coment2 = "This Component has returned from owner $ownerName into PhotoBbooth $sn";
    $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE booth=$ID");

    while ($CLD_CON->FetchArray()) {
        $snC = $CLD_CON->GetArrayField("serialnumber");
        $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$coment2' , '$dateTime' , '$snC')");
    }
    $CLD_CON->OpenRs("SELECT idDongle FROM App_boothDongle WHERE idBooth=$ID AND datetimeF IS NULL ORDER BY datetimeS DESC LIMIT 1");
    if ($CLD_CON->FetchArray()) {
        $dongleID = $CLD_CON->GetArrayField("idDongle");
        $CLD_CON2->Execute("UPDATE booths SET rental_id=1 WHERE id=$dongleID AND rental_id=$owner");
    }

    echo "OK";
} else {
    echo "ERROR";
}
?>