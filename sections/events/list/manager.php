<?php
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('photos');

$eventManagerID = $_SESSION['USERID'];
$events = $baseController->eventsModel->getAllFromEventsListCLD_eventManegerId($eventManagerID);
//$CLD_CON2 = clone($CLD_CON);
//$CLD_CON->OpenRs("SELECT * FROM events WHERE CLD_eventManegerId=$eventManagerID ORDER BY CLD_date_lastPhoto DESC , start_date DESC LIMIT $LIMIT");
echo "<div class='inContent'>";
//while ($CLD_CON->FetchArray()) {
$totalrows = count($events);
foreach($events as $event){
    $trashed = false;
    $idEvent = $event["id"];
    $title = stripcslashes($event["title"]);
    $date = $event["start_date"];
    $date= date("F d, Y", strtotime($date));
    $private = $event["private"];
    if($event["trashed"]){
        $trashed = true;
    }
    if($event["newServer"]){
        $trashed = true;
    }
    $photos = $baseController->photosModel->countPhotosInEvent($idEvent);
//    $CLD_CON2->OpenRs("SELECT id FROM photos WHERE event_id=$idEvent");
//    $numPhotos = $CLD_CON2->GetRsRows();
    $numPhotos = $photos[0]['counter'];
    if($trashed){
        echo "<ul class='regEventUL' onclick='setSection(\"events\" , 2 , $idEvent, $trashed);'>";
    }
    else {
        echo "<ul class='regEventUL' onclick='setSection(\"events\" , 2 , $idEvent);'>";
    }

    echo "<li style='width:30%' title='Event Name'>$title</li>";
    echo "<li style='width:20%' title='Start Date'>$date</li>";
    if ($private == 0) {
        $private2 = "NO";
    } else {
        $private2 = "YES";
    }
   echo "<li style='width:15%'>PRIVATE: $private2</li>";
    echo "<li style='width:15%'>$numPhotos photos</li>";
    echo "<li style='width:10%'> ID:$idEvent</li>";
    echo "<li style='width:10%'>";
        if($trashed){
            echo " <span style='color:white'>*Expired*</span>";
        }
    echo "</li>";
    echo "</ul>";
}
echo "</div>";
$s = "events";
$color="green";
include '../../pagescount.php';
?>
