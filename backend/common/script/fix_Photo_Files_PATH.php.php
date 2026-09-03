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

    print "Loaded Models \n \n ";
    
    print "Loading Photo_Files... \n \n ";
    $photo_files = $baseController->photo_FilesModel->getAllErrorPathScript(G_PATH);
    
    foreach($photo_files as $photo_file){
        $msg = "";
        //New Data
        $newPath = str_replace(G_PATH, '', $photo_file['path']);
//        print $photo_file['path'] . "\n CHANGED \n" . $newPath . "";
        $msg .= $photo_file['id'];
        //Array Update
        $updates = array(
            'path' => $newPath
        );
        if($baseController->photo_FilesModel->updatePhoto_Files($photo_file['id'], $updates)){
            $msg .= " - Changed INFO \n";
        }
        else {
            $msg .= " - Error \n";
        }
        print $msg;
        utils::log($msg, 'logFixPathPhoto_Files');
    }

?>