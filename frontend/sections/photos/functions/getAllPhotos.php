<?php
include '../../../common/global.php';
require_once G_PATH . "common/Classes/baseController.php";    //Per fer funcionar ORM
//require_once G_PATH . 'common/conexio.php'; 

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('photos');


$e1234 = $_POST['event'];

$html ="<img src='images/web/close.png' style='position:absolute;right:30px;cursor:pointer;width: 40px;' onclick='closeAllPhotosPopup();' style='display:none;'>";

if ($event = $baseController->eventsModel->getEvent($e1234)) {
    $event_d = $event[0]["start_date"];
    $eventTitle = $event[0]["title"];

    $eventDateFormat = substr($event_d, 4, 2) . "/" . substr($event_d, 6, 2) . "/" . substr($event_d, 0, 4);
    $eventDateFormat = date("F d, Y", strtotime($eventDateFormat));
}

$ruta = "events/" . $event_d . $e1234 . "/";

$photos = $baseController->photosModel->getPhotos($e1234);

//$CLD_CON->OpenRs("SELECT * FROM photos WHERE flag=0 AND event_id=$e1234");
$html .="<h1 style='margin-left:20px;text-shadow:2px 2px 0px black;color:white;text-align:center;'>$eventTitle Gallery</h1>";
$html .="<h2 style='margin-left:20px;text-shadow:2px 2px 0px black;color:white;text-align:center;'>$eventDateFormat</h2>";
foreach ($photos as $photo){
    $code = $photo["code"];
    $img = $ruta . $code . ".jpg";
    $html .="<div class='photoOfAll' onClick='lookPhoto2(\"$code\")'  style='border: 12px ridge white;'>";
    $size = GetImageSize(G_PATH."$img");
    $anchura = $size[0];
    $altura = $size[1];
    if ($anchura < $altura) {
        $html .="<img src='$img'  style='width:100%;'>";
    } 
    else {
        $html .="<img src='$img'  style='height:100%;'>";
    }
    $html .="</div>";
}
   
echo $html;

?>
