<?php
include '../sessio.php';
require_once G_PATH . 'common/conexio.php';

$form = $_POST['form'];
$ID = $_POST['id'];
switch($form){
    case 1: //EDIT PHOTO OF PROFILE
        include './forms/events/addFrames.php';
        break;
    
        //ADD NEW DONGLE PAYXPRINT
    case 2:
        include './forms/payxprint/addDongle.php';
        break;
    
    case 3:
        include './forms/financingCode/addDongle.php';
        break;
    
    case 4:
        include './forms/instagram/addInstagramHashtagsUsers.php';
        break;
    
    case 5:
        include './forms/upgrade/addBootDCAllowed.php';
        break;
    
}
?>
