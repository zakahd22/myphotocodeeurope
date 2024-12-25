<?php
/*
 * Versió 9.0 27/10/2015
 Mètode: getPBs.php. llista de PBs d'un owner, paràmetres:
id	numèric	id d'owner a myphotocode  `rentals`.`id` bigint(4)
retornarà
ok#<nPBs># ids dels PBs
JSON:
·	 'nPBs' (potser zero)
·	,'infoPBs', serà un array d'objectes amb:
·	'id', id del PB BdD  `App_booths`.`idBooth` int(5)
·	'type', camp `App_booths`.CLD_idType int(2)
·	'typeStr', BdD CLD_boothTypes.name varchar(50)
·	'name', camp name del PB que edita l'owner`App_booths`.name varchar(50)
·	'serial', serialnumber`App_booths`.serialnumber  varchar(15)
·	'startDate', data de compra `App_booths`.CLD_date_sold (datetime)
*/

$Mtr_script = "gestPBs";
require("common.php"); 

//resposta
$resposta['status'] = 0;


$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
//    echo "ko#$MTR_status";
//no cal    $resposta['status'] = 0;
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['id'])){ $idOwner = $_REQUEST['id'];} 
else {
    fesLog("Error - $Mtr_script, missing id - Error01");
    $resposta['statusStr'] = "Error01";
    echo json_encode($resposta);
    return;
}

//torno al control per signatura
$signature = strtoupper(sha1($idOwner.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - getPBs, to code: ".$idOwner.$MTR_tact.$MTR_MtrControl."  - Error02");
    fesLog("Error - getPBs, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}
//20160202 INICI
//$sql = "SELECT App_booths.idBooth, App_booths.CLD_idType, CLD_boothTypes.name, App_booths.name, App_booths.serialnumber
//    FROM App_booths LEFT JOIN CLD_boothTypes ON App_booths.CLD_idType = CLD_boothTypes.id
//    WHERE owner=$idOwner; ";


$sql = "SELECT App_booths.idBooth, App_booths.CLD_idType, CLD_boothTypes.name, App_booths.name, App_booths.serialnumber,`App_booths`.CLD_date_sold
    FROM App_booths LEFT JOIN CLD_boothTypes ON App_booths.CLD_idType = CLD_boothTypes.id
    WHERE owner=$idOwner; ";
//20160202 FINAL

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - $Mtr_script, Error03_sql: $sql.");
    $resposta['statusStr'] = "Error03_sql";
    echo json_encode($resposta);
    return;
}
//    fesLog("TRACE - $Mtr_script, sql: $sql.");

$resposta['nPBs'] = $nPBs = 0;
while($APP_BdD->FetchRs()){
    $item['id'] = $APP_BdD->GetField(1);
    $item['type'] = $APP_BdD->GetField(2);
    $item['typeStr'] = utf8_encode($APP_BdD->GetField(3)); 
    $item['name'] = utf8_encode($APP_BdD->GetField(4)); 
    $item['serialnumber'] = utf8_encode($APP_BdD->GetField(5)); 
    $item['startDate'] = $APP_BdD->GetFieldDateTime(6);//20160202
    $resposta['infoPBs'][$nPBs] = $item;
    $nPBs++;
}
$APP_BdD->CloseRs();
    
//fesLog("TRACE - $Mtr_script, nPBs: $nPBs");

$resposta['nPBs'] = $nPBs;

//resposta
$resposta['status'] = 1;
echo json_encode($resposta);


?>
