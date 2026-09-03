<?php

include '../../sessio.php';
require_once G_PATH . "common/Classes/baseController.php";

/*Delete*/
$CLD_CON2 = clone($CLD_CON);
/**/

$baseController = new baseController();
$baseController->createModel('App_infoDeviceMgt');
$sn = 0;
if(isset($_POST['sn'])){
  $sn = $_POST['sn'];   
}
echo "<div class='inContent'>";
echo "<div class='boxLeft'>";
echo "<h1>Device Alerts";
echo "</h1>";   
echo "<hr>";
echo "<div style='width:100%;height:80%;overflow:auto;'>"; 

$html = $baseController->App_infoDeviceMgtModel->getDeviceAlertsAllPBsHtml($sn);

$html .= "</div>";
$html .="<hr>";
$html .="</div>";
$html .="<div class='boxRight'>";
$html .="</div>";

$html .="</div>";


echo $html;
?>
<script> 
</script>