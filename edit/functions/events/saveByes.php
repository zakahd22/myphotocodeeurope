<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$screens = $_POST['screens'];
$cDate = $_POST['cDate'];
$chars = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p');
$pathFile = "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Custom/";
if(!mkdir($pathFile, 0777, true)) {
    //Fallo al crear las carpetas
}
$pathFile = "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/";
if(!mkdir($pathFile, 0777, true)) {
    //Fallo al crear las carpetas
}

$total_imagenes = count(glob("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/{*.jpg}", GLOB_BRACE));

if ($total_imagenes == 0) {
    $num = 1;
} else {
    $num = ($total_imagenes / $screens) + 1;
}
$i = 1;
$x1 = true;



while ($i < ($screens + 1) && $x1) {
    $c = $chars[$i];
    if ($screens == 1) {
        $name = $num;
    } else {
        $name = $num . $c;
    }
    if (copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.jpg", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpg")) {
    }
    else if(copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.JPG", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPG")){
    }
    else if(copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.jpeg", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpeg")){
    }
    else if(copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.JPEG", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPEG")){
    }
    else {
        $x1 = false;
    }
    
    if (copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.jpg", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Custom/" . $name . ".jpg")) {
    }
    else if (copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.jpeg", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Custom/" . $name . ".jpeg")) {
    }
    else if (copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.JPG", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Custom/" . $name . ".JPG")) {
    }
    else if (copy("../../../printPhoto/tmp/bye" . $ID . "{$i}.JPEG", "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Custom/" . $name . ".JPEG")) {
    }
    else {
        $x1 = false;
    }
    $i++;
}

if ($x1) {
    $i = 1;
    while ($i < ($screens + 1)) {
        unlink("./../../../printPhoto/tmp/bye{$ID}{$i}.jpg");
        $i++;
    }
} else {
    $i = 1;
    while ($i < ($screens + 1)) {
        $c = $chars[$i];
        if ($screens == 1) {
            $name = $num;
        } else {
            $name = $num . $c;
        }
        if (file_exists("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpg")) {
            unlink("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpg");
        }
        else if(file_exists("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPG")){
            unlink("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPG");
        }
        else if(file_exists("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpeg")){
            unlink("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".jpeg");
        }
        else if(file_exists("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPEG")){
            unlink("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Bye/Random/" . $name . ".JPEG");
        }
        $i++;
    }
}
if ($x1) {
    echo "OK";
}
else {
    echo "Can not save this images, try again. If this problem persists contact us at main@myphotocode.com.";
}
?>