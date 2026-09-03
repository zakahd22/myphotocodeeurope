<?php
require("common.php");
if(!$APP_user) return;

//definicions d'alertes per a un PhotoBooth a editar
//info de les condicions d'alerta no editables des de App

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>$APPERROR_noid</comm_status></return>";
return;
}
$xml = $APP_xmlOKcomm;

//dades del booth i info per a d'altres alertes
//<booth_type>NG</booth_type>
//<booth_name>Zoo</booth_name>
//<booth_code>AJ3</booth_code>
//SELECT `estat`, `type`, `owner`, `name`,`hS`, `mS`, `hE`, `mE` FROM `App_booths` WHERE 1
$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
    FROM `App_boothDongle` INNER JOIN booths ON App_boothDongle.idDongle = booths.id
    WHERE idBooth = $idBooth ORDER BY datetimeS DESC) AS myBoothDongle";



$sql = "SELECT `estat`, booth_types.name, App_booths.name, myBoothDongle.rand_string,alertOffline,`hS`, `mS`, `hE`, `mE` 
    FROM (App_booths LEFT JOIN booth_types ON App_booths.type = booth_types.`char`)
    LEFT JOIN $myBoothDongle ON App_booths.idBooth = myBoothDongle.idBooth
    WHERE App_booths.`idBooth`=$idBooth; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 $sql</comm_status></return>";
return;
}

//info alerta online
$textAlertOffline = "Offline alert: ";

$xml.= "<booth>";
if($APP_BdD->FetchRs()){
//20120913 no el volen    $tmp =  $APP_BdD->GetField(1);
//20120913 no el volen    $xml.= "<booth_status>{$APP_estatsBooth[$tmp]}</booth_status>";
    $tmp = $APP_BdD->GetField(2);
    $xml.= "<booth_type>$tmp</booth_type>";
    $tmp = APP_preparaXML($APP_BdD->GetField(3));
    $xml.= "<booth_name>$tmp</booth_name>";
    $tmp = $APP_BdD->GetField(4);
    $xml.= "<booth_code>$tmp</booth_code>";
    $tmp = $APP_BdD->GetField(5);
    if($tmp){//l'alerta offline està definida
        $textAlertOffline.= "from ";
        $tmp = $APP_BdD->GetField(6); $textAlertOffline.= sprintf("%02d.",$tmp);
        $tmp = $APP_BdD->GetField(7); $textAlertOffline.= sprintf("%02d to ",$tmp);
        $tmp = $APP_BdD->GetField(8); $textAlertOffline.= sprintf("%02d.",$tmp);
        $tmp = $APP_BdD->GetField(9); $textAlertOffline.= sprintf("%02d",$tmp);
    }
    else{
        $textAlertOffline.= "not defined";
    }
}
$APP_BdD->CloseRs();
$xml.= "</booth>";

//alertes configurables

$sql = "SELECT `App_alerts`.`typeAlert`,`label`,`value`,`App_alerts`.`values` FROM `App_alerts`
    LEFT JOIN `App_boothAlertDef` ON `App_alerts`.`typeAlert` = `App_boothAlertDef`.`typeAlert`
    WHERE `idBooth`=$idBooth; ";

//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0002 $sql</comm_status></return>";
return;
}
$xml.= "<alerts>";
//    $xml.= "<trace>$sql</trace>";
while($APP_BdD->FetchRs()){
    $xml.= "<alert>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<alert_id>$tmp</alert_id>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
     $xml.= "<alert_text>$tmp</alert_text>";
//20120926    $tmp = $APP_BdD->GetField(3);
//20120926    $xml.= "<valueact>$tmp</valueact>";
//20120926    $tmp = $APP_BdD->GetField(4);
//20120926    $array_values = explode("#",$tmp); $l = count($array_values)-1;//han de ser parelles
//20120926//    $xml.= "<tracel>$l</tracel>";
//20120926    $xml.= "<values>";
//20120926    $i = 0;while($i<$l){
//20120926        $xml.= "<value><id>$array_values[$i]</id><text>";$i++;
//20120926        $xml.= APP_preparaXML($array_values[$i])."</text></value>";$i++;
//20120926    }
//20120926    $xml.= "</values>";

    $tmpAct = $APP_BdD->GetField(3); 
    
    $tmp = $APP_BdD->GetField(4);
    $array_values = explode("#",$tmp); $l = count($array_values)-1;//han de ser parelles
//    $xml.= "<tracel>$l</tracel>";
    $xmlValues = "<values>";
    $tmpActValue = $array_values[1];//primer valor de la llista
    $i = 0;while($i<$l){
        if($array_values[$i] == $tmpAct)  $tmpActValue = $array_values[$i + 1];
        $xmlValues.= "<value><id>$array_values[$i]</id><text>";$i++;
        $xmlValues.= APP_preparaXML($array_values[$i])."</text></value>";$i++;
    }
    $xml.= "<valueact>$tmpActValue</valueact>";
    $xml.= "$xmlValues</values>";

    $xml.= "</alert>";

}
$APP_BdD->CloseRs();
$xml.= "</alerts>";



//20120926 INICI afegim altres configs
$sql = "SELECT `App_config`.`typeConfig`,`label`,`value`,`App_config`.`values` FROM `App_config`
    LEFT JOIN `App_boothConfigDef` ON `App_config`.`typeConfig` = `App_boothConfigDef`.`typeConfig`
    WHERE `idBooth`=$idBooth; ";

//echo "TRACE 01 $sql";
//return;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0002bis $sql</comm_status></return>";
return;
}
$xml.= "<configs>";
//    $xml.= "<trace>$sql</trace>";
while($APP_BdD->FetchRs()){
    $xml.= "<config>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<config_id>$tmp</config_id>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
     $xml.= "<config_text>$tmp</config_text>";
    $tmpAct = $APP_BdD->GetField(3); 
//    $xml.= "<config_valueact>$tmp</config_valueact>";
    $tmp = $APP_BdD->GetField(4);
    $array_values = explode("#",$tmp); $l = count($array_values)-1;//han de ser parelles
//    $xml.= "<tracel>$l</tracel>";
    $xmlValues = "<config_values>";
    $tmpActValue = $array_values[1];//primer valor de la llista
    $i = 0;while($i<$l){
        if($array_values[$i] == $tmpAct)  $tmpActValue = $array_values[$i + 1];
        $xmlValues.= "<config_value><config_value_id>$array_values[$i]</config_value_id><config_value_text>";$i++;
        $xmlValues.= APP_preparaXML($array_values[$i])."</config_value_text></config_value>";$i++;
    }
    $xml.= "<config_valueact>$tmpActValue</config_valueact>";
    $xml.= "$xmlValues</config_values>";

    $xml.= "</config>";

}
$APP_BdD->CloseRs();
$xml.= "</configs>";


//20120926 FINAL


//alertes només configurables des de web

$xml.= "<others>";
$xml.= "<other>$textAlertOffline</other>";
$xml.= "</others>";


//SELECT `hS`, `mS`, `hE`, `mE` FROM `App_booths` WHERE 1

echo "$APP_xml$xml</return>"; // no cal res més


//////Merda, no van els subqueries en  els JOIN FINAL


?>
