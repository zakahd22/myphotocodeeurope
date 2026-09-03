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

$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE owner=$ID");
$IN = "";
while ($CLD_CON->FetchArray()) {
    $IN .= $CLD_CON->GetArrayField("idBooth") . " ,";
}
$IN = substr($IN, 0, -1);


$typeAlert  = array(11,52);
$operatorEstat = "<";
$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);


//Alertes de FILM sense solucionar.
$CLD_CON->OpenRs("SELECT b.serialnumber , ba.when ,ba.typeAlert  FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert IN (11,52) ORDER BY ba.when DESC");

$html = <<<HTML
    <div class='inContent'>
    <h1>FILM <span style='background-color:#FACC2E;color:#FF4500;'>ALERTS</span><span style='background-color:#FF9999;color:#FF0000;'>ERRORS</span></h1>
    <div class='noSolvedAlert'>
HTML;
if ($CLD_CON->GetRsRows() == 0) {
    $html .= "No Film alerts";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $date = $CLD_CON->GetArrayField("when");
    $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 52) {
        $html .= "<p class='alertsRed'>$date - The photobooth $sn has a film error</p>";
    } 
    if ($ty == 11) {
        $html .= "<p class='alertsOrange'>$date - The photobooth $sn is running out the film</p>";
    }
}
$html .= "</div>";

//$typeAlert  = array(11,52);
//$operatorEstat = "=";
//$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);

$CLD_CON->OpenRs("SELECT b.serialnumber , b.name, ba.when , ba.typeAlert FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat=2 AND ba.typeAlert IN (11,52) ORDER BY ba.when DESC");
$html .= "<h1>SOLVED FILM ALERTS</h1>";
$html .= "<div class='solvedAlert'>";

if ($CLD_CON->GetRsRows() == 0) {
    echo "No Film alerts";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
    $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 52) {
        $html .= "<p>$date - The photobooth $sn had a FILM ERROR</p>";
    } else {
        $html .= "<p>$date - The photobooth $sn was running out the film</p>";
    }
}
$html .= "</div>";
$html .= "</div>";

echo $html;
