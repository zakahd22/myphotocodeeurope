<?php
    if(!empty($_FILES['imgFile']['error']))
    {
        switch($_FILES['imgFile']['error'])
        {

            case '1':
                $error = 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                echo "ERROR: File is too large (maximum 2MB)";
                break;
            case '2':
                $error = 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form';
                echo "ERROR: File is too large (maximum 2MB)";
                break;
            case '3':
                $error = 'The uploaded file was only partially uploaded';
                echo "ERROR: File was only partially uploaded. Please try again.";
                break;
            case '4':
                $error = 'No file was uploaded.';
                echo "ERROR: No file was selected";
                break;
            case '6':
                $error = 'Missing a temporary folder';
                echo "ERROR: Server configuration error (missing temp folder)";
                break;
            case '7':
                $error = 'Failed to write file to disk';
                echo "ERROR: Failed to save file to server";
                break;
            case '8':
                $error = 'File upload stopped by extension';
                echo "ERROR: Invalid file type. Only JPG and GIF files are allowed";
                break;
            case '999':
                echo "ERROR: Upload failed";
                break;
            default:
                $error = 'No error code available';
                echo "ERROR: Upload failed";
                break;
        }
    }elseif(empty($_FILES['imgFile']['tmp_name']) || $_FILES['imgFile']['tmp_name'] == 'none')
    {
        $error = 'No file was uploaded..';
        echo "ERROR: No file was uploaded";
    }else{
        $info = pathinfo($_FILES['imgFile']['name']);
        $ext = $info['extension']; //

        // Add file type validation
        if($ext == 'jpg' || $ext == 'jpeg' || $ext == 'gif' || $ext == 'JPG' || $ext == 'JPEG' || $ext == 'GIF'){
            if(move_uploaded_file($_FILES['imgFile']['tmp_name'], "../../../images/ownerIMG/tmp/bn". $_POST['id'] . ".$ext")){
                $rnd = rand(0, 800000) / rand(1, 5000);
                echo "bn".$_POST['id']. ".$ext?version=$rnd";
            }else{
                echo "ERROR: Failed to save the uploaded file";
            }
        }else{
            echo "ERROR: Only JPG and GIF files are allowed";
        }
        
    }

?>
