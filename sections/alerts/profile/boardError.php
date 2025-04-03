<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE owner=$ID");
$IN = "";
while ($CLD_CON->FetchArray()) {
    $IN .= $CLD_CON->GetArrayField("idBooth") . " ,";
}
$IN = substr($IN, 0, -1);
//Alertes de FILM sense solucionar.
$CLD_CON->OpenRs("SELECT b.serialnumber, b.name, ba.`when`, ba.typeAlert FROM App_boothAlert ba LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert=53 ORDER BY ba.`when` DESC");
echo "<div class='inContent'>";
echo "<h1>CONTROL BOARD <span style='background-color:#FF9999;color:#FF0000;'>ERRORS</span></h1>";
echo "<div class='noSolvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    echo "No Board errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
    $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 53) {
          echo "<p class='alertsRed'>$date - PhotoBooth $sn- has a board error</p>";
//          echo "<p class='alertsRed'>$date - PhotoBooth $sn-$name has a board error</p>";
    }
}
echo "</div>";

$CLD_CON->OpenRs("SELECT b.serialnumber, b.name, ba.`when`, ba.typeAlert FROM App_boothAlert ba LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat=2 AND ba.typeAlert=53 ORDER BY ba.`when` DESC");
echo "<h1>SOLVED CONTROL BOARD ERRORS</h1>";
echo "<div class='solvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    echo "No Board errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
    $ty = $CLD_CON->GetArrayField("typeAlert");
    if ($ty == 53) {
//         echo "<p>$date - PhotoBooth $sn-$name had a board error</p>";
         echo "<p>$date - PhotoBooth $sn had a board error</p>";
    }
}
echo "</div>";


echo "</div>";
?>