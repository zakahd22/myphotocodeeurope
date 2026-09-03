<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";


$baseController = new baseController();
$baseController->createModel('App_booths');
$baseController->createModel('CLD_historyBooth');

$id = $_POST['id'];

$booth = $baseController->App_boothsModel->getBoothWhereid($id);
if($booth){$sn = $booth[0]["serialnumber"];}
$html = "<h1> History of photobooth $sn :</h1>";
$html .= "<div style='width:80%;height:76%;overflow:auto;margin-left:10%;border:2px solid #378DE8;'>";


$historyBooths = $baseController->CLD_historyBoothModel->geHistoryBooth($id);

if($historyBooths){
    foreach ($historyBooths as $historyBooth){
        $coment = stripslashes($historyBooth["comment"]);
        $dat    = $historyBooth["data"];
        
        $data = date("F d, Y | H:i:s", strtotime($dat));
        
        $html .= "<p>$data - $coment</p>";
        $html .= "<hr>";
    }
}
//echo "". print_r(error_get_last());
$html .= "</div>";

echo $html;
