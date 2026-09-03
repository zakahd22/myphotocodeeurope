<?php
require "../../../common/utils.php";
include '../../../conf.php';
require_once "../../../common/global.php";
require_once '../../../common/conexio.php';

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);

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


//select per saber quins events es tenen que agafar (agafa tots aquells que tenen mes de 3 messos de creacio i no an sigut comprimits)

$sql = "SELECT * FROM `events` WHERE (`compressed` IS NULL OR `compressed` =0) AND `start_date` < {$data}00 AND `duplicat` = 0 AND `copiat_a` = 0 LIMIT 1"; 
$CLD_CON->OpenRs($sql);
echo "Primer select <br>".$sql."<br><br> Data ";
echo $data."<br><br> Pinta els events que agafa amb les fotos que es mouran: <br>";
$array[] ="";
while ($CLD_CON->FetchArray()) {
    
    
    $EventID = $CLD_CON->GetArrayField("id");
    array_push($array, $EventID);
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

    
echo $EventID." <br>";

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
    if($duplicat == 0 && $copiat_a == 0){
        $insert =  "INSERT INTO events(`rental_id` , `start_date` , `title` , `background_id` , `CLD_banner` , `CLD_banner_URL` , `private` , `autocreated` , `ftp_folder_id` , `available` , `CLD_invitedName` , `CLD_invitedEmail` , `CLD_SecurityCode` , `CLD_eventManegerId` , `CLD_date_lastPhoto` , `hashtag` , `checked` , `compressed` , `trashed` , `newServer` , `duplicat` ) "
                  . "VALUES (            $rental_id,    $data3,  '{$title}-april-2019',$background_id,  $CLD_banner,  '$CLD_banner_URL',  $private,   $autocreated,   $ftp_folder_id,   $available, '$CLD_invitedName', '$CLD_invitedEmail', '$CLD_SecurityCode', '$CLD_eventManegerId',  '$data4',             '$hashtag',   $checked,   $data5,        $trashed,   $newServer,    1 )";
    //    echo $insert;

//li diu la id de la copia a l'original
        $CLD_CON->Execute($insert);
        $update = "UPDATE events SET `copiat_a` = LAST_INSERT_ID() WHERE `id` = '$EventID'";
//        echo $update;
        $CLD_CON->Execute($update);
    }

//agafa la id de la copia
    $sql3 = "SELECT LAST_INSERT_ID() FROM events";
    $CLD_CON3->OpenRs($sql3);
    while ($CLD_CON3->FetchArray()) {
        $last_id = $CLD_CON3->GetArrayField("LAST_INSERT_ID()");
//        echo $last_id."<br>";
    }
    
//    echo $EventID."<br>";
    
//agafa les fotos que tindra que moure al nou event
//    echo $sql."<br>";
    
//les mou cambia l'event al que pertany en la base de dades
    
    $carpeta_vella = $start_date.$EventID;
    $nom_carpeta = $data3.$last_id;
    
    
//Crea la carpeta per el nou event
//    echo $nom_carpeta."<br>";
//    echo $carpeta_vella."<br>";
    if(!file_exists ( "../../../events/$nom_carpeta" )){
        mkdir("../../../events/$nom_carpeta", 0777, true);
        chmod("../../../events/$nom_carpeta", 0777);
    }
    
//Select que agafa el nom de la foto, la direccio on esta guardada i el codi d'aquesta
    $sql2 = "SELECT `code`, `path`, p.`id`, name FROM photos p, photo_Files f WHERE `event_id` = $EventID AND p.`id` = `photoid` AND `Appusr_datetime`< '$data2' "; 
    $CLD_CON2->OpenRs($sql2);
    echo $sql2."<br><br><br><br><br><br>";
    while ($CLD_CON2->FetchArray()) {
        $code = $CLD_CON2->GetArrayField("code");
        $path = $CLD_CON2->GetArrayField("path");
        $id = $CLD_CON2->GetArrayField("id");
        $name = $CLD_CON2->GetArrayField("name");        
        echo $code."<br>";
        
//actualitza la foto per dirli que esta al nou event
        $update = "UPDATE photos SET `event_id` =  $last_id,CLD_pDelete = 0 where `code` = '{$code}'";
//        echo $update."<br><br>";
        $CLD_CON2->Execute($update);
        
//modifica el path per marcar la nova direccio        
        $rutaoriginal = $path;
//        echo "<br>".$id."<br>";
//        $rutanova = preg_replace('/(?<=events\/)(.*?)(?=\/)/', '2015111214674', $rutaoriginal);
        $rutanova = preg_replace('/(?<=events\/)(.*?)(?=\/)/', $nom_carpeta, $rutaoriginal);
        $update2 = "UPDATE photo_Files SET `path` =  '$rutanova' where `path` = '$rutaoriginal'";
        $CLD_CON2->Execute($update2);
//        echo $update2."<br>";
        
        $update3 = "UPDATE statistics_photos SET `event` = $EventID WHERE `code_photo` = '$code'";
        $CLD_CON2->Execute($update3);
//mou les fotos al nou event
        if(file_exists ( "../../../$rutaoriginal" )){
            rename ("../../../$rutaoriginal","../../../$rutanova");
        }
        
//crea el zip i fica les fotos
        $filename = "../../../events/compressed_events/{$last_id}_compressed.zip";
////        echo $filename."<br>";
//        $zip = new ZipArchive();
//        if($zip->open($filename, ZIPARCHIVE::CREATE)===true){
//            echo "obert<br>".$name;
//            if($zip->addFile("../../../$rutanova", $name)){
//                    echo "si<br>";
//            }
//            $zip->close();
//            unlink("../../../$rutanova");
//        }
      
    }
//    echo "<br>";
        
}




    

