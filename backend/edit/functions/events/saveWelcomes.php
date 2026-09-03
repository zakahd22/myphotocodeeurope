<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

//Si la carpeta del Event no esta creada per algun motiu
//dona errors al copiar les fotos.

$ID = $_POST['id'];
$screens = $_POST['screens'];
$cDate = $_POST['cDate'];
$chars = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p');

$total_imagenes = count(glob("../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Welcome/Random/{*.jpg}", GLOB_BRACE));
$num = $total_imagenes == 0? 1 : (($total_imagenes / $screens) + 1);

$i = 1;
$x1 = true;

while ($i < ($screens + 1)) {
    $c = $chars[$i];
    $name = ($screens == 1? $num : ($num . $c));
    $src = "../../../printPhoto/tmp/wlk{$ID}{$i}.jpg";
    
    $pathFile = "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Welcome/Random/";
    if(!mkdir($pathFile, 0777, true)) {
        //Fallo al crear las carpetas
    }
    
    $dstRandom = $pathFile . $name . ".jpg";
    if(!copy($src, $dstRandom)){
        $x1 = false;
    }
    
    $pathFile = "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Welcome/Custom/";
    if(!mkdir($pathFile, 0777, true)) {
        //Fallo al crear las carpetas
    }
    $dstCustom = $pathFile . $name . ".jpg";
    if(!copy($src, $dstCustom)){
        $x1 = false;        
    }
    $i++;
}

if ($x1) {
   $i = 1;
    while ($i < ($screens + 1)){
        $src = "../../../printPhoto/tmp/wlk{$ID}{$i}.jpg";
        if(file_exists($src)){
            unlink($src);
        }
        $i++;
    }
}
else {
    $i = 1;
    while ($i < ($screens + 1)) {
        $c = $chars[$i];
        if($screens == 1){
            $name = $num;
        }
        else{
            $name=  $num . $c;
        }
        
        $dstRandom = "../../../usbs/{$cDate}{$ID}/PhotoIdUpload/Welcome/Random/" . $name . ".jpg";
        if(file_exists($dstRandom)) {
            unlink($dstRandom);
        }
        $i++;
    }
}

if($x1){ echo "OK";}
?>