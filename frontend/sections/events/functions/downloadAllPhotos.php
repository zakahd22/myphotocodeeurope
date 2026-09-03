<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . "common/Classes/baseController.php";

function defineError($title="Error has ocurred", $text="Try again later."){
    $content = "<div class='popup-text'>{$text}</div>";
    $array = json_encode(array('response'=>0, 'title'=>$title, 'content'=>$content));
    return $array;
}

$result = defineError();
$baseController = new baseController();
$baseController->createModel('events');
$baseController->createModel('photos');

$ID = $_POST['id'];

$event = $baseController->eventsModel->getEvent($ID);
if ($event) {
    $eventDate = $event[0]["start_date"];
    $filename = $ID . "_photosAndVideos.zip";
}

$countPhotos = $baseController->photosModel->countPhotosInEvent($ID);
if ($countPhotos) {
    $photos = $countPhotos[0]["counter"];
}

if($photos > 1500){
    $result = defineError(
        "Event contains too many photos",
        "This event has too many photos. Please contact myphotocode@dc-image.com for technical support."
    );
}
else{
    
    $folder = $eventDate . $ID;

    if (file_exists(G_PATH . "events/$folder/" . $filename)) {
        unlink(G_PATH . "events/$folder/" . $filename);
    }
    
    $downloadFile = "events/$folder/$filename";
    $filename = G_PATH . "events/$folder/" . $filename;
    
    if (file_exists($filename)) {
        unlink($filename);
    }
    
    
    try {
        $zip = new ZipArchive();
        $zipOpen = $zip->open($filename, ZIPARCHIVE::CREATE);
        if ($zipOpen === true) {
            $directorio = opendir(G_PATH . "events/$folder");
            while ($archivo = readdir($directorio)) {
                if (!is_dir(G_PATH . "events/$folder/" . $archivo)) {
                    if (strpos($archivo, "background") === false) {
                        if (strpos($archivo, "banner") === false) {
                            if(strpos($archivo, ".txt") === false) {
                                if(!$zip->addFile(G_PATH . "events/$folder/$archivo", $archivo)){
                                    $result = defineError();
                                }
                            }
                        }
                    }
                }
            }
            $zip->close();
            $title = 1;
            $page =  G_PAGE . $downloadFile;
            $result = json_encode(array('response'=>$title, 'content'=>$page));
        }
        else {
            //https://php.net/manual/en/zip.constants.php#ziparchive.constants.create
            utils::log("Error code = " . $zipOpen, "logDownloadAll");
            utils::log("filename = " . $filename, "logDownloadAll");
            $result = defineError(
                "Error has ocurred",
                "If this error remains, please contact myphotocode@dc-image.com for technical support."
            );
        }
    }
    catch (Exception $e){
        utils::log($e, "logDownloadAllPhotos");
        $result = defineError();
        echo $result;
    }
}
echo $result;