<?php

include '../../../sessio.php';
error_log( "TO_DELETE sections/phoyobooths/list/owner.php 01" );

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
$pbs = $baseController->App_boothsModel->getBoothsLimit($USERID, $LIMIT);

$pbs_no_limit = $baseController->App_boothsModel->getBooths($USERID);
$totalrows = count($pbs_no_limit);
$owners = array();
array_push($owners, $USERID);
if(!isset($_POST['filPage'])){
    if (isset($_POST['fil'])) {
        
        $sn_f = $_POST['sn'];
        
        $dongle_f = $_POST['dStr'];
        $dongle_f = trim($dongle_f);

        if(strlen($dongle_f) == 4){
            $dongle_f = substr($dongle_f, 1);
        }
        
        if($_POST['idPb'] != 0 && $_POST['idPb'] != ""){
            $idPB_f = $_POST['idPb'];
        } 
        if($_POST['type'] != 0 && $_POST['type'] != ""){
            $tipo_f = $_POST['type'];
        }               
         
        if (!empty($dongle_f)) {
            $idboo = $baseController->boothsModel->getBoothRandString($dongle_f);            
            $idboo = $idboo[0]['idBooth'];
            if(!$idboo){
                $idboo=FALSE;
            }
            
        }        
        if(!empty($idPB_f)){
            $idboo = $idPB_f;
        }  
        utils::log("{$sn_f}, {$tipo_f}, {$stat_f}, {$idboo}, {$distributor}, {$USERID}", "logasd");
        $pbs = $baseController->App_boothsModel->getPbsListFilter($sn_f, $tipo_f, $stat_f, $idboo, $distributor, $owners);
        $totalrows=count($pbs);  
        utils::log("totalrows:  {$totalrows}", "logasd");
        if($idboo === FALSE || empty($pbs)){
            $pbs = NULL;
        }
    } 
    else {
        $pbs = $baseController->App_boothsModel->getBoothsLimit($USERID, $LIMIT);
        $pbs_no_limit = $baseController->App_boothsModel->getBooths($USERID);
        $totalrows = count($pbs_no_limit);
    }
}

$html = "";

if($pbs === FALSE || empty($pbs)){
    $html .= "No results found";
}
else{
    $html .= "<link rel='stylesheet' href='sections/photobooths/resources/css/photobooths.css' type='text/css'>";
    $html .= "<div id='positional_div'></div>";
    
    foreach ($pbs as $pb){
        $idBooth  = $pb["idBooth"];
        $sn       = $pb["serialnumber"];
        $char     = $pb["type"];
        $typeID   = $pb["CLD_idType"];
        $pbname   = $pb["name"];//20150709pbname
        $location = $pb["location"];
        
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
        
        $html .= "<div class='regBooth' onclick='setSection(\"photobooths\" ,2 ,$idBooth)'>";
        $html .= "<div class='imgListBooth'>";
        if(empty($typeID )){
             $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img''>";
        }
        else{
            if(file_exists(G_PATH . "/images/web/pb/$typeID.png")){
                $html .= "<img src='images/web/pb/$typeID.png' class='pbs_img''>";
            }
            else{
                 $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img'>";
            }
        }
        $html .= "</div>";
        $html .= "<div class='infoListBooth'>";
        $html .= "<p>S/N : $sn $r_string</p>";
        $html .= "<p>Type : $typeName</p>";
        $html .= "<p>Name : $pbname</p>";//20150709pbname
        $html .= "<p>Location : $location</p>";
        $html .= "</div>";
        $html .= "</div>";
        
        $available = $CLD_CON->FetchArray();
        
    }
    
    echo $html;
    
}
//else{
//    echo "<br /><center><b> You don't have any photobooth available </b></center>";
//}

    $s = "photobooths";
    $color = "#5882FA";
    include '../../pagescount.php';