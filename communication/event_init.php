<?php

include_once "../common/global.php";
//error_log( "TO_DELETE event_init 00" );
//include_once G_PATH . "common/general.php";
//error_log( "TO_DELETE event_init 01" );
require_once G_PATH . "common/Classes/baseController.php";
//error_log( "TO_DELETE event_init 02" );

$baseController = new baseController();
$baseController->createModel('booths');
$baseController->createModel('events');
$baseController->createModel('ftp_folders');
$baseController->createModel('photos');

//error_log( "TO_DELETE event_init 03" );

function createNewEvent($baseController, $rental_id, $rand_string) {
    $currentDate = date('Ymd');
    $title = date('F Y'); //20131014
    $title.= " - *" . $rand_string;
    
    
    $baseController->entity->loadEntity('events');
    $baseController->entity->setValue("title", $title);
    $baseController->entity->setValue("rental_id", $rental_id);
    $baseController->entity->setValue("start_date", $currentDate);
    $event_id = $baseController->eventsModel->insertEvent();    
    
    
    
    if(!$event_id)  die("ko#event#insert");
    

    mkdir("../events/" . $currentDate . $event_id, 0777);
    
    return $event_id;
}

function getFtpInfo($baseController, $event_id, $ftp_folder_id) {
            
    if ($ftp_folder_id == 0) {
        $ftp_folder = $baseController->ftp_foldersModel->getftp_folderRand();
        $id = $ftp_folder[0]["id"];
        
        $updates = array('ftp_folder_id' => $id);
        $baseController->ftp_foldersModel->updateFtp_folder($event_id, $updates);
    } 
    else {
        $ftp_folder = $baseController->ftp_foldersModel->getftp_folder($ftp_folder_id);
//        $ftp_folder = mysql_fetch_array(mysql_query("SELECT * FROM ftp_folders WHERE id=$ftp_folder_id"));
    }

    $host = $ftp_folder[0]['host'];
    
//    $host = "amazing-leavitt.82-223-15-221.plesk.page";//202412 a eliminar quan es modifiquin els registres de ftp_folders
    
//20250201 INICI
//    if (in_array(, [8777,8775,8778,8779])) {
//
//    }
//Sense filtre!!! per a tots!!!!!
    $host = "amazing-leavitt.82-223-15-221.plesk.page";
//20250201 FINAL
 
    $user = $ftp_folder[0]['user'] . (G_TEST == 0 ? '' : '-sand');    //En el cas de sandbox, els usuaris han d'apuntar al directori de proves. Només si la maquina esta configurada.
    $path = $ftp_folder[0]['path'];
    $pass = $ftp_folder[0]['password'];

    VIC_fesLog("event_init, TRACE getFtpInfo:  $user");


    //return $host."#".$user."#".$path."#".$pass;
    return $host . "#" . $user . "#/#" . $pass;
}

function VIC_fesLog($text) {
    if (filesize("logVIC.dat") > 5000000) {
        copy("logVIC.dat", "logVIC-" . date('YmdHis') . "-" . rand(10, 99) . ".bak"); //20150319vic
        $fh = fopen("logVIC.dat", 'w');
    } else
        $fh = fopen("logVIC.dat", 'a');
    fwrite($fh, date('Y-m-d H:i:s ') . $text . "\n");
    fclose($fh);
}

$dongle = $_REQUEST['dongle'];
$event_id = $_REQUEST['event_id'];

VIC_fesLog("event_init, dongle: $dongle; event_id: $event_id"); 

if (!$dongle) die("ko#dongle");
$booth = $baseController->boothsModel->getBooth($dongle);

if(!$booth) die("ko#booth#null");
$rental_id   = $booth[0]['rental_id'];
$rand_string = $booth[0]['rand_string'];
 
if (!$event_id) {
    $event_id = createNewEvent($baseController, $rental_id, $rand_string);
    
    $ftpInfo = getFtpInfo($baseController, $event_id, 0);

    VIC_fesLog("event_init, dongle: $dongle; no event ok#" . $event_id . "#" . $ftpInfo); //20130504

//202412merda_die    die("ok#" . $event_id . "#" . $ftpInfo);
    echo "ok#" . $event_id . "#" . $ftpInfo; return;//202412merda_die
}

$event = $baseController->eventsModel->getEvent($event_id, $rental_id);

$event_autocreated   = $event[0]['autocreated'];
$event_start_date    = $event[0]['start_date'];
$event_title         = $event[0]['title'];
$event_ftp_folder_id = $event[0]['ftp_folder_id'];

if (!$event) { 
    $event_id = createNewEvent($baseController, $rental_id, $rand_string);
    $ftpInfo = getFtpInfo($baseController, $event_id, 0);

    VIC_fesLog("event_init, dongle: $dongle; event & dongle from diferent owners ok#" . $event_id . "#" . $ftpInfo); //20130504

//202412merda_die    die("ok#" . $event_id . "#" . $ftpInfo);
    echo "ok#" . $event_id . "#" . $ftpInfo; return;//202412merda_die
}

VIC_fesLog("event_init, dongle: $dongle; def event_id: $event_id; autocreated?: {$event_autocreated}"); //20130504

if ($event_autocreated) {
    $currentMonth = date('Ym');
    $eventMonth = intval($event_start_date / 100);

    $newtitle = date('F Y');
    $newtitle.= " - *" . $rand_string;
    if ($newtitle == $event_title)
        $titolsDiferents = false;
    else
        $titolsDiferents = true;

    VIC_fesLog("TRACEevent_init autocreated, currentMonth: $currentMonth; eventMonth: $eventMonth, newtitle: $newtitle, event['title']: {$event_title}, titolsDiferents: $titolsDiferents"); //20130504
    if (($currentMonth > $eventMonth ) && $titolsDiferents) {
        $event_id = createNewEvent($baseController, $rental_id, $rand_string);
        $ftpInfo = getFtpInfo($baseController, $event_id, 0);
        VIC_fesLog("event_init, dongle: $dongle; currentDate > event_start ok#" . $event_id . "#" . $ftpInfo); //20130504

//202412merda_die        die("ok#" . $event_id . "#" . $ftpInfo);
    } 
    else {
        $ftpInfo = getFtpInfo($baseController, $event_id, $event_ftp_folder_id);
        VIC_fesLog("event_init, dongle: $dongle; currentDate <= event_start ok#" . $event_id . "#" . $ftpInfo); //20130504

//202412merda_die        die("ok#" . $event_id . "#" . $ftpInfo);
    }
} 
else {
    $ftpInfo = getFtpInfo($baseController, $event_id, $event_ftp_folder_id);
    VIC_fesLog("event_init, dongle: $dongle; event not autocreated ok#" . $event_id . "#" . $ftpInfo); //20130504
//202412merda_die    die("ok#" . $event_id . "#" . $ftpInfo);
    
}
echo "ok#" . $event_id . "#" . $ftpInfo;//202412merda_die
