<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$CLD_CON2 = clone($CLD_CON);
$SN = $_POST['id'];
$newDis = $_POST['dis'];
$date = date("Y-m-d H:i:s");
$CLD_CON2->OpenRs("SELECT Name From CLD_Distributors WHERE id=$newDis");
if ($CLD_CON2->FetchArray()) {
    $newDisName = $CLD_CON2->GetArrayField("Name");
}
 $text= addslashes("Sent to $newDisName");
$CLD_CON->OpenRs("SELECT distributor FROM CLD_components WHERE serialnumber='$SN'");
if($CLD_CON->FetchArray()){
    $distributor = $CLD_CON->GetArrayField("distributor");
           $CLD_CON2->OpenRs("SELECT Name From CLD_Distributors WHERE id=$distributor");
        if($CLD_CON2->FetchArray()){
            $disName = $CLD_CON2->GetArrayField("Name");
        }
        $text = addslashes("Sent to $newDisName from $disName");

}

if($CLD_CON->Execute("UPDATE CLD_components SET distributor=$newDis , data_distribuidor='$date' WHERE serialnumber='$SN'")){
    $CLD_CON->Execute("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$text' , '$date' ,'$SN')");
    echo "OK";
}else{
    echo "ERROR , please try again";
}

?>
