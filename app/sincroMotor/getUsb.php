<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: getUBS.php. dades específiques d'un USB previ al download de custom, paràmetres:
idO	numèric	id d'owner a myphotocode
idU	numèric	id de l'Usb, `usbs`.`id` bigint(4)


retornarà
JSON:
·	'idOwner', BdD  `usbs`.`rental_id` bigint(10)
·	'idEvent', BdD  `usbs`.`event_id` bigint(6)
·	'type', BdD usbs.CLD_idTypeBooth  int(2)
·	'available', BdD usbs.available  int(1)

*/

$Mtr_script = "getUsb";
require("common.php"); 

//resposta
$resposta['status'] = 0;

$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['idO'])){ $idOwner = $_REQUEST['idO'];} 
else {
    fesLog("Error - $Mtr_script, missing idO - Error01-");
    $resposta['statusStr'] = "Error01-idO";
    echo json_encode($resposta);
    return;
}
if(isset($_REQUEST['idU'])){ $idUsb = $_REQUEST['idU'];} 
else {
    fesLog("Error - $Mtr_script, missing idU - Error01-");
    $resposta['statusStr'] = "Error01-idU";
    echo json_encode($resposta);
    return;
}

//torno al control per signatura
$signature = strtoupper(sha1($idOwner.$idUsb.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - $Mtr_script, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}

$sql = "SELECT usbs.event_id, usbs.CLD_idTypeBooth, usbs.available, events.title, usbs.creation_date  FROM usbs JOIN events ON usbs.event_id=events.id WHERE usbs.id=$idUsb AND usbs.rental_id=$idOwner; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - $Mtr_script, Error03_sql: $sql.");
    $resposta['statusStr'] = "Error03_sql";
    echo json_encode($resposta);
    return;
}
//    fesLog("TRACE - $Mtr_script, sql: $sql.");
if($APP_BdD->FetchRs()){
    $resposta['idEvent'] = $APP_BdD->GetField(1);
    $resposta['type'] = $APP_BdD->GetField(2);
    $resposta['available'] = $APP_BdD->GetField(3);
    $resposta['title'] = utf8_encode($APP_BdD->GetField(4)); 
    $USBFolder = $APP_BdD->GetField(5) . $idUsb;
    $USBtitle = $resposta['title']; 
    $USBidEvent =  $resposta['idEvent'];

}
$APP_BdD->CloseRs();

require("commonUsb.php"); 

$resposta['length'] = filesize($zipFileName);

//resposta
$resposta['status'] = 1;
echo json_encode($resposta);

?>
