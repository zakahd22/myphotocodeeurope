<?php
if(!empty($_FILES['profileIMG']['error'])){
    switch($_FILES['profileIMG']['error']){
        case '1':
            $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
            echo "$error";
            break;
        case '2':
            $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
            echo "$error";
            break;
        case '3':
            $error = 'The uploaded file was only partially uploaded';
            echo "$error";
            break;
        case '4':
            $error = 'No file was uploaded.';
            echo "$error";
            break;

        case '6':
            $error = 'Missing a temporary folder';
            echo "$error";
            break;
        case '7':
            $error = 'Failed to write file to disk';
            echo "$error";
            break;
        case '8':
            $error = 'File upload stopped by extension';
            echo "$error";
            break;
        case '999':
            echo "999";
            break;
        default:
            $error = 'No error code avaiable';
            echo "$error";
            break;
    }
}
elseif(empty($_FILES['profileIMG']['tmp_name']) || $_FILES['profileIMG']['tmp_name'] == 'none'){
    $error = 'No file was uploaded..';
    echo "$error";
}
else{
    $info = pathinfo($_FILES['profileIMG']['name']);
    $ext = $info['extension']; //

    if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'JPG' || $ext == 'JPEG'){
        if(is_uploaded_file($_FILES['profileIMG']['tmp_name'])){
            if(move_uploaded_file($_FILES['profileIMG']['tmp_name'] , "../../../images/ownerIMG/tmp/". $_POST['id'] . ".$ext")){
                $rnd = rand(0, 800000) / rand(1, 5000);
                echo $_POST['id']. ".$ext?version=$rnd;";
            }
            else{
                if(copy($_FILES['profileIMG']['tmp_name'] , "../../../images/ownerIMG/tmp/". $_POST['id'] . ".$ext")){
                    $rnd = rand(0, 800000) / rand(1, 5000);
                    echo $_POST['id']. ".$ext?version=$rnd;";
                }
                else{
                    echo "ERROR 01";
                }
            }
        }
        else{
            echo "ERROR 02";
        }
    }
    else{
        echo "ERROR 03";
    }
}

?>
