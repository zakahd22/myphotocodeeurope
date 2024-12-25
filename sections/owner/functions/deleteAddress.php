<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
if($CLD_CON->Execute("DELETE FROM App_ownerAddress WHERE id=$ID")){
    echo "OK";
}else{
    echo "ERROR";
}
?>
