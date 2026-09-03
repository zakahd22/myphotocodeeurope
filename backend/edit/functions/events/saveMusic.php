<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$cDate = $_POST['cDate'];
$folder = $cDate.$ID;
if (file_exists("../../../printPhoto/tmp/music$ID.mp3")){
    if(!file_exists("../../../usbs/$folder/PhotoIdUpload/")){
        mkdir("../../../usbs/$folder/PhotoIdUpload/", 0777, true);
    }
    if (copy("./../../../printPhoto/tmp/music$ID.mp3" , "../../../usbs/$folder/PhotoIdUpload/BGmusic.mp3")){          
        unlink("./../../../printPhoto/tmp/music$ID.mp3");
        echo "OK";
    } 
    else {
        echo "ERROR11";
    }
} 
else {
    echo "ERROR12";
}

?>