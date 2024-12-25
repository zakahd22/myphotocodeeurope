<?php

require_once "../../common/global.php";
include G_PATH.'common/general.php';

$code = $_REQUEST['code'];
$row = $_REQUEST['row'];

$baseController = new baseController;
$baseController->createModel('events');
$baseController->createModel('photos');

$photo = $baseController->photosModel->getPhoto($code);

if($photo){
    $eventId = $photo[event_id];
    $flag = $photo['flag'];
}

$event = $baseController->eventsModel->getEvent($event);

$photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$code'"));
$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$photo[event_id]"));

if ($event['owner_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");

if ($photo['flag'])
{
        mysql_query("UPDATE photos SET flag=0 WHERE code='$code'");
}
else
{
        mysql_query("UPDATE photos SET flag=1 WHERE code='$code'");		
}

header("Location:../../rental/events/photos/".$event['start_date'].$event['id']."#row".$row);
	
