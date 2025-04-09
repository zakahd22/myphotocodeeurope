<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$screens = $_POST['screens'];
$cDate = $_POST['cDate'];


$total_imagenes = count(glob("../../../usbs/{$cDate}{$ID}/PhotoIdEvents/CustomShots/{*.jpg}", GLOB_BRACE));

if ($total_imagenes == 0) {
    $num = 1;
} else {
    $num = $total_imagenes  + 1;
}
$i = 1;
$x1 = true;

$return = false;

while ($i < ($screens + 1) && $x1) {

    $name = $num;
    $pathFile = "../../../usbs/{$cDate}{$ID}/PhotoIdEvents/CustomShots/";
    if(!mkdir($pathFile, 0777, true)) {
        //Fallo al crear las carpetas
    }
    if(file_exists("../../../printPhoto/tmp/shot" . $ID . "{$i}.jpg")){
        if(copy("../../../printPhoto/tmp/shot" . $ID . "{$i}.jpg", "../../../usbs/{$cDate}{$ID}/PhotoIdEvents/CustomShots/" . $name. ".jpg")){
            unlink("../../../printPhoto/tmp/shot" . $ID . "{$i}.jpg");
            $num ++;
            $return = true;
        }     
    }
    $i++;

}

if($return){
    echo "OK";
}
else {
    echo "Can not save this images, try again. If this problem persists contact us at main@myphotocode.com.";
}

?>