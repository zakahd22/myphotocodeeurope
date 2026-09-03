<?php
/*
 * Script per reparar la taula Photo_files.
 * Es repara el path que s'introduïa "../" i s'afegeix ServerID i Photoboots i el dongle
 */
    require_once dirname(__FILE__) . "/../global.php";
    require_once G_PATH . "common/Classes/baseController.php";

    print " Script Start \n \n ";
    
    $baseController = new baseController;
    $baseController->createModel('photos');
    $baseController->createModel('photo_Files');
    $baseController->createModel('CLD_Servers');

    print "Loaded Models \n \n ";
    
    // Fix CLD_Server, 1and1
    print "Getting Server id.";
    $server_id = $baseController->CLD_ServersModel->getCLD_Servers('1and1');
    if($server_id){
        $server_id = $server_id[0]['id'];
        print "  -  Completed! \n \n ";
    }
    else{
        print "  -  Error! \n \n ";
        die;
    }
    
    print "Loading Photo_Files... \n \n ";
    $photo_files = $baseController->photo_FilesModel->getAllPhotoFilesScript();
    
    foreach($photo_files as $photo_file){
        print $photo_file['id'] . " - " . $photo_file['name'] . " ";
        //New Data
        $newPath =  substr($photo_file['path'], 3);
       
        $codePhoto = explode(".", $photo_file['name']);
        $codePhoto = $codePhoto[0];
        $codePhoto = explode("-", $codePhoto);
        $codePhoto = $codePhoto[0];
        
        $infoPhoto = $baseController->photosModel->getPhoto((string)$codePhoto);

        //Array Update
        $updates = array(
            'path' => $newPath,
            'ServerId' => $server_id,
            'photobooth' => $infoPhoto[0]['pbs_id'],
            'dongle' => $infoPhoto[0]['booth_id']
        );
        
        if($baseController->photo_FilesModel->updatePhoto_Files($photo_file['id'], $updates)){
            print " - Changed INFO \n ";
        }
        else {
            print " - Error \n ";
        }
        
        
        print "------------------------------------------------------"
            . "------------------------------------------------------" . " \n ";
    }

?>