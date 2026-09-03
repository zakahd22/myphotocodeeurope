<?php
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
        include 'distributor.php';
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

$html = ob_get_contents();
ob_end_clean();
echo $html;
exit;
?>