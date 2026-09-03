<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);


$ID = $_POST['id'];
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
}

if (isset($_POST['dis'])) {
    $dis = $_POST['dis'];
} else {
    $CLD_CON->OpenRs("SELECT CLD_Distributor FROM App_booths WHERE idBooth=$ID");
    if ($CLD_CON->FetchArray()) {
        $dis = $CLD_CON->GetArrayField("CLD_Distributor");
    }
}
    $CLD_CON->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$dis");
    if ($CLD_CON->FetchArray()) {
        $disName = $CLD_CON->GetArrayField("Name");
    }
$from = $_POST['from'];
$from_text = " " . $BOOTHS_TYPE_STATUS[$from] . " ";
$to_text = " " . $BOOTHS_TYPE_STATUS[2] . " ";
$date = date("Y-m-d H:i:s");
$f1 = "";
$f2 = "";

// Check if we're coming from status 1 (new) or need to check current distributor
if ($from == 1) {
    // New booth from production
    $f1 = ", CLD_Distributor=$dis, CLD_date_sold='$date'";
    $f2 = ", data_distribuidor='$date'";
    $c = "The Photobooth sent to Distributor $disName,";
} else {
    // Get current distributor to see if it's changing
    $currentDis = null;
    $CLD_CON3->OpenRs("SELECT CLD_Distributor FROM App_booths WHERE idBooth=$ID");
    if ($CLD_CON3->FetchArray()) {
        $currentDis = $CLD_CON3->GetArrayField("CLD_Distributor");
    }
    
    // If distributor is changing, update the timestamp
    if ($currentDis != $dis) {
        $f1 = ", CLD_Distributor=$dis";
        $f2 = ", data_distribuidor='$date'"; // New distributor gets timestamp
        $c = "The Photobooth returned to new Distributor $disName";
    } else {
        $f1 = ", CLD_Distributor=$dis";
        $c = "The Photobooth returned to same Distributor $disName";
    }
}

if ($CLD_CON->Execute("UPDATE App_booths SET  CLD_Status=2 $f1 WHERE idBooth=$ID")) {
    $CLD_CON2->Execute("UPDATE CLD_components SET distributor=$dis  $f2 , owner = NULL , data_owner= NULL  , Status =2 WHERE booth=$ID");
    $coment = addslashes("$c Status change from $from_text to $to_text ");
    $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment ,data , idBooth , sn) VALUES('$coment' , '$date' , $ID , '$sn' )");
    
    if ($from == 1) {
        $CLD_CON2->OpenRs("SELECT serialnumber FROM CLD_components WHERE  booth=$ID");
        while($CLD_CON2->FetchArray()){
            $serialnumber = $CLD_CON2->GetArrayField("serialnumber");
            $coment2 = addslashes("Sent to Distributor $disName into photobooth $sn");
            $CLD_CON3->Execute("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$coment2' , '$date' , '$serialnumber');");
        }
    }
    echo "OK";
} else {
    echo "ERROR , please try again.";
}
?>
