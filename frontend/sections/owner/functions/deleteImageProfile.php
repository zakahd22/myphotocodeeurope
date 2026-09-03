<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
$id=$_POST['id'];

if(unlink("../../../images/ownerIMG/$id.jpg")){
    echo "OK";
}
?>
