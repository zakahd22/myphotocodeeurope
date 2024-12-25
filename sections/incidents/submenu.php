<?php
/*include '../../sessio.php';
include G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('CLD_Incidents');


//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$type = $_POST['menu'];
$ID = $_POST['id'];
if ($type == 1) {
    if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {
        
        $html .= <<<HTML
        <img src='images/icons/submenu/alertsPb.png' class='dMenu2' onclick='profile("photobooths" , "alerts" , $ID)' id='alerts'>
HTML;
    }
}
if ($type == 2) {
    
$html .= <<<HTML
    <img src='images/icons/submenu/infoPb.png' class='dMenuSelected2' onclick='profile("photobooths" , "info" , $ID)' id='info'>
    <img src='images/icons/submenu/alertsPb.png' class='dMenu2' onclick='profile("photobooths" , "alerts" , $ID)' id='alerts'>
    <img src='images/icons/submenu/events_pb.png' class='dMenu2' onclick='profile("photobooths" , "Events" , $ID)'id='Events_2'>
    <!-- <img src='images/icons/submenu/photos_pb.png' class='dMenu2' onclick='profile("photobooths" , "Photos" , $ID)' id='Photos_2'> -->

    <!-- <img src='images/icons/submenu/reports.png' class='dMenu2' onclick='profile("photobooths" , "Reports" , $ID)' id='Reports'>
    <img src='images/icons/submenu/statistics.png' class='imgMenu2' onclick='profile("photobooths" , "stadistics" , $ID)'> -->
HTML;
if ($_SESSION['USERTYPE'] < 4 || $_SESSION['USERTYPE']==6 ) {

    $html .= <<<HTML
    <img src='images/icons/submenu/incidents_blue.png' class='dMenu2' onclick='profile("photobooths" , "incidents" , $ID)'id='incidents'>
HTML;
    $incidents = $baseController->CLD_IncidentsModel->getIncidents($ID);
    if(!empty($incidents)){
        $num_incid = $incidents[0]["counter"];
                
        if($num_incid > 0){
            $html .= "<span style='z-index:1;font-size: 8pt;color: white;padding: 3 9;background: red;border: 2px solid white;display: inline;border-radius: 213px;float: left;position: relative;left: -16;margin-right: -15px;top: -8px;'> $num_incid</span>";  
        }
    }

   $html .= <<<HTML
    <img src='images/icons/submenu/components.png' class='dMenu2' onclick='profile("photobooths" , "components" , $ID)' id='components'>                                
    <img src='images/icons/submenu/donglePairings.png' class='dMenu2' onclick='profile("photobooths" , "pairings" , $ID)' id='pairings'>
    <img src='images/icons/submenu/history_photobooths.png' class='dMenu2' onclick='profile("photobooths" , "History" , $ID)' id='History'>                                
HTML;
    }
}

echo $html;*/
?>