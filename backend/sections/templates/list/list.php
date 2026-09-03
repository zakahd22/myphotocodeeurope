<?php
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php'; 
//$labels_apartat="photobooths";
//include '../../../labels.php';
//utils::log("Trace 0", "logFerran"); 

if(isset($_POST['limit'])){
    $LIMIT = $_POST['limit'];
    $PAGE = $_POST['page'];
}
$USERTYPE = $_SESSION['USERTYPE'];
$USERID = $_SESSION['USERID'];

//utils::log($USERTYPE, "logFerran");

switch ($USERTYPE) {
    case 1: //SUPERUSER;
        include 'owner.php';
        break;
    case 2://MANAFUCTER
        include 'owner.php';
        break;
    case 3: //DISTRIBUTOR
        include 'owner.php';
        break;
    case 4: //OWNER
        include 'owner.php';
        break;
    case 5://EVENT MANAGER
        include 'owner.php';
        break;
    case 6: //USER
        include 'owner.php';
        break;
    case 7:
        include 'owner.php';
        break;
}