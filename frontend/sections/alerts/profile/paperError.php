<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_booth');

$ID = $_POST['id'];

$boths = $baseController->App_boothModel->getBooths($ID);
$i = 0;
foreach ($boths as $both){
    $arrayBooths = $both[$i];
    $i++;
}

$ID = $_POST['id'];
$CLD_CON->OpenRs("SELECT idBooth FROM App_booths WHERE owner=$ID");
$IN = "";
while ($CLD_CON->FetchArray()) {
    $IN .= $CLD_CON->GetArrayField("idBooth") . " ,";
}
$IN = substr($IN, 0, -1);

//$request = $baseController->App_boothModel->getBoothAndBoothAlerts($arrayBooths);


//Alertes de FILM sense solucionar.
$CLD_CON->OpenRs("SELECT b.serialnumber , b.name, ba.when FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat<2 AND ba.typeAlert=52 ORDER BY ba.when DESC");
echo "<div class='inContent'>";
echo "<h1>New Paper errors</h1>";
echo "<div class='noSolvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    echo "No Paper errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");

    echo "<p>The photobooth $sn has a PAPER ERROR from $date</p>";
//    echo "<p>The photobooth $sn-$name has a PAPER ERROR from $date</p>";
}
echo "</div>";

$CLD_CON->OpenRs("SELECT b.serialnumber , b.name ba.when FROM App_boothAlert ba   LEFT JOIN App_booths b ON b.idBooth = ba.idBooth WHERE ba.idBooth IN($IN) AND ba.estat=2 AND ba.typeAlert=52 ORDER BY ba.when DESC");
echo "<h1>Old Paper errors</h1>";
echo "<div class='solvedAlert'>";
if($CLD_CON->GetRsRows()==0){
    echo "No Paper errors";
}
while ($CLD_CON->FetchArray()) {
    $sn = $CLD_CON->GetArrayField("serialnumber");
    $name = $CLD_CON->GetArrayField("name");
    $date = $CLD_CON->GetArrayField("when");
    echo "<p> $date - The PhotoBooth $sn had a PAPER ERROR.</p>";
//    echo "<p> $date - The PhotoBooth $sn-$name had a PAPER ERROR.</p>";
}
echo "</div>";


echo "</div>";
?>