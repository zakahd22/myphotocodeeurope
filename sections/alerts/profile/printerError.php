<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_booths');


$ID = $_POST['id'];
$boths = $baseController->App_boothsModel->getBooths($ID);

$i = 0;
foreach ($boths as $both){
    $arrayBooths[$i]= $both["idBooth"];
    $i++;
}

/**/
$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE owner = $ID");
$IN = "";
while ($CLD_CON->FetchArray()) {
    $IN .= $CLD_CON->GetArrayField("idBooth") . " ,";
}
$IN = substr($IN, 0, -1);
/**/


$typeAlert  = array(51);
$operatorEstat = "<";
$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);

//Alertes de FILM sense solucionar.
$CLD_CON->OpenRs("SELECT b.serialnumber, b.name, ba.when, ba.typeAlert FROM App_boothAlert ba LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert=51 ORDER BY ba.when DESC");
$html .= "<div class='inContent'>";
$html .="<h1>PRINTER <span style='background-color:#FF9999;color:#FF0000;'>ERRORS</span></h1>";
$html .="<div class='noSolvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    $html .="No Printer errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
    $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 51) {
//         $html .= "<p class='alertsRed'>$date - PhotoBooth $sn-$name  has a printer error</p>";
         $html .= "<p class='alertsRed'>$date - PhotoBooth $sn  has a printer error</p>";
    }
    
}
$html .= "</div>";

$typeAlert  = array(51);
$operatorEstat = "=";
$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);

$CLD_CON->OpenRs("SELECT b.serialnumber, b.name, ba.when, ba.typeAlert FROM App_boothAlert ba LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert=51 ORDER BY ba.when DESC");
$html .= "<h1>SOLVED PRINTER ERRORS</h1>";
$html .= "<div class='solvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    $html .= "No Printer errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
        $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 51) {
         $html .= "<p>$date - PhotoBooth $sn  had a printer error</p>";
//         $html .= "<p>$date - PhotoBooth $sn-$name  had a printer error</p>";
    }
}
$html .= "</div>";
$html .= "</div>";

echo $html;