<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

function get_file($pattern){
    $result = false;
    $files = glob($pattern . "*");
    if(count($files) == 1){
        $result = $files[0];
    }
    return $result;
}

$ID = $_POST['id'];
$cDate= $_POST['cDate'];
$folder = $cDate . $ID;

$filename = false; 
$path = "../../../printPhoto/tmp/header{$ID}";
$path2 = "../../../usbs/{$folder}/PhotoIdEvents/Wedding/Header/";
$filename = get_file($path);
$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

    if (file_exists($filename)){             
        mkdir($path2, 0777, true);       
        if(copy($filename , $path2 . "/1." . $file_extension)){          
            unlink($filename);
            echo "OK";
        } else {
//            echo(error_get_last()."\n");
            echo "Can not save this images, try again. If this problem persists contact us at main@myphotocode.com.";
        }
    } else {
        echo "Can not save this images, try again. If this problem persists contact us at main@myphotocode.com.";
    }

?>