<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
$ID = $_POST['id'];
$booth= $_POST['boothid'];

$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$booth");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}

if($CLD_CON->Execute("UPDATE CLD_components SET  booth = NULL WHERE serialnumber='$ID' ")){
    $date = date("Y-m-d H:i:s");
    $text = "Removed from photobooth $sn";
    $CLD_CON->Execute("INSERT INTO CLD_historyComponents (component_sn , data , comment) VALUES('$ID' , '$date' , '$text')");
    echo "OK";
}

?>
