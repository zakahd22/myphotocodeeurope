<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$SN = $_POST['id'];
$selectedOwner = $_POST['owner'];
$data = date("Y-m-d H:i:s");
$CLD_CON->OpenRs("SELECT * FROM CLD_components WHERE serialnumber='$ID'");
if ($CLD_CON->FetchArray()) {
    $ownerID2 = $CLD_CON->GetArrayField("owner");
}
$CLD_CON->OpenRs("SELECT name FROM rentals WHERE id='$ownerID2'");
if ($CLD_CON->FetchArray()) {
    $oldOwnerName = $CLD_CON->GetArrayField("name");
}

if ($selectedOwner == 0) {
    if ($CLD_CON->Execute("UPDATE CLD_components SET owner=NULL , data_owner=NULL WHERE serialnumber='$SN'")) {
        $text = "Returned from $oldOwnerName";
        $CLD_CON->Execute("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$text' , '$data' ,'$SN')");
        echo "OK , The component has been returned";
    }
} else {
    $CLD_CON->OpenRs("SELECT name FROM rentals WHERE id='$selectedOwner'");
    if ($CLD_CON->FetchArray()) {
        $newOwnerName = $CLD_CON->GetArrayField("name");
    }
    if ($CLD_CON->Execute("UPDATE CLD_components SET owner=$selectedOwner , data_owner='$data' WHERE serialnumber='$SN'")) {
        $text = "Sent to $newOwnerName";
        $CLD_CON->Execute("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$text' , '$data' ,'$SN')");
        echo "OK , The component has been assign to $newOwnerName";
    }
}
?>
