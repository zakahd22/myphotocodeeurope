<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');
$baseController->createModel('CLD_Distributors');
$baseController->createModel('rentals');
$baseController->createModel('CLD_Incidents');

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

if(isset($_POST['fil'])){
    $sn_f = $_POST['sn'];
    $owner_f = $_POST['owner'];
    $dongle_f = $_POST['dStr'];
    $dongle_f = trim($dongle_f);
        
    if(strlen($dongle_f) == 4){
        $dongle_f = substr($dongle_f, 1);
    }

    if(!empty($owner_f)){
        $rentals = $baseController->rentalsModel->getRental('%'.$owner_f.'%');
        foreach ($rentals as $rental){
            $idO = $rental["id"];
            if (empty($owners)) {
                array_push($owners, $idO);
            } else {
                array_push($owners, $idO);
            }
        }
    }
    
    if($_POST['type'] != 0 && $_POST['type'] != ""){
        $tipo_f = $_POST['type'];
    }
    if($_POST['status'] != "N" && $_POST['status'] != ""){
        $stat_f = $_POST['status'];
    }
    if($_POST['distributor'] != 0 && $_POST['distributor'] != ""){
        $distributor = $_POST['distributor'];
    }
    elseif($USERTYPE == 3){
        $distributor = $USERID;
    }
    
    
//    $filters .= " AND CLD_Distributor=$USERID";
//    $CLD_CON->OpenRs("SELECT * FROM App_booths WHERE $filters");
//    $select_nolimit ="SELECT * FROM App_booths WHERE $filters";
    $pbs = $baseController->App_boothsModel->getPbsListFilter($sn_f, $tipo_f, $stat_f, $idboo, $distributor, $owners);
    $totalrows=count($pbs);
}
else{
    $select_nolimit = "SELECT * FROM App_booths WHERE CLD_Distributor=$USERID AND CLD_Status>1 ORDER BY serialnumber"; 
    /*Change Select No limit    #152SNL*/
//    $select_nolimit = $baseController->App_boothsModel->getPbsAsDistributor($USERID);
    $pbs = $baseController->App_boothsModel->getPbsAsDistributor($USERID, $LIMIT);
    $count_no_limits = $baseController->App_boothsModel->getPbsAsDistributor($USERID);
    $totalrows = $count_no_limits[0]["counter"];
}


$html = "<link rel='stylesheet' href='sections/photobooths/resources/css/photobooths.css' type='text/css'>";
$html .= "<div id='positional_div'></div>";

foreach ($pbs as $pb){
    $idBooth    = $pb["idBooth"];
    $sn         = $pb["serialnumber"];
    $char       = $pb["type"];
    $typeID     = $pb["CLD_idType"];
    $pbname     = $pb["name"];//20150709pbname
    $location   = $pb["location"];
    $owner      = $pb["owner"];
    $status     = $pb["CLD_Status"];
    
    if (!empty($typeID)) {
        $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeName($typeID);
        if($boothTypes)$typeName = $boothTypes[0]["name"];
    } 
    else{
        $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeByChar($char);
        if($boothTypes){
            $typeName = $boothTypes[0]["name"];
            $typeName = $boothTypes[0]["id"];
        }
    }
    $boothDongleLmit = $baseController->App_boothDongleModel->boothDongleLmit($idBooth, 1);
    if($boothDongleLmit){
        $dongleID = $boothDongleLmit[0]["idDongle"];
        $booth = $baseController->boothsModel->getBoothsByDongle($dongleID);
        //if($booth) $r_string = " - " . $char . $a[0]["rand_string"];
        if($booth) $r_string = " - " . $char . $booth[0]["rand_string"];
    }
    else {
        $r_string = "";
    }
    
    $distributor_request = $baseController->CLD_DistributorsModel->getDistributor($distributor_id);
    if($distributor_request) $distributor = $distributor_request[0]["Name"];
    else $distributor = "Undefined";
    
    $html .= "<div class='regBooth' onclick='setSection(\"photobooths\" ,2 ,$idBooth)' style='height:38%;'>";
    $html .= "<div class='imgListBooth'>";
    if(empty($typeID )){
        $html .= "<img src='images/web/pb/no-machine.png' style='width:80%;max-height:95%;'>";
    }
    else{
        if(file_exists(G_PATH . "/images/web/pb/$typeID.png")){
            $html .= "<img src='images/web/pb/$typeID.png' style='width:80%;margin-left:10%;margin-top:10%;max-height:95%;'>";
        }
        else{
             $html .= "<img src='images/web/pb/no-machine.png' style='width:80%;max-height:95%;'>";
        }
    }
    $html .= "</div>";
    $html .= "<div class='infoListBooth'>";
    $html .= "<p>S/N : $sn $r_string</p>";
    $html .= "<p>Type : $typeName</p>";
    $html .= "<p>Name : $pbname</p>";//20150709pbname
    $html .= "<p>Location : $location</p>";
    $html .= "<p>Status: " . $BOOTHS_TYPE_STATUS[$status]." </p>";
    if($status == 3){
        $html .= "<p>Owner : $ownerName </p>";
    }
    
    $incidents = $baseController->CLD_IncidentsModel->getIncidentsByBooth($idBooth);
    $num_incidents = count($incidents);
    if($num_incidents > 0){
        $html .= "<span class='incidPop'> $num_incidents</span>";;
    }
    
    $html .= "</div>";
    $html .= "</div>";
}

echo $html;

$s = "photobooths";
$color="#5882FA";
include '../../pagescount.php';

