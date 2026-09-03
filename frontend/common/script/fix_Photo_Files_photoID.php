<?php
/*
 * Script per reparar la taula Photo_files.
 * Es repara el path que s'introduïa "../" i s'afegeix ServerID i Photoboots i el dongle
 */
    require_once dirname(__FILE__) . "/../global.php";
    require_once G_PATH . "common/Classes/baseController.php";

    print " Script Start \n \n ";
    
    $baseController = new baseController;
    $baseController->createModel('photo_Files');
    $baseController->createModel('photos');

    print "Loaded Models \n \n ";
    
    print "Loading Photo_Files... \n \n";
    $limit = 1000;
    $counterFile = 0;
    $photo_files = $baseController->photo_FilesModel->getAllErrorPhotoIdScript($limit);
    
    foreach($photo_files as $photo_file){
        $msg = "";
        $msg = "Get ";
        
        $code = explode('.', $photo_file['name']);
        $code = $code[0];
        $code = explode('-', $code);
        $code = $code[0];
        $msg .= "Photo files -> {$photo_file['name']}, Code -> {$code} ";
        
        //New Data
        $photoId = $baseController->photosModel->getPhoto($code);
        $photoId = $photoId[0]['id'];
        if($photoId){
            $msg .= " -> PhotoId {$photoId}";

            //Array Update
            $updates = array(
                'photoId' => $photoId
            );

            if($baseController->photo_FilesModel->updatePhoto_Files($photo_file['id'], $updates)){
                $msg .= " - Changed INFO";
            }
            else {
                $msg .= " - Error";
            }
            $counterFile++;
        }
        else {
            $msg .= " -> PhotoId NOT EXIST -> NOT UPDATED";
        }
//        print $msg."\n";
        print "[ ".round((($counterFile/$limit)*100),2)." % ] Updating Photo_Files..... \r";
        utils::log($msg, 'logFixPĥotoIdPhoto_Files');
        ob_flush();
        flush();
    }
    print "\n";
?>