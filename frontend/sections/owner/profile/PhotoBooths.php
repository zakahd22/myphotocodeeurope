<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 

$ID =$_POST['id'];

$baseController = new baseController;
$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('App_boothDongle');
$baseController->createModel('CLD_Incidents');
$baseController->createModel('booths');


$pbs = $baseController->App_boothsModel->getBooths($ID);

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

$html = "";
foreach ($pbs as $pb){
    $idBooth = $pb["idBooth"];
    $sn = $pb["serialnumber"];
    $char = $pb["type"];
    $typeID = $pb["CLD_idType"];
    $pbname = $pb["name"];//20150709pbname
    $location = $pb["location"];

    if (!empty($typeID)) {
        $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeName($typeID);
        if($boothTypes){
            $typeName = $boothTypes[0]["name"];
        }
    }
    else{
        $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeByChar($char);
        if($boothTypes){
            $typeName = $boothTypes[0]["name"];
            $typeID = $boothTypes[0]["id"];
        }
    }
    
    $dongle = $baseController->App_boothDongleModel->boothDongleLmit($idBooth, 1);
    if($dongle){
        $dongleID = $dongle [0]["idDongle"];
        
        $booth = $baseController->boothsModel->getBoothsByDongle($dongleID);
        if($booth){
             $r_string = " - " . $char . $booth[0]["rand_string"];
        }
    }
    else{
        $r_string  = "";
    }
    
    $html .= "<div class='regBooth' onclick='openLink(\"PhotoBooths\",$idBooth);'>";
    $html .= "<div class='imgListBooth'>";
    $html .= "<img src='images/web/pb/$typeID.png' style='width:80%;margin-left:10%;margin-top:10%;max-height:95%;'>";
    $html .= "</div>";
    $html .= "<div class='infoListBooth'>";
    $html .= "<p>S/N : $sn $r_string</p>";
    $html .= "<p>Type : $typeName</p>";
    $html .= "<p>Name : $pbname</p>";
    $html .= "<p>Location : $location</p>";
    
    $incidents = $baseController->CLD_IncidentsModel->getIncidents($idBooth);
    $num_incidents = $incidents[0]["counter"];
    if ($num_incidents > 0) {
        $html .= "<span class='incidPop'> $num_incidents</span>";
    }
    
    $html .= "</div>";
    $html .= "</div>";
}

echo $html;

