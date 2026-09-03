<?php
require("common.php");


if(!$APP_user) return;


//només mira si hi ha alertes en estat < 2 per l'owner, si hi ha només 1 passa l'idBooth

$sql = "SELECT App_booths.idBooth, App_boothAlert.id
    FROM App_boothAlert LEFT JOIN App_booths ON App_boothAlert.idBooth = App_booths.idBooth
    WHERE App_boothAlert.estat < 2 AND owner=$APP_userId; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$xml = $APP_xmlOKcomm;
$xml.= "<alerts>";
$nAlerts = 0;
$idBooth = 0;
while($APP_BdD->FetchRs()){
    $idBooth =  $APP_BdD->GetField(1);
    $nAlerts++;
}
$APP_BdD->CloseRs();
$xml.= "$nAlerts</alerts>";

if($nAlerts == 1) $xml.= "<booth_id>$idBooth</booth_id>";

echo "$APP_xml$xml</return>"; // no cal res més



?>
