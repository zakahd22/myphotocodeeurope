<?php
/*
 * Script per pasar dades del CLD_estaditiques_photos a la nova BD mypc_statistics->statistics_photos
 */
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/Classes/baseController.php";
require_once G_PATH . "common/Classes/StatisticsController.php";

$bcMypc = new baseController();
$bcMypc->createModel('CLD_estadistiques_photos');
$bcMypc->createModel('photos');
$stdController = new StatisticsController();
$mgs = "";

$statistics_old = $bcMypc->CLD_estadistiques_photosModel->getAll();
foreach($statistics_old as $std){
    if($std["photo"] != NULL || $std["photo"] != ''){
        $eventId = $bcMypc->photosModel->getEventPhoto($std["photo"]);
        $eventId = $eventId[0]['event_id'];
        
        $date = explode(" ", $std["data"]);
        $std["data"] = date($date[0]); 
//        $std["data"] = utils::datetime_to_date_std($std["data"], 'Y-m-d H:i:s', 'Y-m-d');    //No es compatible amb php5.2.17 :O
        
        if($std["state"] == NULL || $std["state"] == '-'){
            $std["state"] = NULL;
        }
        if($std["country"] == NULL || $std["country"] == '-'){
            $std["country"] = NULL;
        }
//        print "{$std['id']} - {$std["data"]} - {$std["photo"]} - Type: {$std["type_info"]} - \n "
//            . "Writing in new BD ->";
        $mgs .= "{$std['id']} - {$std["data"]} - {$std["photo"]} - {$eventId} - Type: {$std["type_info"]} - \n "
            . "Writing in new BD ->";
        if($stdController->saveStdScript($std["type_info"], $std["photo"], $eventId, $std["data"], $std["state"], $std["country"])){
//                print " COMPLETE \n";
            $mgs .= " COMPLETE \n";
            if($bcMypc->CLD_estadistiques_photosModel->deleteEstaditiquesPhoto($std["id"])){
                $mgs .= " Old entry in CLD_estadistiques_photos deleted! \n";                
            }
        } else{
//            print " ERROR \n";
            $mgs .= " ERROR \n";
        }
        utils::log($mgs, 'script_mypcStatistics');
    }
}