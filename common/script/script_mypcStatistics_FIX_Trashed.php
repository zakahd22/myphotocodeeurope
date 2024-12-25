<?php
/*
 * Reparar EVENT ID de les foto que es visualitzen i no s'ha guardat correctament.
 * Funciona amb la BD Trashed, per si s'ha colat alguna foto vella
 */
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/Classes/baseController.php";

$bcmypc = new baseController();
$bcmypc->createModel('photos', false, 'myphotocode_trashed');
$bcmypc->createModel('statistics_photos', false, 'myphotocode_statistics');

$statistics = $bcmypc->statistics_photosModel->getStatisticEvent('0');
print "Num Statistics to FIX: ".count($statistics)."\n\n";

foreach($statistics as $statistic){
    $event = $bcmypc->photosModel->getPhoto($statistic['code_photo']);
    if($event[0]['event_id']){
        $eventid = $event[0]['event_id'];
        print "Photo -> {$statistic['code_photo']} -> Id event: {$eventid}";
        $update = array(
            'event' => $eventid
        );
        if($bcmypc->statistics_photosModel->updateStd_photos($statistic['id'], $update)){
            print " --- FIXED!\n";
        }
        else {
            print " --- ERROR! :(\n";
            utils::log('Update Delete id:'.$statistic['id'], 'logFIXStatisticsScript');
        }
    }
//    else {
//        if($bcmypc->statistics_photosModel->deletedStd_photos($statistic['id'])){
//            print " --- NOT FOUND - DELETED!\n";
//        }
//        else {
//            print " --- NOT FOUND - ERROR! :(\n";
//            utils::log('Error Delete id:'.$statistic['id'], 'logFIXStatisticsScript');
//        }
//    }
}

print "END SCRIPT \n\n";