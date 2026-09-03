<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . 'common/global.php';

$json = json_decode($_POST["data"], TRUE);
$orphans = $json;


$deleted = 0;
foreach ($orphans as $orphan){
    if(unlink($orphan)){
        $deleted++;
    }
}
if ($deleted == count($orphans)){
    return true;
}else{
    return false;
}