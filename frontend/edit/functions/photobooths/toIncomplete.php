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
$from = $_POST['from'];
$from_text = $BOOTHS_TYPE_STATUS[$from];
$to = $BOOTHS_TYPE_STATUS[7];
$dateTime = date("Y-m-d H:i:s");
$set = "";
if(isset($_POST['dis'])){
    $distributor = $_POST['dis'];
      $CLD_CON->OpenRs("SELECT Name FROM CLD_Distributors WHERE id=$distributor");
    if ($CLD_CON->FetchArray()) {
        $disName = $CLD_CON->GetArrayField("Name");
    }
    
    
    $set = " , CLD_date_sold='$dateTime' ,CLD_Distributor=$distributor ";
}
$code = "#autoIn";
$status = 0;
$username = "server";
if ($CLD_CON2->Execute("UPDATE App_booths SET CLD_Status=7 $set WHERE idBooth=$ID")) {
    $in = $CLD_CON->ExecuteInsert("INSERT INTO CLD_Incidents (idBooth , coment , datetime , code  , user , status)" . "VALUES($ID , '$coment' , '$dateTime' , '$code' , '$username' , $status)");
    $coment = addslashes("Change from $from_text to $to  , and create a new incident #$in#");
    $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment' , '$dateTime' , '$ID' , '$sn')");      
    if(isset($_POST['dis'])){
     $CLD_CON2->Execute("UPDATE CLD_components SET distributor = $distributor , data_distribuidor='$dateTime' WHERE booth=$ID");
     $coment2 = "This Component sent to Distributor $disName into photobooth $sn";
     $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE booth=$ID");
     while($CLD_CON->FetchArray()){
         $snC = $CLD_CON->GetArrayField("serialnumber");
         $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$coment2' , '$dateTime' , '$snC')");
     }
    }   
    echo "OK";
    
} else {
    echo "ERROR";
}
?>