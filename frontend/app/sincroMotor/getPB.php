<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: getPB.php. dades específiques d'un PB d'un owner en concret, paràmetres:
id	numèric	id d'owner a myphotocode `rentals`.`id` bigint(4)
idB	numèric	id del PB, `App_booths`.`idBooth` int(5)
retornarà
ok# dades?
JSON:
·	'idOwner', BdD  `App_booths`.`owner` smallint(5)
·	'type', BdD  `App_booths`.CLD_idType int(2)
·	'typeStr', BdD CLD_boothTypes.name varchar(50)
·	'name', camp name del PB que edita l'owner`App_booths`.name varchar(50)
·	'serial', serialnumber BdD  `App_booths`.serialnumber  varchar(15)
*/

$Mtr_script = "getPB";
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
if(isset($_REQUEST['idB'])){ $idBooth = $_REQUEST['idB'];} 
else {
    fesLog("Error - $Mtr_script, missing idB - Error01");
    $resposta['statusStr'] = "Error01";
    echo json_encode($resposta);
    return;
}

//torno al control per signatura
$signature = strtoupper(sha1($idBooth.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - $Mtr_script, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}



$sql = "SELECT App_booths.owner, App_booths.CLD_idType, CLD_boothTypes.name, App_booths.name, App_booths.serialnumber
    FROM App_booths LEFT JOIN CLD_boothTypes ON App_booths.CLD_idType = CLD_boothTypes.id
    WHERE idBooth=$idBooth; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - $Mtr_script, Error03_sql: $sql.");
    $resposta['statusStr'] = "Error03_sql";
    echo json_encode($resposta);
    return;
}
//    fesLog("TRACE - $Mtr_script, sql: $sql.");
if($APP_BdD->FetchRs()){
    $resposta['idOwner'] = $APP_BdD->GetField(1);
    $resposta['type'] = $APP_BdD->GetField(2);
    $resposta['typeStr'] = utf8_encode($APP_BdD->GetField(3)); 
    $resposta['name'] = utf8_encode($APP_BdD->GetField(4)); 
    $resposta['serialnumber'] = utf8_encode($APP_BdD->GetField(5)); 
}
$APP_BdD->CloseRs();
    
//fesLog("TRACE - $Mtr_script, nPBs: $nPBs");

//resposta
$resposta['status'] = 1;
echo json_encode($resposta);

?>
