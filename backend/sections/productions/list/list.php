<?php
include "../../../sessio.php";
require_once G_PATH . 'common/conexio.php'; 
//$labels_apartat="photobooths";
//include '../../../labels.php';
if(isset($_POST['limit'])){
    $LIMIT = $_POST['limit'];
    $PAGE = $_POST['page'];
}
$USERTYPE = $_SESSION['USERTYPE'];
$USERID = $_SESSION['USERID'];
switch ($USERTYPE){
    case 1: //SUPERUSER;
        include 'manufacturer.php';
    break;
    case 2://MANAFUCTER
        include 'manufacturer.php';
    break;
    case 3: //DISTRIBUTOR
        
    break;
    case 4: //OWNER
       // include 'owner.php';
    break;
    case 5://EVENT MANAGER
        
    break;
    case 6: //USER
        
    break;
}

?>