<?php

//Funció escriu al log
function writeOnLog($log, $text) {
    $d = date("Y-m-d H:i:s");
    $f = fopen($log, "a+");
    fwrite($f, $d . " - " . $text . "\n");
    fclose($f);
}

//Funcio que Busca el fitxer save-in-usb dins de un fitxer
function searchAndDeleteUSBZip($dirname) {
    $ret=0;
    if (is_dir($dirname)){
        $dir_handle = opendir($dirname);
        while ($file = readdir($dir_handle)) {
            $ret=1;
            //writeOnLog("logCronUSB.txt", "file = $file");                    
            if ($file == "save-in-usb.zip") {
                $del = unlink($dirname . "/" . $file);
                if($del){
                    $ret=2;
                    echo ">>> Deleting".$dirname."/".$file."/save-in-usb.zip deleted\n";
                    //writeOnLog("logCronUSB.txt", "unlink($dirname" . "/" . "$file)");                    
                }
                break;
            }
        }
        closedir($dir_handle);
    }
    return $ret;
}

//Funcio que Busca el fitxer id_photosAndVideos dins de una carpeta d'Events
function searchAndDeleteEventZip($dirname, $ID) {
    $deleted = false;
    $res = 0;
    if (is_dir($dirname)){
        $dir_handle = opendir($dirname);
        while ($file = readdir($dir_handle)){
            $res=2;
            if($file == "$ID" . "_photosAndVideos.zip"){
                $deleted=unlink($dirname . "/" . $file);
                if($deleted){
                    $res=1;
                    //writeOnLog("logCronEvents.txt", "unlink($dirname" . "/" . "$file)");
                    echo ">>>  deleting {$dirname}/{$file}\n";
                }
                break;
            }
        }
        closedir($dir_handle);
    }
    
    return $res;
}

function notBanner($file){
    return (strpos($file, "banner") == false);
}

function notBackground($file){
    return (strpos($file, "background") == false);
}

function notHiddenFile($file){
    return ($file != ".") && ($file != "..");
}

function notSubDirectory($file, $dir){
    return !is_dir($dir . "/" . $file);
}

function saveZipFiles($event_dir, $zip, $max, $subdir=""){
    /*
    Saves the event images to the zip file.
    Returns true if success, only compress 400 photos each time, because if more (600)
    failure, the remaining files would stay at the event folder
    */
    $path = $event_dir."/";
    $event = opendir($event_dir);

    $compressed_subfolderFiles = 0;
    $count = 0;
    $theorically_added = 0;
    $notempty = 0;
    $next_file = readdir($event);
    
    while($next_file){
        if(notHiddenFile($next_file)){
            //counter to know if there are any file or directory in the folder
            $notempty++;
            if(notBackground($next_file) && notBanner($next_file)){
                if($theorically_added < $max){
                    if(is_dir($path.$next_file)){
                        $added = $zip->addEmptyDir($next_file);
                        //don't check if exists the subdir in the zip because if is a second execution, it could exist yet 
                        $compressed_subfolderFiles=0;
                        //echo "+$next_file\n";
                        $compressed_subfolderFiles = saveZipFiles($path.$next_file, $zip, ($max-$theorically_added), $subdir."/".$next_file);
                        if($compressed_subfolderFiles == -2){
                            return $compressed_subfolderFiles;
                        }
                        else{
                            //echo ">>> added ".$compressed_subfolderFiles." files\n";
                            $theorically_added += $compressed_subfolderFiles;
                            $count += $compressed_subfolderFiles;
                        }
                    }
                    else{
                        /*
                        if($subdir!=""){
                            echo "\t";
                        }
                        echo "-$next_file\n";
                        */
                        $count++;
                        $added = $zip->addFile($path.$next_file, $subdir."/".$next_file);
                        if($added){
                            //echo ">>> #{$theorically_added} - added \n";
                            $theorically_added++;
                        }
                    }
                }
                else{
                    break;
                }
            }
        }
        $next_file = readdir($event);
    }
    
    closedir($event);
    if($count == $theorically_added){
        if($notempty==0){
            //empty folder
            return 0;
        }
        //compressed {$theorically_added} files
        return $theorically_added;
    }
    else{
        return -2;
    }
}

//comprimirEvents(anterior)
function compressEvent($event, $dir, $id, $ziplimit=400){
    $compressed = -3;
    if(is_dir($event)){
        $filename = "{$dir}compressed_events/{$id}_compressed.zip";
        //echo "compressing at {$filename} \n";
        $zip = new ZipArchive();
        $zip_open = $zip->open($filename, ZIPARCHIVE::CREATE);
        if($zip_open){
            $compressed = saveZipFiles($event, $zip, $ziplimit);
            $zip->close();
        }
    }
    else{
        $compressed = -1;
    }
    return $compressed;
}

function deleteZip($dir, $id){
     /*
    Deletes the zip file. Returns true if success
    */
    $deleted = false;
    $filename = "{$dir}compressed_events/{$id}_compressed.zip";
    echo "deleting at {$filename} \n";
    if(file_exists($zipname)){
        $deleted = unlink($zipname);
    }
    return $deleted;
}

function notZipFile($dir, $id){
    $zipname = $dir."compressed_events/".$id."_compressed.zip";
    echo "consulting at {$zipname} \n";
    return !file_exists($zipname);
}

/* Delete images */
function deleteIMG($dir, $limit, $subdir=null){
    if(is_dir($dir)){
        $image = opendir($dir);
        $count = 0;
        $notempty=0;
        $theorically_deleted = 0;
        
        $next_file = readdir($image);

        while($next_file){
            if(notHiddenFile($next_file)){
                //counter to know if there are any file or directory in the folder
                $notempty++;
                if(notBackground($next_file) && notBanner($next_file)){
                    if($theorically_deleted < $limit){
                        if (is_dir($dir."/".$next_file)){
                            //echo "+$next_file\n";
                            $deleted_subfolderFiles = 0;
                            $deleted_subfolderFiles = deleteIMG($dir."/".$next_file, ($limit-$theorically_deleted),1);
                            $theorically_deleted += $deleted_subfolderFiles;
                            //echo ">>> deleted ".$deleted_subfolderFiles." files\n";
                            if($theorically_deleted < $limit){
                                $deleted=rmdir($dir."/".$next_file);
                                if($deleted){
                                    //echo "folder ".$dir."/".$next_file." deleted";
                                }
                            }
                        }
                        else{
                            $deleted = unlink($dir."/".$next_file);
                            $count++;
                            if($deleted){
                                /*
                                if($subdir == 1){
                                    echo "\t";
                                }
                                echo "-$next_file\n";
                                */
                                $theorically_deleted++;
                            }
                            else{
                                break;
                            }
                        }
                    }
                    else{
                        break;
                    }
                }
            }
            $next_file = readdir($image);
        }
        closedir($image);
        return $theorically_deleted;
    }
    return -1;
}

function is_dir_empty($dir) {
  if (!is_readable($dir)) return NULL; 
  return (count(scandir($dir)) == 2);
}

function deleteCompressedPhotos($dir, $code) {
    if(is_dir($dir)){
        $image = opendir($dir);
        $count = 0;
        $notempty=0;
        $theorically_deleted = 0;
        
        $next_file = readdir($image);

        while($next_file){
            if(notHiddenFile($next_file)){
                $notempty++;
                if(notBackground($next_file) && notBanner($next_file)){
                    if(strpos($next_file, $code) !== false){
                        if (is_dir($dir."/".$next_file)){
                            $deleted_subfolderFiles = 0;
                            if(!is_dir_empty($dir."/".$next_file)){ 
                                $deleted_subfolderFiles = deleteCompressedPhotos($dir."/".$next_file, $code);
                            }
                            if($deleted_subfolderFiles !== false){
                                $theorically_deleted += $deleted_subfolderFiles; 
                                utils::log("No files left in {$dir}/{$next_file}..", "logDeleteCompressedPhotos");
                                utils::log("removing DIR -- {$dir}/{$next_file}...", "logDeleteCompressedPhotos");
                                $deleted=rmdir($dir."/".$next_file);
                            }
                        }
                        else{
                            utils::log("removing FILE -- {$dir}/{$next_file}...", "logDeleteCompressedPhotos");
                            $deleted = unlink($dir."/".$next_file);
                            $count++;
                            if($deleted) $theorically_deleted++;
                        }
                    }
                }
            }
            $next_file = readdir($image);
        }
        
        closedir($image);
        return $theorically_deleted;
    }
    return -1;
}

//function deleteCompressedPhotos($folder, $code) {
//    $removed = false;
//    if(!is_dir($folder.$code)){
//        if (file_exists($folder . $code . ".jpg")) {
//            unlink($folder . $code . ".jpg");
//            $removed = true;
//        }
//        if (file_exists($folder . $code . "GIF.gif")) {
//            unlink($folder . $code . "GIF.gif");
//            $removed = true;
//        }
//        if (file_exists($folder . $code . "-T3D.gif")) {
//            unlink($folder . $code . "-T3D.gif");
//            $removed = true;
//        }
//        if (file_exists($folder . $code . ".wmv")) {
//            unlink($folder . $code . ".wmv");
//            $removed = true;
//        }
//        if (file_exists($folder . $code . ".mp4")) {
//            unlink($folder . $code . ".mp4");
//            $removed = true;
//        }
//        
//        if (file_exists($folder . $code . "-3D.mp4")) {
//            unlink($folder . $code . "-3D.mp4");
//            $removed = true;
//        }
//    }
//    return $removed;
//}

function eventToTrash($document_root, $id) {
    $res = false;
    $compresedZip = $document_root . "events/compressed_events/" . $id . "_compressed.zip";
    if (file_exists($compresedZip)) {
        $trashedZip = $document_root . "events/trashed_events/" . $id . "_compressed.zip";
        $res = copy($compresedZip, $trashedZip);
    }
    return $res;
}

function eliminarDir($carpeta) {
    foreach (glob($carpeta . "/*") as $archivos_carpeta) {
        echo $archivos_carpeta;
        if (is_dir($archivos_carpeta)) {
            eliminarDir($archivos_carpeta);
        } else {
            unlink($archivos_carpeta);
        }
    }
    rmdir($carpeta);
}

function delete_directory($dirname) {
    $res=false;
    if (is_dir($dirname)){
        $dir_handle = opendir($dirname);
        while($file = readdir($dir_handle)) {
            if ($file != "." && $file != "..") {
                if (!is_dir($dirname."/".$file))
                    $res=unlink($dirname."/".$file);
                else
                    $res=delete_directory($dirname.'/'.$file);
            }
            else{
                $res=true;
            }
        }
        closedir($dir_handle);
        rmdir($dirname);
    }
    else{
        echo "{$dirname} does not exist";
    }
    return $res;
}
?>
