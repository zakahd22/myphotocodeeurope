<?php
include '../../sessio.php';
require_once G_PATH . "common/Classes/baseController.php";

$baseController = new baseController();
$baseController->createModel('rentals');
$baseController->createModel('events');
$baseController->createModel('photos');

$CLD_CON2 = clone($CLD_CON);

if (isset($_POST['fil'])) {
    
    $tt = $_POST['title'];
    $id = $_POST['id']; 
    $owner_f = $_POST['owner'];
    $filters = "";
    $x=false;
    if (!empty($owner_f)) {
        $rentals = $baseController->rentalsModel->getRental('%'.$owner_f.'%');
//        $CLD_CON->OpenRs("SELECT id FROM rentals WHERE name LIKE '%$owner_f%'");
//        $in = "";
//        while ($CLD_CON->FetchArray()) {
        $in = array();
        foreach ($rentals as $rental){
            //$idO = $CLD_CON->GetArrayField("id");
            $idO = $rental["id"];
            if (empty($in)) {
                array_push($in, $idO);
//                $in .= "$idO";
            } else {
                array_push($in, $idO);
//                $in .= ", " . $idO;
            }
        }
    }
    $events = $baseController->eventsModel->getAllFromEventsListIn($in, $tt, $id, $owner);
    $totalrows = count($events);
//    $CLD_CON->OpenRs("SELECT * FROM events WHERE $filters AND rental_id=$owner  ORDER BY CLD_date_lastPhoto DESC, start_date DESC");
    $select_nolimit = "SELECT * FROM events WHERE $filters AND rental_id=$owner ORDER BY CLD_date_lastPhoto DESC , start_date DESC";
   
    
}
else {  
    $events = $baseController->eventsModel->getAllFromEventsList($owner, $LIMIT);
    $select_nolimit ="SELECT * FROM events WHERE rental_id=$owner AND trashed IS NULL ORDER BY CLD_date_lastPhoto , start_date DESC";
    $totalrows = count($baseController->eventsModel->getAllFromEventsList($owner));
}

$eventsNum = count($events);
$html = <<<HTML
    <div class='inContent'>
        <div style='position:fixed;width:34px;height:103px;top:235px;left:175px;'>
        <div style='width:34px;height:34px;background-color:#6BBA70;' title='Active Event'></div>
        <div style='width:34px;height:34px;background-color:#FFCC33;' title=' has received pictures between last week and three months ago'></div>
        <div style='width:34px;height:34px;background-color:#A10326;' title='Didn&#8219;t recive a photo during the last 3 month'></div>
    </div>
HTML;

foreach ($events as $event){
    $event_numPhotos = $event["counter"];
    //$eventLastPhoto = $event["CLD_date_lastPhoto"];
    $event_id = $event["id"];
    $event_title = stripcslashes($event["title"]);
    $event_date = date("F d, Y", strtotime($event["start_date"]));
    $event_private = $event["private"];
    $event_lastPhoto = $event["CLD_date_lastPhoto"];
    $trashed = false;
    if($event["trashed"]){
        $trashed = true;
    }
    if($event["newServer"]){
        $trashed = true;
    }
    
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

    if($trashed){
        $html .= "<ul class='$class' onclick='setSection(\"events\" , 2 , $event_id, $trashed);'>";
    }
    else {
        $html .= "<ul class='$class' onclick='setSection(\"events\" , 2 , $event_id);'>";
    }
    
    $html .= <<<HTML
            <li style='width:20%' title='Event Name'>$event_title</li>
            <li style='width:20%' title='Start Date'>$event_date</li>
            <li style='width:20%'>PRIVATE: $event_private</li>
            <li style='width:20%'>$event_numPhotos photos</li>
            <li style='width:10%'> ID:$event_id</li>
            <li style='width:10%'>
HTML;
            if($trashed){
                $html .= "<span style='color:white'>*Expired*</span>";
            }
    $html .= <<<HTML
            </li>
        </ul>
HTML;

}

$html .= "</div>";

echo $html;

$s = "events";
$color="green";
include '../../pagescount.php';
