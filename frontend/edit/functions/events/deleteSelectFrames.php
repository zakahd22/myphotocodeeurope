<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["data"], TRUE);
$id = $json[0];
utils::log($id, "logASD");
$files = glob("../../../printPhoto/e{$id}/PhotoIdUpload/Frames/*"); // get all file names
utils::log($files, "logASD");
foreach($files as $file){ // iterate files
//    utils::log($file, "logASD");
    if(is_file($file)){

        unlink($file); // delete file
                    

    }
}

//$CLD_CON->OpenRs("SELECT id_event, frame FROM event_frame WHERE id_event = $ID");
$CLD_CON->OpenRs("UPDATE event_frame SET frame=NULL WHERE id_event=$id");

echo "Frames are deleted";

