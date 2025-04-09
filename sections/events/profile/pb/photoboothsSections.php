<?php
include '../../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$apartat = $_POST['apartat'];
$booth = $_POST['booth'];
$folder = $_POST['folder'];
$bbTypeID = $_POST['bb'];
$idUSB = substr($folder , 8);

$CLD_CON2->OpenRs("SELECT b.* FROM booth_types b WHERE b.char='$booth'");
$CLD_CON2->FetchArray();
$welcome_width = $CLD_CON2->GetArrayField('welcome_w');
$welcome_height = $CLD_CON2->GetArrayField('welcome_h');
$banner_width = $CLD_CON2->GetArrayField('banner_w');
$banner_height = $CLD_CON2->GetArrayField('banner_h');
$custom_width = $CLD_CON2->GetArrayField('custom_w');
$custom_height = $CLD_CON2->GetArrayField('custom_h');
$screens = $CLD_CON2->GetArrayField('screens');
$chars = array('', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l');
$CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $idUSB");
$CLD_CON->FetchArray();
$bgmusic = $CLD_CON->GetArrayField('bgmusic');
$welcome_type = $CLD_CON->GetArrayField('welcome_type');
$bye_type = $CLD_CON->GetArrayField('bye_type');
$event_id = $CLD_CON->GetArrayField("event_id");

switch($apartat){
    case 1: 

       include 'welcomes.php';       
        
    break;
    case 2: 

       include 'byes.php';       
        
    break;
    case 3: 

       include 'customs.php';       
        
    break;
    case 4: 

       include 'bgMusic.php';       
        
    break;
    case 5: 

       include 'header.php';       
        
    break;

}

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */



function listarArchivos($path , $s , $U , $a , $u , $e , $f , $b) {
    // Abrimos la carpeta que nos pasan como parámetro
    $c=1;
    if($s ==1){
        $width  = '50%;margin-left:25%;margin-right:25%;margin-top:2%;';
    }else{
    $width = 100/$s -2;
    }
    $x = 1;
    $dir = opendir("../../../../" . $path);
    // Leo todos los ficheros de la carpeta
    $o = 0;
    $dir = scandir("../../../../" . $path);
    foreach ($dir as $file) {
        // Tratamos los elementos . y .. que tienen todas las carpetas
        if ($file != "." && $file != "..") {
            utils::log($file, "logevents");
            if($o == 0){
                echo "<div class='framesContent'>";
            }
            // Si es una carpeta
            $rnd = rand(0, 800000) / rand(1, 5000);
            if (!is_dir($path . $file)) {
                echo "<img src='". $path . $file . "?version=$rnd;' class='frames' style='width:$width%;' >";
                if ($x % $s == 0) {
                    echo "<p style='color:red;text-align:center;cursor:pointer;padding-bottom:10px;'>";
                    echo "<input type='button' class='miniTrash' style='' onclick='deleteIMGusb($a , $e , $u , $c , \"it\" , \"$f\" , \"$b\" , $s);'>";
                    echo "</p>";
                      $c++;
                }
                $x++;
            }
            if($o == 3){
                echo "</div>";
                $o = 0;
            }
            else{
                $o ++;                
            }
        }
    }
    
}
function listarArchivos2($path , $s , $U , $a , $u , $e , $f , $b) {
    // Abrimos la carpeta que nos pasan como parámetro
    $width = 100/$s -2;
    $x = 1;
    $c=1;
    $dir = opendir("../../../../" . $path);
    // Leo todos los ficheros de la carpeta
    while ($elemento = readdir($dir)) {
        $rnd = rand(0, 800000) / rand(1, 5000);
        // Tratamos los elementos . y .. que tienen todas las carpetas
        if ($elemento != "." && $elemento != "..") {
            // Si es una carpeta
            if (!is_dir($path . $elemento)) {
                echo "<div style='display:inline;float:left;width:$width%;margin:1%;' class='frame'>";
                echo "<img src='". $path . $elemento . "?version=$rnd;' style='width:100%;'>";
                  echo "<p style='color:red;text-align:center;cursor:pointer;padding-bottom:10px;padding-right:5;'>";
                    echo "<input type='button' class='miniTrash' style='right: -47%;' onclick='deleteIMGusb($a , $e , $u , $c , \"it\" , \"$f\" , \"$b\" , $s);'>";
                    echo "</p>";
                echo "</div>";
                $x++;
                $c++;
            }
        }
    }
}
?>
