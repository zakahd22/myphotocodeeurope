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
$baseController->createModel('App_infoDeviceMgt');


$owners = array();
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
if(isset($_POST['filPage'])){
//    $where = $_SESSION['WH'];
//    $CLD_CON->OpenRs("SELECT * FROM App_booths $where LIMIT $LIMIT");
//    $select_nolimit ="SELECT * FROM App_booths $where";
}
else{
    if (isset($_POST['fil'])) {
        $filters = "";

        $sn_f = $_POST['sn'];
        $owner_f = $_POST['owner'];
        $dongle_f = $_POST['dStr'];
        $dongle_f = trim($dongle_f);
        
        if(strlen($dongle_f) == 4){
            $dongle_f = substr($dongle_f, 1);
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
        if($_POST['idPb'] != 0 && $_POST['idPb'] != ""){
            $idPB_f = $_POST['idPb'];
        }
        $hasUPGRADEidFilter = 0;        
        if($_POST['UPGRADEid'] != "0" && $_POST['UPGRADEid'] != ""){            
            $UPGRADEid = $_POST['UPGRADEid'];
            $hasUPGRADEidFilter = 1;
        }
        
        if (!empty($owner_f)) {
            $rentals = $baseController->rentalsModel->getRental('%'.$owner_f.'%');
            foreach ($rentals as $rental){
                $idO = $rental["id"];
                if (empty($owners)) {
                    array_push($owners, $idO);
                    $owner_null = 1;
                } else {
                    array_push($owners, $idO);
                }
            }
        }
 
        if (!empty($dongle_f)) {
            $idboo = $baseController->boothsModel->getBoothRandString($dongle_f);
            
            $idboo = $idboo[0]['idBooth'];
            if(!$idboo){$idboo=FALSE;}
        }
        
        if(!empty($idPB_f)){
            $idboo = $idPB_f;
        }
        if($_SESSION['USERTYPE'] == 2){
            $PbsIds = $baseController->boothsModel->getBoothRandStringManufacturer($dongle_f);
            
            if($PbsIds){
                $idPb = FALSE;
                $arrayPbsIds = [];
                foreach ($PbsIds as $PbsId){
                    array_push($arrayPbsIds, $PbsId["idBooth"]);
                }
            }
            else{
               $arrayPbsIds = FALSE;
               $idPb = $idboo;
            }
            
            $pbs = $baseController->App_boothsModel->getPbsListFilterManufacturer($sn_f, $tipo_f, $stat_f, $distributor, $owners, $arrayPbsIds, $idPb);
            $totalrows=count($pbs); 
        }
        else{
            if($hasUPGRADEidFilter){                       
                $pbs = $baseController->App_boothsModel->getPbsListFilterWithUPGRADEid($sn_f, $tipo_f, $stat_f, $idboo, $distributor, $owners, $UPGRADEid);
            }else{                
                $pbs = $baseController->App_boothsModel->getPbsListFilter($sn_f, $tipo_f, $stat_f, $idboo, $distributor, $owners);
            }    
            
            
            $totalrows=count($pbs); 
        }    
        if($idboo === FALSE && empty($pbs) && $owner_null == 1 ){
            $pbs = NULL;
        }
    } 
    else {
        $pbs = $baseController->App_boothsModel->getAllPbs($LIMIT);
        $count_no_limits = $baseController->App_boothsModel->getAllPbs();
        $totalrows = $count_no_limits[0]["counter"];
    }
}

$html = "";

if(empty($pbs)){
    $html .= "No results found";
}
else{
    $html .= "<link rel='stylesheet' href='sections/photobooths/resources/css/photobooths.css' type='text/css'>";
    $html .= "<div id='positional_div'></div>";

    foreach ($pbs as $pb){
        $idBooth = $pb["idBooth"];
        $sn = $pb["serialnumber"];
        $char = $pb["type"];
        $typeID = $pb["CLD_idType"];
        $pbname = $pb["name"];//20150709pbname
        $location = $pb["location"];
        $owner = $pb["owner"];
        $status = $pb["CLD_Status"];
        $distributor_id = $pb["CLD_Distributor"];

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


        $rental = $baseController->rentalsModel->getRentalsById($owner);
        if($rental) $owner = $rental[0]["name"];
        else $owner = "Undefined";

        $html .= "<div class='regBooth' onclick='setSection(\"photobooths\" ,2 ,$idBooth)'>"; 
        $html .= "<div class='imgListBooth'>";
        if(empty($typeID )){
            $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img'>";
        }
        else{
            if(file_exists(G_PATH . "images/web/pb/$typeID.png")){
                $html .= "<img src='images/web/pb/$typeID.png' class='pbs_img''>";
            }
            else{
                 $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img''>";
            }
        }
        $html .= "</div>";
        $html .= "<div class='infoListBooth'>";
        $html .= "<p>S/N : $sn $r_string</p>";
        $html .= "<p>Type : $typeName</p>";
        $html .= "<p>Name : $pbname</p>";//20150709pbname
        $html .= "<p>Location : $location</p>";
        if ($status == 0) {
            $html .= "<p>Status: $BOOTHS_TYPE_STATUS[0]</p>";
        }
        if ($status == 1) {
            $html .= "<p>Status: $BOOTHS_TYPE_STATUS[1]</p>";
        }
        if ($status == 2) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[2]</p>";
        }
        if ($status == 3) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[3]</p>";
            $html .= "<p>Owner : $owner</p>";
        }
        if ($status == 4) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[4]</p>";
        }
        if ($status == 5) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[5]</p>";
        }
        if ($status == 6) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[6]</p>";
        }
        if ($status == 5) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[7]</p>";
        }
        if ($status == 8) {
            $html .= "<p>Status: Distributor $distributor - $BOOTHS_TYPE_STATUS[8]</p>";
        }

        $incidents = $baseController->CLD_IncidentsModel->getIncidentsByBooth($idBooth);
        $num_incidents = count($incidents);
        $deviceAlerts = $baseController->App_infoDeviceMgtModel->getActiveDeviceAlertsByBooth($idBooth);

        if($num_incidents > 0){
            
            $html .= "<span class='incidPop' onclick='profile(\"photobooths\" , \"incidents\" , $idBooth);' style='z-index:10;display:block;'>$num_incidents</span>";
        }
        if($deviceAlerts > 0){
            
            $html .= "<span class='devicePop' onclick='profile(\"photobooths\" , \"incidents\" , $idBooth);' style='z-index:10;display:block;'>$deviceAlerts</span>";
       }
        $html .= "</div>";
        $html .= "</div>";
    }
    
    $s = "photobooths";
    $color = "#5882FA";
    include '../../pagescount.php';
}
    echo $html;
    
