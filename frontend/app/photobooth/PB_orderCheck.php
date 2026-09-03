<?php

/*
 * Petició des d'un PB de l'estat d'un pagament per PayPal
 * Params dongle i PB, etc
 * p: $fpagcontrol$idOrder
 * 
 * 
 */
if(isset($_REQUEST['p'])){
    $p = $_REQUEST['p'];
}
else{
    echo "ko#error pp01 no id";
return;
}
$l = strlen($p);
if($l < 20){
    echo "ko#error pp02 l<20";
}
$fpagcontrol = substr($p, 0, 20);
$idOrder = substr($p, 20);
        
require("common.php");
$sql ="SELECT `total`, `fpagstatus`  FROM `App_PBorders` WHERE  idOrder = $idOrder AND `fpagcontrol` = '$fpagcontrol';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
     echo "ko#error pp03 no $sql";
    return;
}
if($APP_BdD->FetchRs()){
    $total =  $APP_BdD->GetField(1);
    $fpagstatus =  $APP_BdD->GetField(2);
}
else{
    $total = 0;
    $fpagstatus = 1;
}
$APP_BdD->CloseRs();

echo "ok#$total#$fpagstatus"; 


?>
