<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$CLD_CON2 = clone($CLD_CON);

$baseController = new baseController();
$baseController->createModel('rentals');
$baseController->createModel('events');
$baseController->createModel('photos');

$ID =$_POST['id'];

$events = $baseController->eventsModel->getAllFromEventsList($ID, false);

$num_events = count($events);
$html = "<div class='inContent'>";
$html .= "<p> This owner made $num_events Events. </p>";

foreach ($events as $event){
    $event_numPhotos = $event["counter"];
    $eventLastPhoto = $event["CLD_date_lastPhoto"];
    $event_id = $event["id"];
    $event_title = stripcslashes($event["title"]);
    $event_date = date("F d, Y", strtotime($event["start_date"]));
    $event_private = $event["private"];
    $event_lastPhoto = $event["CLD_date_lastPhoto"];
    
    $photos = $baseController->photosModel->countPhotosInEvent($event_id);
    $event_numPhotos = $photos[0]["counter"];
    
    $event_private == 0? $event_private = "NO" : $event_private = "YES";

    $fecha = date("Y-m-d");
    $DateDiff = date_diff(date_create($fecha), date_create($event_lastPhoto));

    if($event_lastPhoto == null){
        $class = "regEventULRed";
    }
    else{
        $month = $DateDiff->days / 30;
        if($month > 3){
            $class = "regEventULRed";
        }
        elseif($DateDiff->days > 7){
            $class = "regEventULAmbar";
        }
        else{
            $class = "regEventUL";
        }
    }

    $html .= <<<HTML
       <ul class='$class' onclick='openLink("Events" , $event_id);'> 
            <li style='width:30%' title='Event Name'>$event_title</li>
            <li style='width:20%' title='Start Date'>$event_date</li>
            <li style='width:20%'>PRIVATE: $event_private</li>
            <li style='width:20%'>$event_numPhotos photos</li>
            <li style='width:10%'> ID:$event_id</li>
        </ul>
HTML;
}

$html .= "</div>";

echo $html;
