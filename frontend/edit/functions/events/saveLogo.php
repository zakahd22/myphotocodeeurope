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

$filename = false; 
$path = "../../../printPhoto/tmp/logo{$ID}";
$path2 = "../../../printPhoto/e{$ID}/";

$filename = get_file($path);
$file_extension = pathinfo($filename, PATHINFO_EXTENSION);

if($file_extension == "jpg" || $file_extension == "JPG" ||$file_extension == "jpeg" ||$file_extension == "JPEG"){
    if(file_exists($filename)){
        if(!file_exists($path2 . '/PhotoIdUpload/')) {
            mkdir($path2 , 0777, true);
            mkdir($path2 . 'PhotoIdUpload/', 0777, true);
        }

        if(copy("./" . $filename , "./".$path2."PhotoIdUpload/Logo.jpg")){          
            unlink($filename);
            echo "OK";
        }
        else{
            echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
            // Mail
        }
    }
    else{
        echo "Can not save this image, try again. If this problem persists contact us at main@myphotocode.com.";
        // Mail
    }
}
else{
    echo "Can not save this image, the file extension is not compatible (it must be JPG or JPEG).";
    // Mail
}
