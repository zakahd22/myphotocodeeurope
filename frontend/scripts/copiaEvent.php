<?php
require "../common/utils.php";
require_once "../common/global.php";
require_once "../includes/classes/APP_BdD_MySQLi.php";

//$exec = shell_exec ( "ps aux | grep copiaEvent.php" );
////if($exec == NULL){
////    return;
////}
//utils::log($exec, "logCopia");
//return;

$CLD_CON = getNewBdD();

function getNewBdD(){
    require '../common/config/config.php';
    utils::log("entra", "logCopia");
    $newBdD = new BdDi();
    $e = $newBdD->OpenBdD($DB_myphotocode_web['host'], $DB_myphotocode_web['user'], $DB_myphotocode_web['pass'], $DB_myphotocode_web['database']);
    if(!$e){
        utils::log("Mysql ERROR {$newBdD->error}", dirname (__FILE__) . "logCopia");
        utils::log("Host = {$DB_myphotocode['host']}", "logModel");
        utils::log("USERNAME = {$DB_myphotocode['user']}", dirname (__FILE__) . "logCopia");
        utils::log("PASSWORD = {$DB_myphotocode['pass']}", dirname (__FILE__) . "logCopia");
        utils::log("DBNAME = {$DB_myphotocode['database']}", dirname (__FILE__) . "logCopia");
    }
    return $newBdD;
}

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$photos = 0;

$fecha = new DateTime();

$fecha->modify('-3 month');
$data = $fecha->format('Ym');
$data2 = new DateTime();
$data2->modify('-3 month');
$data2 = $data2->format('Y-m');
$data2 = $data2."-00 00:00:00";
$data3 = new DateTime();
$data3 = $data3->format('Ymd');
$data4 = new DateTime();
$data4->modify('-3 month');
$data4 = $data4->format('Y-m-d');
$data5 = new DateTime();
$data5 = $data5->format('Ymd');

utils::log("empieza", "logCopia");
if(fopen("events.txt")){
    $text = fgets("events.txt");
    $event_vell = substr($text, 5);
    $event_nou = substr($text, -5);
    
    $sql2 = "SELECT `code`, `path`, p.`id`, name FROM photos p, photo_Files f WHERE `event_id` = $event_vell AND p.`id` = `photoid` AND `Appusr_datetime`< '$data2' "; 
    $CLD_CON2->OpenRs($sql2);
    while ($CLD_CON2->FetchArray()) {
        $code = $CLD_CON2->GetArrayField("code");
        $path = $CLD_CON2->GetArrayField("path");
        $id = $CLD_CON2->GetArrayField("id");
        $name = $CLD_CON2->GetArrayField("name"); 
        utils::log("$code", "logCopia");
        utils::log("carpeta vella: $path", "logCopia");        
        
        //actualitza la foto per dirli que esta al nou event
        $update = "UPDATE photos SET `event_id` =  '$event_nou', CLD_pDelete = 0 where `code` = '$code'";
        utils::log($update, "logCopia");
        $CLD_CON2->Execute($update);
        
        //modifica el path per marcar la nova direccio        
        if(!strstr($path, "S_EventFiles")){
            $tiempo_inicio = microtime(true);
            $rutanova = preg_replace('/(?<=events\/)(.*?)(?=\/)/', $nom_carpeta, $path);
            $update2 = "UPDATE photo_Files SET `path` =  '$rutanova' where `path` = '$path'";
            $CLD_CON2->Execute($update2);

            $update3 = "UPDATE statistics_photos SET `event` = $event_nou WHERE `code_photo` = '$code'";
            $CLD_CON2->Execute($update3);
            //mou les fotos al nou event
            utils::log("carpeta Nova: $rutanova", "logCopia");
            if( rename ($path,$rutanova)){ 
                $existeix = stat($rutanova);
                if($existeix)unlink ($path);
                utils::log("moguda", "logCopia");
                $photos = $photos +1;
            }else{
                utils::log("error rename", "logCopia");
                $update2 = "UPDATE photo_Files SET `path` =  '$path' where `path` = '$rutanova'";
                $CLD_CON2->Execute($update2);
                $update3 = "UPDATE statistics_photos SET `event` = $event_vell WHERE `code_photo` = '$code'";
                $CLD_CON2->Execute($update3);
                $update = "UPDATE photos SET `event_id` =  '$event_vell', CLD_pDelete = 0 where `code` = '$code'";
                $CLD_CON2->Execute($update);
            }
            $tiempo_fin = microtime(true);
            utils::log("Tiempo empleado: " . date("s",$tiempo_fin - $tiempo_inicio), "logCopia");
            utils::log("despmres de moure", "logCopia");
            
        }
        
    }
    utils::log("Fixers moguts: $photos", "logCopia");
    unlink("events.txt");
    
    
    
}else{
    $sql = "SELECT * FROM `events` WHERE (`compressed` IS NULL OR `compressed` =0) AND `start_date` < {$data}00 AND `duplicat` = 0 AND `copiat_a` = 0"; 
    $CLD_CON->OpenRs($sql);
    utils::log("SQL: $sql </bgr>", "logCopia");
    $array[] ="";
    while ($CLD_CON->FetchArray()) {

        $event_vell = $CLD_CON->GetArrayField("id");
        $rental_id = $CLD_CON->GetArrayField("rental_id");
        $start_date = $CLD_CON->GetArrayField("start_date");
        $title = $CLD_CON->GetArrayField("title");
        $background_id = $CLD_CON->GetArrayField("background_id");
        $CLD_banner = $CLD_CON->GetArrayField("CLD_banner");
        $CLD_banner_URL = $CLD_CON->GetArrayField("CLD_banner_URL");
        $private = $CLD_CON->GetArrayField("private");
        $autocreated = $CLD_CON->GetArrayField("autocreated");
        $ftp_folder_id = $CLD_CON->GetArrayField("ftp_folder_id");
        $available = $CLD_CON->GetArrayField("available");
        $CLD_invitedName = $CLD_CON->GetArrayField("CLD_invitedName");
        $CLD_invitedEmail = $CLD_CON->GetArrayField("CLD_invitedEmail");
        $CLD_SecurityCode = $CLD_CON->GetArrayField("CLD_SecurityCode");
        $CLD_eventManegerId = $CLD_CON->GetArrayField("CLD_eventManegerId");
        $CLD_date_lastPhoto = $CLD_CON->GetArrayField("CLD_date_lastPhoto");
        $hashtag = $CLD_CON->GetArrayField("hashtag");
        $checked = $CLD_CON->GetArrayField("checked");
        $compressed = $CLD_CON->GetArrayField("compressed");
        $trashed = $CLD_CON->GetArrayField("trashed");
        $newServer = $CLD_CON->GetArrayField("newServer");
        $duplicat = $CLD_CON->GetArrayField("duplicat");
        $copiat_a = $CLD_CON->GetArrayField("copiat_a");

        utils::log($event_vell, "events");   

        //si el camp esta buit escriu NULL
        if(!$CLD_banner_URL)$CLD_banner_URL = 'NULL';
        if(!$CLD_invitedName)$CLD_invitedName = 'NULL';
        if(!$CLD_invitedEmaiul)$CLD_invitedEmaiul = 'NULL';
        if(!$CLD_SecurityCode)$CLD_SecurityCode = 'NULL';
        if(!$CLD_eventManagerID)$CLD_eventManagerID = 'NULL';
        if(!$CLD_date_lastPhoto)$CLD_date_lastPhoto = 'NULL';
        if(!$hastag)$hastag = 'NULL';
        if(!$checked)$checked = 'NULL';
        if(!$compressed)$compressed = 'NULL';
        if(!$trashed)$trashed = 'NULL';
        
        //duplica l'event a la base de dades
        $insert =  "INSERT INTO events(`rental_id` , `start_date` , `title` , `background_id` , `CLD_banner` , `CLD_banner_URL` , `private` , `autocreated` , `ftp_folder_id` , `available` , `CLD_invitedName` , `CLD_invitedEmail` , `CLD_SecurityCode` , `CLD_eventManegerId` , `CLD_date_lastPhoto` , `hashtag` , `checked` , `compressed` , `trashed` , `newServer` , `duplicat` ) "
                . "VALUES (            $rental_id,    $data3,  '{$title}_April',$background_id,  $CLD_banner,  '$CLD_banner_URL',  $private,   $autocreated,   $ftp_folder_id,   $available, '$CLD_invitedName', '$CLD_invitedEmail', '$CLD_SecurityCode', '$CLD_eventManegerId',  '$data4',             '$hashtag',   $checked,   $data5,        $trashed,   $newServer,    1 )";
        utils::log("insert copia event: $insert </bgr>", "logCopia");
        $CLD_CON->Execute($insert);  
        //agafa la id de la copia
        $sql3 = "SELECT LAST_INSERT_ID() AS id FROM events ORDER BY `events`.`id` DESC LIMIT 1 ";
        
        $CLD_CON3->OpenRs($sql3);
        while ($CLD_CON3->FetchArray()) {
            $event_nou = $CLD_CON3->GetArrayField("id");
            utils::log("nova id: $event_nou", "logCopia");
            $update = "UPDATE events SET `copiat_a` = $event_nou WHERE `id` = '$event_vell'";
            $CLD_CON->Execute($update);
            utils::log("update per saber a quin event s'esta copian: $update </bgr>", "logCopia");
            
            fopen("events.txt", "w");
            fwrite("events.txt", "$event_vell - $event_nou");
            $carpeta_vella = $start_date.$event_vell;
            $nom_carpeta = $data3.$event_nou;

            //Crea la carpeta per el nou event
            if(!file_exists ( "../events/$nom_carpeta" )){
                mkdir("../events/$nom_carpeta", 0777, true);
                chmod("../events/$nom_carpeta", 0777);
            }
            utils::log("Nova carpeta: $nom_carpeta", "logCopia");
            //Select que agafa el nom de la foto, la direccio on esta guardada i el codi d'aquesta
            $sql2 = "SELECT `code`, `path`, p.`id`, name FROM photos p, photo_Files f WHERE `event_id` = $event_vell AND p.`id` = `photoid` AND `Appusr_datetime`< '$data2' "; 
            $CLD_CON2->OpenRs($sql2);
            while ($CLD_CON2->FetchArray()) {
                $code = $CLD_CON2->GetArrayField("code");
                $path = $CLD_CON2->GetArrayField("path");
                $nouPath = "../".$CLD_CON2->GetArrayField("path");
                $id = $CLD_CON2->GetArrayField("id");
                $name = $CLD_CON2->GetArrayField("name"); 
                utils::log("$code", "logCopia");
                utils::log("carpeta vella: $nouPath", "logCopia");

                //actualitza la foto per dirli que esta al nou event
                $update = "UPDATE photos SET `event_id` =  '$event_nou', CLD_pDelete = 0 where `code` = '$code'";
                utils::log($update, "logCopia");
                $CLD_CON2->Execute($update);

                //modifica el path per marcar la nova direccio        
                if(!strstr($path, "S_EventFiles")){
                    $tiempo_inicio = microtime(true);
                    $rutanova = "../events/$nom_carpeta/$name";
                    $update2 = "UPDATE photo_Files SET `path` =  '$rutanova' where `path` = '$path'";
                    $CLD_CON2->Execute($update2);

                    $update3 = "UPDATE statistics_photos SET `event` = $event_nou WHERE `code_photo` = '$code'";
                    $CLD_CON2->Execute($update3);
                    //mou les fotos al nou event
                    utils::log("carpeta Nova: $rutanova", "logCopia");
                    if( rename ($nouPath,$rutanova)){ 
                        $existeix = stat($rutanova);
                        if($existeix)unlink ($path);
                        utils::log("moguda", "logCopia");
                        $photos = $photos +1;
                    }else{
                        utils::log("error rename", "logCopia");
                        $update2 = "UPDATE photo_Files SET `path` =  '$path' where `path` = '$rutanova'";
                        $CLD_CON2->Execute($update2);
                        $update3 = "UPDATE statistics_photos SET `event` = $event_vell WHERE `code_photo` = '$code'";
                        $CLD_CON2->Execute($update3);
                        $update = "UPDATE photos SET `event_id` =  '$event_vell', CLD_pDelete = 0 where `code` = '$code'";
                        $CLD_CON2->Execute($update);
                    }
                    $tiempo_fin = microtime(true);
                    utils::log("Tiempo empleado: " . date("s",$tiempo_fin - $tiempo_inicio), "logCopia");
                    utils::log("despmres de moure", "logCopia");
                }
            }
        }
    }
}






    

