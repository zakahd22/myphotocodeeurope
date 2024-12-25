<?php
/*
 * Script per reparar la taula Photo_files.
 * Es repara el path que s'introduïa "../" i s'afegeix ServerID i Photoboots i el dongle
 */
    require_once dirname(__FILE__) . "/../global.php";
    require_once G_PATH . "common/Classes/baseController.php";
    require_once G_PATH . "common/Classes/scriptFormatOutput.php";

    $fileLog = "logFix_Photo_Files_dates";
    scriptFormatOutput::CLI_echoAndLog('>---- Script Start ----<', $fileLog);
    
    $baseController = new baseController;
    $baseController->createModel('photo_Files');
    $baseController->createModel('photos');

    scriptFormatOutput::CLI_echoAndLog('Loaded Models', $fileLog);
    scriptFormatOutput::CLI_echoAndLog('Loading Photo_Files ...', $fileLog);
    
    $limit = 1000;
    $counterFile = 0;
    
    $date = utils::get_datetime();
    $photo_files = $baseController->photo_FilesModel->getAllErrorDatesScript($date, $limit);
    
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
        $date = $photoId[0]['Appusr_datetime'];
        if($photoId){
            $msg .= " -> PhotoId {$photoId}";

            //Array Update
            $updates = array(
                'date' => $date
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
        
//        print "[ ".round((($counterFile/$limit)*100),2)." % ] Updating Photo_Files..... \r";
        scriptFormatOutput::CLI_progressBAR((($counterFile/$limit)*100), 'Updating Photo_Files');
        
        utils::log($msg, 'logFixPĥotoIdPhoto_Files');
        ob_flush();
        flush();
    }
    print "\n";
?>