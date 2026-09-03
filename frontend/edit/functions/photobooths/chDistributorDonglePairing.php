<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["dades"]);

utils::log($json, "log_edi");

$idDongle = $json[0];
$idDistributor = $json[1];
$idBooth = $json[2];

utils::log($idDongle, "log_edi");
utils::log($idDistributor, "log_edi");
utils::log($idBooth, "log_edi");

$result = "Error";


$sql = "UPDATE booths SET CLD_Distributor = $idDistributor WHERE id = $idDongle";

utils::log($sql, "log_edi");

if($CLD_CON->Execute($sql)){
    $CLD_CON->OpenRs(
        "SELECT  App_boothDongle.idDongle,  booths.rand_string, rentals.name AS owner_name, CLD_Distributors.Name AS distributor_name , App_boothDongle.datetimeS, App_boothDongle.datetimeF
        FROM App_boothDongle
        LEFT JOIN booths
        ON booths.id = App_boothDongle.idDongle
        LEFT JOIN App_booths
        ON App_booths.idBooth = App_boothDongle.idBooth
        LEFT JOIN rentals
        ON rentals.id = booths.rental_id
        LEFT JOIN CLD_Distributors
        ON CLD_Distributors.id = booths.CLD_Distributor
        WHERE App_boothDongle.idBooth = $idBooth;"
    );
    
    
    if($CLD_CON->GetRsRows()){
        $i = 0;
        $donglepairing_array = array();
        while ($CLD_CON->FetchArray()) {
            $donglepairing_array[$i] = array();
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("idDongle"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("rand_string"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("owner_name"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("distributor_name"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("datetimeS"));
            array_push($donglepairing_array[$i], $CLD_CON->GetArrayField("datetimeF"));
            $i ++; 
       }
    } 
    
    $html .= "<table class='matching_table'> ";
        $html .= "<tr>";
            $html .= "<td>String</td>";
            $html .= "<td>Owner</td>";
            $html .= "<td>Distributor</td>";
            $html .= "<td>Start Date</td>";
            $html .= "<td>Finish Date</td>";
            $html .= "<td></td>";
        $html .= "</tr>";
    for ($i = 0; $i < count($donglepairing_array); $i++) {
        $html .= "<tr>";
            $html .= "<td>{$donglepairing_array[$i][1]}</td>";
            $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][2]}<img src='images/web/edit.png' class='edit_owner' pb='$idBooth' id='{$donglepairing_array[$i][0]}'></img></div></td>";
            $html .= "<td><div class='cel_text_img'>{$donglepairing_array[$i][3]}<img src='images/web/edit.png' class='edit_distri' pb='$idBooth' id='{$donglepairing_array[$i][0]}'></img></div></td>";
            $html .= "<td>{$donglepairing_array[$i][4]}</td>";
            $html .= "<td>{$donglepairing_array[$i][5]}</td>";
            $html .= "<td><div class='delete' pb='{$idBooth}' id='{$donglepairing_array[$i][0]}'></div></td>";
        $html .= "</tr>";
    }
    $html .= "</table>";

    
    $result = $html;
}

echo json_encode($result);