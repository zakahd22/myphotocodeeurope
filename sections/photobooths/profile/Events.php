<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('App_boothDongle');
$baseController->createModel('events');
$baseController->createModel('photos');

$ID = filter_var($_POST['id'], FILTER_VALIDATE_INT);
$dongles_ids = [];
$i = 0;
$user = FALSE;
$PAGE = 1;
$limit = 24;

$booths = $baseController->App_boothDongleModel->boothDongles($ID);
//$CLD_CON->OpenRs("SELECT * FROM App_boothDongle WHERE idBooth = $ID");
foreach($booths as $booth){
    $array[0] = $booth["idDongle"];
    $array[1] = $booth["datetimeS"];
    $array[2] = $booth["datetimeF"];
    $dongles_ids[$i] = $array;
    $i++;
}
$in = array();
foreach ($dongles_ids as $arrDongle) {
    if (empty($arrDongle[2])) {
        $dateF = "3000-01-01";
    }
    else {
        $dateF = $arrDongle[2];
    }
    $x = 0;
    if($_SESSION['USERTYPE']==4){
        $user =$_SESSION['USERID'];
        $eventsid = $baseController->photosModel->getAllPhotosFromPbs($arrDongle[0], $arrDongle[1], $dateF, $user, true);
    }
    else{
        $eventsid = $baseController->photosModel->getPhotosScript($arrDongle[0], $arrDongle[1], $dateF, true);
    }
    if(is_array($eventsid)) {
        foreach($eventsid as $eventid){
            $events[$x] = [$eventid["event_id"], $eventid["code"]];
            array_push($in, $events[$x][0]);
            $x++;
        }
    }
}
//$in .= "0";
echo "<div class='inContent'>";
echo "<div style='position:fixed;width:34px;height:103px;top:235px;left:175px;'>";
echo "<div style='width:34px;height:34px;background-color:#6BBA70;' title='Active Event'></div>";
echo "<div style='width:34px;height:34px;background-color:#FFCC33;' title=' has received pictures between last week and three months ago'></div>";
echo "<div style='width:34px;height:34px;background-color:#A10326;' title='Didn&#8219;t recive a photo during the last 3 month'></div>";
echo "</div>";

//if ($_SESSION['USERTYPE'] == 4) {
//    $z = count($baseController->eventsModel->getEventsIdIN($in, $_SESSION['USERID']));
//}
//else {
//    $z = count($baseController->eventsModel->getEventsIdIN($in));
//}

$z = isset($events) && is_array($events) ? count($events) : 0;
echo "<p>" . $z . " Events<p>";

if(isset($events) && is_array($events)) {
    foreach ($events as $event1) {
        $event = $event1[0];
        $class = "regEventULRed";
        $eventData = $baseController->eventsModel->getEvent($event);
        //$CLD_CON->OpenRs("SELECT * FROM events WHERE id=$event");
        if ($eventData) {
            $eventData = $eventData[0];
            $event_lastPhoto = $eventData['CLD_date_lastPhoto'];
            $z++;
            
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
            
            $idEvent = $eventData["id"];
            $title = stripcslashes($eventData["title"]);
            $date = $eventData["start_date"];
            $date = date("F d, Y", strtotime($date));
            $private = $eventData["private"];
            
            $photos = $baseController->photosModel->getPhotos($idEvent);
            $numPhotos = is_array($photos) ? count($photos) : 0;
            
            echo "<ul class='$class' onclick='openLink(\"Events\" , $idEvent);'>";
            echo "<li style='width:30%' title='Event Name'>" . htmlspecialchars($title) . "</li>";
            echo "<li style='width:20%' title='Start Date'>$date</li>";
            if ($private == 0) {
                $private2 = "NO";
            } else {
                $private2 = "YES";
            }
            echo "<li style='width:20%'>PRIVATE: $private2</li>";
            echo "<li style='width:20%'>$numPhotos photos</li>";
            echo "</ul>";
        }
    }
}

echo "</div>";
?>