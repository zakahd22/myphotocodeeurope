<?php
 if(!empty($_FILES['musicFile']['error'])){
    switch($_FILES['musicFile']['error']){

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
elseif(empty($_FILES['musicFile']['tmp_name']) || $_FILES['musicFile']['tmp_name'] == 'none'){
    $error = 'No file was uploaded..';
    echo "ERROR";
}
else{

    $info = pathinfo($_FILES['musicFile']['name']);
    $ext = $info['extension']; //
    if($ext == 'mp3' || $ext == 'MP3'){
        if(copy($_FILES['musicFile']['tmp_name'] , "../../../printPhoto/tmp/music".$_POST['id'].".mp3")){
            echo "music".$_POST['id'].".mp3";
        }
    }
    else{
        echo "ERROR";

    }
}

 echo $error;
?>
