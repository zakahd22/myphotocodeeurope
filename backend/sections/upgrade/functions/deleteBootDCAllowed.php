<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$id = $_POST['id'];



	
       
if($CLD_CON->Execute("DELETE FROM App_bootDCAllowed WHERE id=$id")){
    echo "OK";
}else{
    echo "ERROR1";
}

        
        


?>
