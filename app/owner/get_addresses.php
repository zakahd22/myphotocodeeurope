<?php
require("common.php");
if(!$APP_user) return;

//adresses d'on owner per a fer la comanda
//incloent-hi preus de shipping per als packs a cada adreça


$xml = $APP_xmlOKcomm;

//SELECT `id`, `idOwner`, `preference`, `address`, `code`, `city`, `state`, `country` FROM `App_ownerAddress` WHERE 1

//SELECT `idAddress`, `idPack`, `price` FROM `App_adressPackSipping` WHERE 1
/*$sql = "SELECT `id`, `address`, `code`, `city`, `state`, `country`, `idPack`, `price` 
    FROM App_ownerAddress LEFT JOIN App_adressPackSipping ON App_ownerAddress.id = App_adressPackSipping.`idAddress`
    WHERE `idOwner`=$APP_userId AND CLD ORDER BY App_ownerAddress.`preference`, `idPack`; ";*/

//20140409 JoanCorominas
$sql = "SELECT `id`, `address`, `code`, `city`, `state`, `country`, `idPack`, `price` 
    FROM App_ownerAddress LEFT JOIN App_adressPackSipping ON App_ownerAddress.id = App_adressPackSipping.`idAddress`
    WHERE `idOwner`=$APP_userId AND CLD_status=1 ORDER BY App_ownerAddress.`preference`, `idPack`; ";
//20140409 JoanCorominas FI
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

//info alerta online
$textAlertOffline = "Offline alert: ";

$xml.= "<addresses>";
$address_id = -1;
$address_text = "";
$n = 0;
while($APP_BdD->FetchRs()){
    $tmp =  $APP_BdD->GetField(1);
    if($tmp != $address_id){
        if($address_id != -1) $xml.= "</shipping></address>";
        $address_id = $tmp;
        $xml.= "<address>";
        $xml.= "<address_id>$tmp</address_id>";
//, `address`, `code`, `city`, `state`, `country`
        $address_text = APP_preparaXML($APP_BdD->GetField(2));
//`address`, `code`, `city`, `state`, `country`        
         $xml.= "<address_address>$address_text</address_address>";//20120926
       
        
        $tmp = APP_preparaXML($APP_BdD->GetField(3));
//`address`, `code`, `city`, `state`, `country`        
         $xml.= "<address_code>$tmp</address_code>";//20120926
         
        $address_text.= " $tmp";
        $tmp = APP_preparaXML($APP_BdD->GetField(4));
//`address`, `code`, `city`, `state`, `country`        
         $xml.= "<address_city>$tmp</address_city>";//20120926
         
        $address_text.= " $tmp";
        $tmp = APP_preparaXML($APP_BdD->GetField(5));
//`address`, `code`, `city`, `state`, `country`        
         $xml.= "<address_state>$tmp</address_state>";//20120926
         
        $address_text.= " $tmp";
        $tmp = APP_preparaXML($APP_BdD->GetField(6));
//`address`, `code`, `city`, `state`, `country`        
         $xml.= "<address_country>$tmp</address_country>";//20120926
         
        $address_text.= " $tmp";
        $xml.= "<address_text>$address_text</address_text>";
        $xml.= "<shipping>";
        $n++;
    }
    $xml.= "<pack>";
    $tmp = $APP_BdD->GetField(7);
    $xml.= "<pack_id>$tmp</pack_id>";
    $tmp = $APP_BdD->GetField(8);
    $xml.= "<shipping_price>$tmp</shipping_price>";
    $xml.= "</pack>";
}
$APP_BdD->CloseRs();
if($n){//201403
$xml.= "</shipping></address>";
}//201403
$xml.= "</addresses>";


echo "$APP_xml$xml</return>"; // no cal res més



?>
