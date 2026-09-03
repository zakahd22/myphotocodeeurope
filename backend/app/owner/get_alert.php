<?php
require("common.php");
if(!$APP_user) return;

//definicions d'alertes per a un PhotoBooth a editar
//info de les condicions d'alerta no editables des de App

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<status>$APPERROR_noid</status></return>";
return;
}

$sql = "SELECT `App_alerts`.`typeAlert`,`descr`,`value`,`App_alerts`.`values` FROM `App_alerts` 
    LEFT JOIN `App_boothAlertDef` ON `App_alerts`.`typeAlert` = `App_boothAlertDef`.`typeAlert`
    WHERE `idBooth`=$idBooth; ";

//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<status>Error - Database error code: 0002 $sql</status></return>";
return;
}
$xml.= "<status>OK</status>";
while($APP_BdD->FetchRs()){
    $xml.= "<alert>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<id>$tmp</id>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
     $xml.= "<text>$tmp</text>";
    $tmp = $APP_BdD->GetField(3);
    $xml.= "<valueid>$tmp</valueid>";
    $tmp = $APP_BdD->GetField(4);
//    $xml.= "<trace>$tmp</trace>";
    $array_values = explode("#",$tmp); $l = count($array_values)-1;//han de ser parelles
    $xml.= "<tracel>$l</tracel>";
    $i = 0;while($i<$l){
        $xml.= "<value><id>$array_values[$i]</id><text>";$i++;
        $xml.= APP_preparaXML($array_values[$i])."</text></value>";$i++;
    }
    $xml.= "</alert>";

}
$APP_BdD->CloseRs();

echo "$APP_xml$xml</return>"; // no cal res més


//////Merda, no van els subqueries en  els JOIN FINAL


?>
