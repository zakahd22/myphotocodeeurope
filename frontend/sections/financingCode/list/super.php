<?php

$CLD_CON2 = clone($CLD_CON);

$baseController = new baseController();
$baseController->createModel('Fcode_dongle');
$baseController->createModel('Fcode_reg');

$CLD_CON->OpenRs("SELECT Fcode_dongle.idDongle AS idDongle, booths.rand_string AS String, Fcode_dongle.idPB AS idPb, rentals.name AS Owner
                  FROM Fcode_dongle
                  LEFT JOIN booths
                  ON Fcode_dongle.idDongle = booths.id
                  LEFT JOIN rentals
                  ON booths.rental_id = rentals.id");


$html = "<ul class='regDongleUL regDongleUL_Title'>";
$html .= "<li  style='width:25%;'><b>Dongle String</b></li>";
$html .= "<li  style='width:25%;'><b>ID Potobooth</b></li>";
$html .= "<li  style='width:25%;'><b>Owner</b></li>";
$html .= "<li  style='width:25%;'><b>DateEnd</b></li>";
$html .= "</ul>";
    
while ($CLD_CON->FetchArray()) {
    $dateEnd = "-";
    $idDongle = $CLD_CON->GetArrayField("idDongle");
    $string = $CLD_CON->GetArrayField("String");
    $idPb = $CLD_CON->GetArrayField("idPb");
    $Owner = $CLD_CON->GetArrayField("Owner");
    
    $today = date("Y-m-d");
    
    $dong = $baseController->Fcode_regModel->getDateEndFcode($idDongle, $today);

    if(is_array($dong) && !empty($dong)){
        $dateEnd = $dong[0]["dateEnd"];
    }
    
    $html .= "<ul class='regDongleUL' onclick='edit(71 , $idDongle)'>";
    $html .= "<li  style='width:25%;' title='Dongle String'> &nbsp $string</li>";
    $html .= "<li  style='width:25%;' title='ID Potobooth'> &nbsp $idPb</li>";
    $html .= "<li  style='width:25%;' title='Owner'> &nbsp $Owner</li>";
    $html .= "<li  style='width:25%;' title='DateEnd'> &nbsp $dateEnd</li>";
    $html .= "</ul>";
}
$html .= "</div>";

echo $html;

//$s = "dongles";
//$color = "orange";
//include '../../pagescount.php';
