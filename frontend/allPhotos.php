<?php
require_once "common/global.php";
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController;
$baseController->createModel('events');
$baseController->createModel('photos');

$_SESSION['eventID'] = 6825;

echo $_SESSION['eventID'];

if(isset($_SESSION['eventID'])){
    $event = $_SESSION['eventID'];
    
    $events = $baseController->eventsModel->getEvent($event);
    
    if($events){
        $event_d = $events[0]["start_date"];
    }
    else{
       echo "No event 2";
       return;
    }
    
    $ruta = $URL . "events/".$event_d . $event . "/";
    
    $photos = $baseController->photosModel->getPhotosByEvent($event, 0);
    
    foreach ($photos as $photo){
        $code = $photo["code"];
        $img = $ruta . $code . ".jpg";
        echo "<div class='photoOfAll' style='width:30%;margin:1%;overflow:hidden;display:inline;float:left;height:250px;cursor:pointer;'>";
        $size = GetImageSize("$img");
        $anchura=$size[0]; 
        $altura=$size[1]; 
        echo "$anchura y $altura";

        echo "<img src='$img'  style='width:100%;'>";
        echo "</div>";
    }
}
else{
    echo "No event";
}
