<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$USERTYPE = $_SESSION['USERTYPE'];
$owner = $_SESSION['USERID'];
switch ($USERTYPE){
     case 1: //SUPERUSER;
        include 'super2.php';
    break;
    case 2://MANAFUCTER
        include 'super2.php';
    break;
    case 3: //DISTRIBUTOR
        include 'distributor2.php';
    break;
    case 4: //OWNER
        include 'owner2.php';
    break;
    case 5://EVENT MANAGER
        
    break;
    case 6: //USER
        
    break;
}
?>
