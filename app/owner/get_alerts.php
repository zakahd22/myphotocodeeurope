<?php
require("common.php");

/* repàs dels codis d'alertes
 * 
 * Alertes / errors configurables només des de pantalles navegador standar
 * 1: Alerta offline, cal indicar la franja horària 
 * 
 * Alertes configurables des de l'App
 * 11: FILM (Run out of film) llista de valors al combo: none#none#50#50#75#75#100#100 (és #valor#etiqueta)
 * 12: MONEY () llista de valors al combo: none#none#200#$200#300#$300 (és #valor#etiqueta)
 * 
 * Errors (integrats a alertes el maig de 2013)
 * 51: Printer
 * 52: Paper
 * 53: Board
 * 54: Camera
 * 
 */


if(!$APP_user) return;



//20130926 INICI
//$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
//    FROM `App_boothDongle` INNER JOIN booths ON App_boothDongle.idDongle = booths.id
//    WHERE rental_id = $APP_userId ORDER BY datetimeS DESC) AS myBoothDongle";

$myBoothDongle = " (SELECT DISTINCT `idBooth`, `idDongle`, booths.`rand_string`
    FROM `App_boothDongle` INNER JOIN booths ON App_boothDongle.idDongle = booths.id
    WHERE rental_id = $APP_userId  AND `datetimeF` IS NULL) AS myBoothDongle";

//20130926 FINAL


//SELECT `id`, `idBooth`, `typeAlert`, `when`, `status` FROM `App_boothAlert` WHERE 1
        
//NOTA: de moment totes OK
$sql = "SELECT App_booths.idBooth, booth_types.name, App_booths.name, myBoothDongle .rand_string, App_boothAlert.estat,
    App_boothAlert.`when`, App_alerts.`textAlert`
    FROM (((App_boothAlert LEFT JOIN App_booths ON App_boothAlert.idBooth = App_booths.idBooth)
    LEFT JOIN booth_types ON App_booths.type = booth_types.`char`)
    LEFT JOIN $myBoothDongle ON App_booths.idBooth = myBoothDongle.idBooth)
    LEFT JOIN `App_alerts` ON `App_boothAlert`.`typeAlert` = `App_alerts`.`typeAlert`
    WHERE owner=$APP_userId ORDER BY App_boothAlert.estat,App_boothAlert.`when` DESC; ";
//    WHERE App_boothAlert.estat < 2 AND owner=$APP_userId ORDER BY App_boothAlert.estat,App_boothAlert.`when` DESC; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0002a </comm_status></return>";
return;
}

$xml = $APP_xmlOKcomm;
$xml.= "<alerts>";
while($APP_BdD->FetchRs()){
    $xml.= "<alert>";
    $idBooth =  $APP_BdD->GetField(1);
    $xml.= "<booth_id>$idBooth</booth_id>";
    $tmp = APP_preparaXML($APP_BdD->GetField(2));
    $xml.= "<booth_type>$tmp</booth_type>";
    $tmp = APP_preparaXML($APP_BdD->GetField(3));
    $xml.= "<booth_name>$tmp</booth_name>";
    $tmp = $APP_BdD->GetField(4);
    $xml.= "<booth_code>$tmp</booth_code>";
    $tmp = $APP_BdD->GetField(5);
    $xml.= "<alert_status>{$APP_estatsAlert[$tmp]}</alert_status>";
    
    $xml.= "<alert_text>";
//    $tmp = $APP_BdD->GetFieldDateTime(6);
//    $xml.= "<trace>".$tmp->format("Y m-d H:i")."</trace>";
    $tmp = $APP_BdD->GetFieldDateTime(6);
    //$xml.= "<trace>".var_dump($tmp)."</trace>";
    if($tmp){
     $xml.= $tmp->format("m-d-Y H:i ");
    }
//    else{
//
//    }
    
    $tmp = $APP_BdD->GetField(7);
    $xml.= "$tmp</alert_text>";

    $xml.= "</alert>";

}
$APP_BdD->CloseRs();
$xml.= "</alerts>";

echo "$APP_xml$xml</return>"; // no cal res més



?>
