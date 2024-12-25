<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
$status = $_POST['status'];
if($CLD_CON->Execute("UPDATE CLD_Incidents SET status=$status WHERE id=$ID")){
    echo "OK";
}else{    
    echo "ERROR";
}
?>
