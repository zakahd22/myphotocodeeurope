<?php
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php';

error_log( "TO_DELETE sections/phoyobooths/list/list.php 01" );

//$labels_apartat="photobooths";
//include '../../../labels.php';
if(isset($_GET['filters'])){
    $ownerId_F           = $_GET['ownerId'];
    $boothType_F         = $_GET['boothType'];
    $boothSerialNumber_F = $_GET['boothSN'];
    $boothStatus_F       = $_GET['boothStatus'];
    $dongleString_F      = $_GET['dongleString'];
    $idPB_F              = $_GET['idPb'];
}
if(isset($_POST['limit'])){
    $LIMIT = $_POST['limit'];
    $PAGE = $_POST['page'];
}


$USERTYPE = $_SESSION['USERTYPE'];
$USERID = $_SESSION['USERID'];
switch ($USERTYPE){
    case 1: //SUPERUSER;
        include 'super.php';
    break;
    case 2://MANAFUCTER
        include 'super.php';
    break;
    case 3: //DISTRIBUTOR
        include 'distributor.php';
    break;
    case 4: //OWNER
        include 'owner.php';
    break;
    case 5://EVENT MANAGER
        
    break;
    case 6: //USER CONSULTANT
        include 'super.php';
        
    break;
}

?>