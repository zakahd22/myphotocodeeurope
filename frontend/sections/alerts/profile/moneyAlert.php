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

/*Delete*/
//$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE owner=$ID");
//$IN = "";
//while ($CLD_CON->FetchArray()) {
//    $IN .= $CLD_CON->GetArrayField("idBooth") . " ,";
//}
//$IN = substr($IN, 0, -1);
/*End delete*/

$typeAlert  = array(12);
$operatorEstat = "<";
$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);

//Alertes de FILM sense solucionar.
$html .= "<div class='inContent'>";
$html .= "<h1>CASH BOX <span style='background-color:#FACC2E;color:#FF4500;'>ALERTS</span></h1>";
$html .= "<div class='noSolvedAlert'>";

//$CLD_CON->OpenRs("SELECT b.serialnumber , ba.when , ba.typeAlert FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert=12 ORDER BY ba.when DESC");
//if($CLD_CON->GetRsRows()==0){
//    $html .= "No Cash box alerts";
//}
//while ($CLD_CON->FetchArray()) {
//    $sn = $CLD_CON->GetArrayField("serialnumber");
//    $date = $CLD_CON->GetArrayField("when");
//    $ty = $CLD_CON->GetArrayField("typeAlert");
//    if ($ty == 12) {
//        $html .= "<p class='alertsOrange'>$date - PhotoBooth $sn :  CashBox is getting full  </p>";
//    }
//   
//}
if(count($request['App_boothAlert'])==0){
    $html .= "No Cash box alerts";
}
else{
    $max = count($request['App_boothAlert']);
}
for($i=0; $i < $max; $i++){
    $sn = $request["App_booths"][$i]["serialnumber"];
    $name = $request["App_booths"][$i]["name"];
    $date = $request["App_boothAlert"][$i]["when"];
    $ty = $request["App_boothAlert"][$i]["typeAlert"];
    if ($ty == 12) {
        $html .= "<p class='alertsOrange'>$date - PhotoBooth $sn-$name :  CashBox is getting full  </p>";
    }
   
}
$html .= "</div>";


$typeAlert  = array(12);
$operatorEstat = "=";
$request = $baseController->App_boothsModel->getBoothAndBoothAlerts($arrayBooths, $typeAlert, $operatorEstat);

$html .= "<h1>SOLVED CASHBOX ALERTS</h1>";
$html .= "<div class='solvedAlert'>";

if(count($request['App_boothAlert'])==0){
    $html .= "No CashBox alerts";
}
else{
    $max = count($request['App_boothAlert']);
}
for($i=0; $i < $max; $i++){
    $sn = $request["App_booths"][$i]["serialnumber"];
    $name = $request["App_booths"][$i]["name"];
    $date = $request["App_boothAlert"][$i]["when"];
    $ty = $request["App_boothAlert"][$i]["typeAlert"];
    if ($ty == 12) {
        $html .= "<p class='alertsOrange'>$date - PhotoBooth $sn-$name :  CashBox is getting full  </p>";
    }
   
}

//$CLD_CON->OpenRs("SELECT b.serialnumber , ba.when , ba.typeAlert FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat=2 AND ba.typeAlert=12 ORDER BY ba.when DESC");
//if($CLD_CON->GetRsRows()==0){
//    $html .= "No CashBox alerts";
//}
//while ($CLD_CON->FetchArray()) {
//    $sn = $CLD_CON->GetArrayField("serialnumber");
//    $date = $CLD_CON->GetArrayField("when");
//        $ty = $CLD_CON->GetArrayField("typeAlert");
//    if ($ty == 12) {
//         $html .= "<p>$date - PhotoBooth $sn :  CashBox was getting full  </p>";
//    }
//}
$html .= "</div>";
$html .= "</div>";

echo $html;