<?php

//ob_start();
//
//include '../functions/auditsManager.php';
//
//$json = ob_get_contents();
//ob_clean();
//
//$result = json_decode($json);
//
//echo $result->message;

include "../../../sessio.php";
include G_PATH . "common/conexio.php";

//$labels_apartat="photobooths";
//include '../../../labels.php';

if(isset($_POST['limit'])){
    $LIMIT = $_POST['limit']."";
    $PAGE = $_POST['page'];
}
$USERTYPE = $_SESSION['USERTYPE'];
$owner = $_SESSION['USERID'];

$html = "";

ob_start();

switch ($USERTYPE){
    case 1: //SUPERUSER;
        include 'super.php';
    break;
    case 2://MANAFUCTER
        include 'super.php';
    break;
    case 3: //DISTRIBUTOR
        include 'owner.php';
    break;
    case 4: //OWNER
        include 'owner.php';
    break;
    case 5://EVENT MANAGER
         include 'manager.php';
    break;
    case 6: //USER CONSULTANT
        include 'super.php';
    break;
}

$json = ob_get_contents();
ob_end_clean();

//$html .= "<div style='position:fixed;width:34px;height:103px;top:165px;left:175px;'>";
//$html .= "<div style='width:34px;height:34px;background-color:#6BBA70;' title='Active Event'></div>";
//$html .= "<div style='width:34px;height:34px;background-color:#FFCC33;' title=' has received pictures between last week and three months ago'></div>";
//$html .= "<div style='width:34px;height:34px;background-color:#A10326;' title='Didn&#8219;t recive a photo during the last 3 month'></div>";
//$html .= "</div>";

$result = json_decode($json);

echo $result->message;
?>