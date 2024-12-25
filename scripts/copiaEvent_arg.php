<?php
require "../common/utils.php";
//include 'conf.php';
require_once "../common/global.php";
require_once "../includes/classes/APP_BdD_MySQLi.php";

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


$event_vell = $argv[1];
$event_nou = $argv[2];

//select per saber quins events es tenen que agafar (agafa tots aquells que tenen mes de 3 messos de creacio i no an sigut comprimits)

$sql = "SELECT * FROM `events` WHERE id = {$event_vell}"; 
$CLD_CON->OpenRs($sql);
utils::log("SQL: $sql </bgr>", "logCopia");
//echo $data."<br><br> Pinta els events que agafa amb les fotos que es mouran: <br>";
$array[] ="";
while ($CLD_CON->FetchArray()) {
    
    
    $EventID = $CLD_CON->GetArrayField("id");
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

utils::log($EventID, "events");   

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

    if(!$event_nou){
//duplica l'event a la base de dades
        $insert =  "INSERT INTO events(`rental_id` , `start_date` , `title` , `background_id` , `CLD_banner` , `CLD_banner_URL` , `private` , `autocreated` , `ftp_folder_id` , `available` , `CLD_invitedName` , `CLD_invitedEmail` , `CLD_SecurityCode` , `CLD_eventManegerId` , `CLD_date_lastPhoto` , `hashtag` , `checked` , `compressed` , `trashed` , `newServer` , `duplicat` ) "
                  . "VALUES (            $rental_id,    $data3,  '{$title}_April',$background_id,  $CLD_banner,  '$CLD_banner_URL',  $private,   $autocreated,   $ftp_folder_id,   $available, '$CLD_invitedName', '$CLD_invitedEmail', '$CLD_SecurityCode', '$CLD_eventManegerId',  '$data4',             '$hashtag',   $checked,   $data5,        $trashed,   $newServer,    1 )";
    //    echo $insert;
        utils::log("insert copia event: $insert </bgr>", "logCopia");
//        echo "insert copia event: $insert        |        ";
//li diu la id de la copia a l'original
        $CLD_CON->Execute($insert);  
//agafa la id de la copia
        $sql3 = "SELECT LAST_INSERT_ID() AS id FROM events ORDER BY `events`.`id` DESC LIMIT 1 ";
    }else{
        $sql3 = "SELECT id FROM events WHERE id =  $event_nou";
    }
    
    $CLD_CON3->OpenRs($sql3);
    while ($CLD_CON3->FetchArray()) {
        $last_id = $CLD_CON3->GetArrayField("id");
        utils::log("nova id: $last_id", "logCopia");
        $update = "UPDATE events SET `copiat_a` = $last_id WHERE `id` = '$EventID'";
//        echo $update;
        $CLD_CON->Execute($update);
         utils::log("update per saber a quin event s'esta copian: $update </bgr>", "logCopia");
    
//    
//    echo $EventID."<br>";
//    
//agafa les fotos que tindra que moure al nou event
//    echo $sql."<br>";
    
//les mou cambia l'event al que pertany en la base de dades
    
    $carpeta_vella = $start_date.$EventID;
    $nom_carpeta = $data3.$last_id;
    
    
//Crea la carpeta per el nou event
//    echo $nom_carpeta."<br>";
//    echo $carpeta_vella."<br>";
    if(!file_exists ( "events/$nom_carpeta" )){
        mkdir("events/$nom_carpeta", 0777, true);
        chmod("events/$nom_carpeta", 0777);
    }
     utils::log("Nova carpeta: $nom_carpeta", "logCopia");
//Select que agafa el nom de la foto, la direccio on esta guardada i el codi d'aquesta
    $sql2 = "SELECT `code`, `path`, p.`id`, name FROM photos p, photo_Files f WHERE `event_id` = $EventID AND p.`id` = `photoid` AND `Appusr_datetime`< '$data2' "; 
    $CLD_CON2->OpenRs($sql2);
    while ($CLD_CON2->FetchArray()) {
        $code = $CLD_CON2->GetArrayField("code");
        $path = $CLD_CON2->GetArrayField("path");
        $id = $CLD_CON2->GetArrayField("id");
        $name = $CLD_CON2->GetArrayField("name"); 
        utils::log("$code", "logCopia");
        utils::log("carpeta vella: $path", "logCopia");
        
//        echo "$code         |        ";
        
//actualitza la foto per dirli que esta al nou event
        $update = "UPDATE photos SET `event_id` =  '$last_id', CLD_pDelete = 0 where `code` = '$code'";
        utils::log($update, "logCopia");
//        echo $update."<br><br>";
        $CLD_CON2->Execute($update);
//        
//modifica el path per marcar la nova direccio        
        if(!strstr($path, "S_EventFiles")){
            $tiempo_inicio = microtime(true);
            //        echo "<br>".$id."<br>";
//          $rutanova = preg_replace('/(?<=events\/)(.*?)(?=\/)/', '2015111214674', $rutaoriginal);
            $rutanova = preg_replace('/(?<=events\/)(.*?)(?=\/)/', $nom_carpeta, $path);
            $update2 = "UPDATE photo_Files SET `path` =  '$rutanova' where `path` = '$path'";
            $CLD_CON2->Execute($update2);
    //        echo $update2."<br>";

            $update3 = "UPDATE statistics_photos SET `event` = $last_id WHERE `code_photo` = '$code'";
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
                $update3 = "UPDATE statistics_photos SET `event` = $EventID WHERE `code_photo` = '$code'";
                $CLD_CON2->Execute($update3);
                $update = "UPDATE photos SET `event_id` =  '$EventID', CLD_pDelete = 0 where `code` = '$code'";
                $CLD_CON2->Execute($update);
            }
            $tiempo_fin = microtime(true);
            utils::log("Tiempo empleado: " . date("s",$tiempo_fin - $tiempo_inicio), "logCopia");
            utils::log("despmres de moure", "logCopia");
            
        }
        
    }

        
        
    }
    utils::log("Fixers moguts: $photos", "logCopia");
////    echo "<br>";
//    echo "event compres";
}




    

