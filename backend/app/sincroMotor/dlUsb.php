<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: dlUsb.php. download de custom, paràmetres:
idO	numèric	id d'owner a myphotocode
idU	numèric	id de l'Usb, `usbs`.`id` bigint(4)
retornarà
Cap JSON, serà un fitxer zip a descarregar


*/

$Mtr_script = "dlUsb";
require("common.php"); 

//resposta
$resposta['status'] = 0;

$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
//    $resposta['statusStr'] = $MTR_status;
//    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['idO'])){ $idOwner = $_REQUEST['idO'];} 
else {
    fesLog("Error - $Mtr_script, missing idO - Error01-");
//    $resposta['statusStr'] = "Error01-idO";
//    echo json_encode($resposta);
    return;
}
if(isset($_REQUEST['idU'])){ $idUsb = $_REQUEST['idU'];} 
else {
    fesLog("Error - $Mtr_script, missing idU - Error01-");
//    $resposta['statusStr'] = "Error01-idU";
//    echo json_encode($resposta);
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

$sql = "SELECT usbs.event_id, usbs.creation_date, events.title  FROM usbs JOIN events ON usbs.event_id=events.id WHERE usbs.id=$idUsb AND usbs.rental_id=$idOwner AND usbs.available=1; ";

//$USBFolder = $dataUSB . $idUSB;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - $Mtr_script, Error03_sql: $sql.");
//    $resposta['statusStr'] = "Error03_sql";
//    echo json_encode($resposta);
    return;
}
//    fesLog("TRACE - $Mtr_script, sql: $sql.");
if($APP_BdD->FetchRs()){
    $USBidEvent = $APP_BdD->GetField(1);
    $USBFolder = $APP_BdD->GetField(2) . $idUsb;
    $USBtitle = utf8_encode($APP_BdD->GetField(3)); 
}
$APP_BdD->CloseRs();


require("commonUsb.php"); 

if(isset($_REQUEST['dl'])){ $calHeader = $_REQUEST['dl'];} 
if($calHeader){
    header('Content-Type: application/zip');
    header("Content-disposition: attachment; filename=\"$USBtitle.zip\"");
    header('Content-Length: ' . filesize($zipFileName));
}
readfile($zipFileName);

?>
