<?php
include "../../../sessio.php";
include G_PATH . "common/conexio.php";
include G_PATH . "common/Classes/compressorUtility.php";

function descompressZipEvent($zipFile, $folderEvent){
    $arrayPhotos = array();
    $compressorUtility = new compressorUtility;
    $arrayZip = $compressorUtility->iterateCompressedEventZip($zipFile);
    
    foreach($arrayZip as $entry){
        if($entry['size'] != 0){
            $codephoto = extractSinglePhoto($zipFile, $folderEvent, $entry['name']);
            if($codephoto){
                $arrayPhotos = saveInArray($arrayPhotos, $codephoto);
            }
        }
        else {
            existOrCreateDirectory($folderEvent.$entry['name']);
        }
    }
    return $arrayPhotos;
}

function existOrCreateDirectory($dir){
    $rtn = false;
    if (!file_exists($dir)) {
        if(mkdir($dir, 775, true)){
            chmod("{$dir}", 0775);
//            chown("{$dir}", "www-data");
            $rtn = true;
        }
    }
    else {
        $rtn = true;
    }
    return $rtn;
}

function saveInArray($arrayPhotos, $codephoto){
    if($codephoto != "banner" && $codephoto != "compressed"){
        if(in_array($codephoto, $arrayPhotos)){
//            utils::log("$codephoto -> Exist in Array", 'logDescompress');
        }
        else {
//            utils::log("$codephoto -> Array Push", 'logDescompress');
            array_push($arrayPhotos, $codephoto);
        }
    }
    
    return $arrayPhotos;
}

function extractSinglePhoto($zipFile, $folderEvent, $fileToExtract){
    $name = false;
    $extension = explode(".", $fileToExtract);
    if($extension[1] != "zip"){
        $zip = new ZipArchive();
        if ($zip->open($zipFile) === TRUE) {
            if($file = $zip->getFromName($fileToExtract)) {
                if(file_put_contents("{$folderEvent}/{$fileToExtract}", $file)){
    //                chmod("{$folderEvent}/{$fileToExtract}", 0755);
    //                chown("{$folderEvent}/{$fileToExtract}", "www-data");
                    // Get codephoto from filename
                    $name = pathinfo("{$folderEvent}/{$fileToExtract}");
                    $name = $name['filename'];
                    if(strpos($name, '-') !== false){
                        $name = explode('-', $name);
                        $name = $name[0];
                    }
                }
            }
        }
    }
    return $name;
}

function updatePDelete($a = false, $idEvent = false, $arrayPhotos = false){
    $baseController = new baseController;
    $baseController->createModel('events');
    $baseController->createModel('photos');
    if($a){
        $array = array('CLD_pDelete' => 1);
        $arrayUpdateIn = array();
        if(count($arrayPhotos) > 100){
            foreach($arrayPhotos as $codePhoto){
                if(count($arrayUpdateIn) == 100){
                    $baseController->photosModel->updatePhotoArrayIn($arrayUpdateIn, $array);
//                    utils::log($arrayUpdateIn, 'logDescompress');
                    $arrayUpdateIn = array();
                }
                array_push($arrayUpdateIn, $codePhoto);
            }
        }
        else {
            $arrayUpdateIn = $arrayPhotos;
        }
//        utils::log($arrayUpdateIn, 'logDescompress');    
        $baseController->photosModel->updatePhotoArrayIn($arrayUpdateIn, $array);
        if($idEvent){
//            $array = array('compressed' => NULL);
//            $baseController->eventsModel->updateEvent($idEvent, $array);
        }
    }
    else {
        if($idEvent){
            $array = array('compressed' => NULL);
            $baseController->eventsModel->updateEvent($idEvent, $array);
        }
    }
    
    
    
}

function contaDescomprimides($idEvent){
    $baseController = new baseController;    
    $baseController->createModel('photos');
    $photos_pDelete = $baseController->photosModel->getCLD_pDeleteEvents($idEvent); //descomprimides        
    return $photos_pDelete[0]['counter'];
    
    
}

function updateDescompressed($idEvent){
    $baseController = new baseController;
    $baseController->createModel('events');
    $baseController->createModel('photos');
    $array = array('compressed' => NULL);
    $baseController->eventsModel->updateEvent($idEvent, $array);
    $array = array('CLD_pDelete' => 1);
    $upd = $baseController->photosModel->updatePhotoByEvent($idEvent, $array, false);    
    
}

 



$idEvent = $_POST['eventid'];
$dateEvent = $_POST['eventDate'];
$zipFile = G_PATH . "events/compressed_events/" . $idEvent . "_compressed.zip";
$folderEvent = G_PATH . "events/" . $dateEvent . $idEvent . "/";
//utils::log("Descompress - Trace 01", "logDescompress");
//utils::log("Zipfile Location: $zipFile", "logDescompress");
if (file_exists($zipFile)) {
    if(existOrCreateDirectory($folderEvent)){
        $arrayPhotos = descompressZipEvent($zipFile, $folderEvent);
        $totalJPGsInFolder = count(glob($folderEvent.'{*.jpg}',GLOB_BRACE));
 
        if (count($arrayPhotos) > 0) {
//            utils::log($arrayPhotos, 'logDescompress');
            updatePDelete(true, $idEvent, $arrayPhotos);
            
            


            $f = fopen($folderEvent . "compressed.txt", "a+");
            fwrite($f, date("Y-m-d H:i:s  - "). " -- Uncompressed");
            fclose($f);
        } 
        else {
            echo 'Failed to extract the files, please try again.';
        }
        //20220125
        //Si un event es reactiva però consta com a comprimit els arxius nous fins a 2022 per defecte entren amb CLD_pDelete=0 (comprimit) però realment no està comprimit
        //Ja ho hem canviat a la BD i per defecte els arxius tenen CLD_pDelete=1 a cada nou insert
        //Si tenim tants arxius descomprimits o més que CLD_pDelete=1 a taula photos amb id de l'event marquem l'event com descomprimit, sino, entra en bucle ;) //patch
        $photosDescomprimides = contaDescomprimides($idEvent); //descomprimides
        
        if($totalJPGsInFolder>=$photosDescomprimides){
            updateDescompressed($idEvent);
        }
    }
    else {
        echo "Can not create this folder, {$folderEvent}";
    }
} 
else {
    updatePDelete(false);
    echo "This event don't have any Photo compressed: ".$zipFile;
}