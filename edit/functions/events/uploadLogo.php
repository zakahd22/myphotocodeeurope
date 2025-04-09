<?php
 if(!empty($_FILES['imgFile']['error'])){
    switch($_FILES['imgFile']['error']){
        case '1':
            $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            echo "ERROR";
            break;
        case '2':
            $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            echo "ERROR";
            break;
        case '3':
            $error = 'The uploaded file was only partially uploaded';
            echo "ERROR";
            break;
        case '4':
            $error = 'No ile was uploaded.';
            echo "ERROR";
            break;

        case '6':
            $error = 'Missing a temporary folder';
            echo "ERROR";
            break;
        case '7':
            $error = 'Failed to write file to disk';
            echo "ERROR";
            break;
        case '8':
            $error = 'File upload stopped by extension';
            echo "ERROR";
            break;
        case '999':
            echo "ERROR";
            break;
        default:
            $error = 'No error code avaiable';
            echo "ERROR";
           break;
    }
}
elseif(empty($_FILES['imgFile']['tmp_name']) || $_FILES['imgFile']['tmp_name'] == 'none'){
    $error = 'No file was uploaded..';
    echo "ERROR";
}
else{        
    $info = pathinfo($_FILES['imgFile']['name']);
    $ext = $info['extension']; //
    if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
        $fileExist = "../../../printPhoto/tmp/logo".$_POST['id'];
        if(file_exists($fileExist . ".jpg") || file_exists($fileExist . ".jpeg") || file_exists($fileExist . ".JPG") || file_exists($fileExist . ".JPEG")){
            unlink($fileExist . ".jpg");
            unlink($fileExist . ".jpeg");
            unlink($fileExist . ".JPG");
            unlink($fileExist . ".JPEG");
        }
        
        $uploadedfile = $_FILES['imgFile']['tmp_name'];
        list($width, $height) = getimagesize($uploadedfile);
        $src = @imagecreatefromjpeg($uploadedfile);
        $tmp = imagecreatetruecolor(800, 600);
        imagecopyresampled($tmp, $src, 0, 0, 0, 0, 800, 600, $width, $height);
        $filename = "../../../printPhoto/tmp/logo". $_POST['id'] . ".$ext";
        imagejpeg($tmp, $filename, 50);
        imagedestroy($src);
        imagedestroy($tmp);      
        echo "logo".$_POST['id']. ".$ext";      
    }
    else{
        echo "ERROR";
    }        
}


?>
