<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
//$ID = $_POST['id'];
$cl = $_POST['collage'];

if (!empty($_FILES['fileCollages']['error'])) {
    switch ($_FILES['fileCollages']['error']) {

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
} elseif (empty($_FILES['fileCollages']['tmp_name']) || $_FILES['fileCollages']['tmp_name'] == 'none') {
    $error = 'No file was uploaded..';
    echo "ERROR";
} else {
            require '../../../includes/classes/SimpleImage.php';
            $image = new SimpleImage();
            $image->load($_FILES['fileCollages']['tmp_name']);
            //#resize
            $image->resize(800 , 600);
            $image->save("../../../printPhoto/tmp/cl$cl.png" , IMAGETYPE_PNG);
            $rnd = rand(0, 800000) / rand(1, 5000);
            echo "cl$cl.png?version=$rnd;";
}

echo "$error";
?>
