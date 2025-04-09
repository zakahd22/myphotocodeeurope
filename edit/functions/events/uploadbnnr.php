<?php
    if(!empty($_FILES['imgFile']['error']))
    {
        switch($_FILES['imgFile']['error'])
        {

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
    }elseif(empty($_FILES['imgFile']['tmp_name']) || $_FILES['imgFile']['tmp_name'] == 'none')
    {
        $error = 'No file was uploaded..';
        echo "ERROR";
    }else{
        $info = pathinfo($_FILES['imgFile']['name']);
        $ext = $info['extension']; //

        if(move_uploaded_file($_FILES['imgFile']['tmp_name'] ,  "../../../images/ownerIMG/tmp/bn". $_POST['id'] . ".{$ext}")){
             $rnd = rand(0, 800000) / rand(1, 5000);
            echo "bn".$_POST['id']. ".{$ext}?version={$rnd};";
        }else{
            echo "ERROR";
        }
        
    }

?>
