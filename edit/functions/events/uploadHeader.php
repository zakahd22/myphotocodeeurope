<?php
 if(!empty($_FILES['headerFile']['error']))
    {
        switch($_FILES['headerFile']['error'])
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
    }elseif(empty($_FILES['headerFile']['tmp_name']) || $_FILES['headerFile']['tmp_name'] == 'none')
    {
        $error = 'No file was uploaded..';
        echo "ERROR";
    }else{
        
        $info = pathinfo($_FILES['headerFile']['name']);
        $ext = $info['extension']; //
        if($ext == 'jpg' || $ext == 'JPG' || $ext == 'jpeg' || $ext == 'JPEG'){
            require '../../../includes/classes/SimpleImage.php';
            $image = new SimpleImage();
            $image->load($_FILES['headerFile']['tmp_name']);
            //#resize
            $image->resize(800 , 600);
            $image->save("../../../printPhoto/tmp/header".$_POST['id'].".jpg"); 
             $rnd = rand(0, 800000) / rand(1, 5000);
            echo "header".$_POST['id'].".jpg?version=$rnd;";

        }else{
            echo "ERROR";
           
        }
        
    }

 echo $error;
?>
