<?php
include '../../../sessio.php';

require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_boothDongle');
$baseController->createModel('booths');

$ID = $_POST['id'];
$CLD_CON2 = clone($CLD_CON);

echo "<div class='inContent'>";
echo "<h1>Dongles Pairings";
if ($_SESSION['USERTYPE']<3) {
echo "<input type='button' class='miniAdd' onClick='edit(37 , $ID);'>";
}
echo "</h1>";
//echo "<p>Text descriptiu ... </p>";
echo "<div style='width:80%;height:60%;overflow:auto;margin-left:10%;border:1px solid gray;padding-top:10px;'>";



$dongles = $baseController->App_boothDongleModel->boothDongles($ID);
foreach ($dongles as $dongle) {
    $idDongle = $dongle["idDongle"];
    $datetimeS = $dongle["datetimeS"];
    $datetimeF = $dongle["datetimeF"];

    if (empty($datetimeF)) {
        $datetimeF = " No End Date ";
    } else {
        $datetimeF = date("F d, Y", strtotime($datetimeF));
    }
    $datetimeS = date("F d, Y", strtotime($datetimeS));
    
    $randString = $baseController->boothsModel->getBoothsByDongle($idDongle);
    if ($randString){
        $randString = $randString[0]["rand_string"];
        echo "<ul class='regPairUL'>";
        echo "<li style='width:30%;height:32px;'>$randString</li>";
        echo "<li style='width:30%;height:32px;text-align:center;'>$datetimeS</li>";
        echo "<li style='width:5%%;height:32px;text-align:center;'><img src='images/web/flechasAzules.png' style='width:32px;height:32px;'></li>";
        echo "<li style='width:30%;height:32px;text-align:center;'>$datetimeF</li>";
        echo "</ul>";
    }
    
    
}
echo "</div>";

//$CLD_CON->OpenRs("SELECT idDongle  , datetimeS , datetimeF FROM App_boothDongle WHERE idBooth = $ID ORDER BY datetimeS DESC");
//
//while ($CLD_CON->FetchArray()) {
//    $idDongle = $CLD_CON->GetArrayField("idDongle");
//    $datetimeS = $CLD_CON->GetArrayField("datetimeS");
//    $datetimeF = $CLD_CON->GetArrayField("datetimeF");
//
//    if (empty($datetimeF)) {
//        $datetimeF = " No End Date ";
//    } else {
//        $datetimeF = date("F d, Y", strtotime($datetimeF));
//    }
//    $datetimeS = date("F d, Y", strtotime($datetimeS));
//    $CLD_CON2->OpenRs("SELECT rand_string FROM booths WHERE id=$idDongle");
//    if ($CLD_CON2->FetchArray()){
//        $randString = $CLD_CON2->GetArrayField("rand_string");
//        
//        echo "<ul class='regPairUL'>";
//        echo "<li style='width:30%;height:32px;'>$randString</li>";
//        echo "<li style='width:30%;height:32px;text-align:center;'>$datetimeS</li>";
//        echo "<li style='width:5%%;height:32px;text-align:center;'><img src='images/web/flechasAzules.png' style='width:32px;height:32px;'></li>";
//        echo "<li style='width:30%;height:32px;text-align:center;'>$datetimeF</li>";
//        echo "</ul>";
//    }
//    
//    
//}
//echo "</div>";
//
//
//

echo "</div>";
?>