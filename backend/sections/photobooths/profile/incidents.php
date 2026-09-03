<?php
include '../../../sessio.php';

require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('CLD_Incidents');
$baseController->createModel('App_infoDeviceMgt');


$ID = $_POST['id'];

echo "<div class='inContent'>";
echo "<div class='boxLeft'>";
echo "<h1>Incidents";
if ($_SESSION['USERTYPE']!=6) {
        echo "<input type='button' class='miniAdd' onclick='edit(38 , $ID)'>";
    }

echo "</h1>";   
echo "<hr>";
echo "<div style='width:100%;height:80%;overflow:auto;'>"; 


$incidents = $baseController->CLD_IncidentsModel->getIncidentsBooth($ID);

foreach ($incidents as $incident){
    
    $in_id  = $incident["id"];
    $coment = $incident["coment"];
    $date   = $incident["datetime"];
    $code   = $incident["code"];
    $user   = $incident["user"];
    $status = $incident["status"];
    
    $coment = stripslashes($coment);
    
    $html .= "<div class='incReg' onclick='showComents($in_id)'>";
    $html .= "<p style='margin-top:0px;margin-bottom:10px;border-bottom:1px solid white;' >";
    $html .=  "$code , $date";
    $html .=  "<span style='width:45%;margin-right:4%;float:right;text-align:right;'>";
    $html .=  "$user";
    $html .=  "</span>";
    $html .=  "</p>";
    $html .=  "<p style='margin-top:5px;margin-bottom:5px;margin-left:4%;margin-right:5%;'>";
    
    if(strlen($coment)>150) $coment = substr( $coment , 0 , 150) . "...";
    
    $html .=  $coment;
    $html .=  "</p>";
    $html .=  "<p style='margin-top:10px;margin-bottom:0px;text-align:right;border-top:1px solid white;'>";
    
    if($status == 0) $html .=  "Not Solved";
    if($status == 1) $html .=  "Seen by SuperUser";
    if($status == 2)$html .=  "Solved";
    
    $html .=  "</p>";    
    $html .=  "</div>";
}
if(!$incidents) $html = "<p style='text-align:center;'>This PhotoBooth does not have any incidents.</p>";

$html .= "</div>";
$html .="<hr>";
$html .="</div>";
$html .="<div class='boxRight'>";
$html .="<div class='boxRight2'>";
$html .="</div>";
/***
 * Afegim apartat DeviceMgt
 */

$html .= "<h1 style='margin-left: -5px; margin-top: 30px;'>Device alerts";
$html .= "</h1>";   
$html .= "<hr>";
$html .= "<div style='width:100%;height:80%;overflow:auto;'>"; 



$deviceAlerts = $baseController->App_infoDeviceMgtModel->getDeviceAlertsByBoothHtml($ID);

$html .=$deviceAlerts;


/*
 * Fi apartat DeviceMgt
 */
$html .="</div>";
$html .="</div>";



echo $html;


?>


<script>
    function showComents(id){
         var ajaxData = {id: id};
                $.ajax({
                    url: 'sections/photobooths/functions/getIncidentComents.php',
                    type: 'POST',
                    success: function(data) {
                        $(".boxRight2").html(data);
                    },
                    data: ajaxData,
                    contentType: 'application/x-www-form-urlencoded'
                });         
        
    } 
</script>