<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["dades"], TRUE);
$ID = $json[0];
$estat = $json[1];
if(is_numeric($ID)){
    if($estat == 0){
        $CLD_CON->Execute("UPDATE CLD_Login SET banned = 1 WHERE id_user = $ID");
    }else{
        $CLD_CON->Execute("UPDATE CLD_Login SET banned = 0 WHERE id_user = $ID");
    }
}
