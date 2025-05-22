<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('CLD_boothTypes');
$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');

$USERID_filter = $USERID;

$pbs = $baseController->App_boothsModel->getBooths($USERID_filter); // Get ALL booths

$html = "";

if ($pbs === FALSE || empty($pbs)) {
    $html .= "No results found";
} else {
    foreach ($pbs as $pb) {
        $idBooth = $pb["idBooth"];
        $sn = $pb["serialnumber"];
        $char = $pb["type"];
        $typeID = $pb["CLD_idType"];
        $pbname = $pb["name"]; //20150709pbname
        $location = $pb["location"];

        if (!empty($typeID)) {
            $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeName($typeID);
            if ($boothTypes) $typeName = $boothTypes[0]["name"];
        } else {
            $boothTypes = $baseController->CLD_boothTypesModel->getBoothTypeByChar($char);
            if ($boothTypes) {
                $typeName = $boothTypes[0]["name"];
                $typeName = $boothTypes[0]["id"];
            }
        }
        $boothDongleLmit = $baseController->App_boothDongleModel->boothDongleLmit($idBooth, 1);
        if ($boothDongleLmit) {
            $dongleID = $boothDongleLmit[0]["idDongle"];
            $booth = $baseController->boothsModel->getBoothsByDongle($dongleID);
            if ($booth) $r_string = " - " . $char . $booth[0]["rand_string"];
        } else {
            $r_string = "";
        }

        $html .= "<div class='regBooth grid-item' onclick='setSection(\"photobooths\" ,2 ,$idBooth)'>";
        $html .= "<div class='imgListBooth'>";
        if (empty($typeID)) {
            $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img'>";
        } else {
            if (file_exists(G_PATH . "/images/web/pb/$typeID.png")) {
                $html .= "<img src='images/web/pb/$typeID.png' class='pbs_img'>";
            } else {
                $html .= "<img src='images/web/pb/no-machine.png' class='pbs_img'>";
            }
        }
        $html .= "</div>";
        $html .= "<div class='infoListBooth'>";
        $html .= "<p>S/N : $sn $r_string</p>";
        $html .= "<p>Type : $typeName</p>";
        $html .= "<p>Name : $pbname</p>"; //20150709pbname
        $html .= "<p>Location : $location</p>";
        $html .= "</div>";
        $html .= "</div>";
    }
}

echo $html; // Output the HTML.  This is what's sent back to the AJAX call.
?>