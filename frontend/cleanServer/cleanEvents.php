
<?php 

ini_set('max_execution_time', 0);//300 seconds = 5 minutes
//$document_root = "/homepages/46/d399659235/htdocs/";

require_once dirname(__FILE__) . "/../common/global.php";
include G_PATH . "common/conexio.php";
include G_PATH . 'cleanServer/functions/cleanFunctions.php';

include G_PATH . 'common/Classes/eventCompressor.php';
require_once G_PATH . 'common/Classes/cleanFTPFolders.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON4 = clone($CLD_CON);
$d= date("Ymd");

ob_start();
/** 
 * Eliminar el ZIP de descarga de les fotos (Aquest es torna a genera si el tornen a solicitar).
 */
include G_PATH . "cleanServer/cleanEvents/deleteZipAllPhotos.php"; 

/**
 * Serveix per eliminar el fitxer save_file_usb dels usb.
 */
include G_PATH . "cleanServer/cleanEvents/deleteUSBZip.php";

/**
 * Eliminar les fotos dels events que han estat descomprimits perque els han anat a mirar
 */
include G_PATH . "cleanServer/cleanEvents/cleanCompressedEvents.php";

/**
 * Comprimir els events mes vells de 6 mesos i posar-los dins la carpeta compressed_events i eliminar totes les fotos de la carpeta
 */
include G_PATH . "cleanServer/cleanEvents/compressEvents.php";

//if (PHP_SAPI === 'cli'){
//    $html = false;
//}
//else {
//    $html = true;
//}

$html = false;
$eventCompressor = new eventCompressor($html);
$cleanFTPFolders = new cleanFTPFolders();
echo $cleanFTPFolders->deletedFiles . " files deleted from uploads/";
//include "./cleanEvents/eventToTrush.php"; //Copia el compressed a dins de la carpeta trashed_events i eliminar el compressed_event , i marca a la base de dades els events com a eliminats
echo "----------------- CLEAN SERVER FINISHED ----------------- \n";

$logs = ob_get_contents();
ob_end_clean();
utils::log($logs, "logCronCleanEvents");
echo "$logs<br>" ;

//ob_start();
//$dateToTrash = '2014-12-31';
//include "./cleanEvents/trashedManually.php"; //Esborrar els directoris dels events to trush fets manualment o fallits
//
//$logs = ob_get_contents();
//ob_end_clean();
//utils:log($logs, G_PATH . "log/logCronCleanEvents");
//echo $logs;



