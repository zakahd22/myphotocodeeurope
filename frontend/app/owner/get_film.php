<?php
require("common.php");
if(!$APP_user) return;

//packs per a formulari a editar
//

$xml = $APP_xmlOKcomm;

//SELECT `idPack`, `label`, `descr`, `price`, `active` FROM `App_ordersPack` WHERE 1


$sql = "SELECT `idPack`, `label`, `price` FROM `App_ordersPack` WHERE active = 1; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$xml.= "<currency>$</currency>";

$xml.= "<packs>";
while($APP_BdD->FetchRs()){
    $xml.= "<pack>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<pack_id>$tmp</pack_id>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
    $xml.= "<pack_text>$tmp</pack_text>";
    $tmp = $APP_BdD->GetField(3);
//20120913 no volen decimals    $xml.= "<pack_price>$tmp</pack_price>";
    $xml.= "<pack_price>".sprintf("%.0f",$tmp)."</pack_price>";//20120913 no volen decimals
    $xml.= "</pack>";
}
$APP_BdD->CloseRs();
$xml.= "</packs>";

echo "$APP_xml$xml</return>"; // no cal res més


?>
