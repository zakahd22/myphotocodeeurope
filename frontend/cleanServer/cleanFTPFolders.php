<?php
ini_set('max_execution_time', 0);//300 seconds = 5 minutes
$document_root = "/homepages/46/d399659235/htdocs";

//include "../common/utils.php";
include "../conf.php";
include "../conexio.php";
include './functions/cleanFunctions.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON4 = clone($CLD_CON);
$d= date("Ymd");

ob_start();

echo "----------------- CLEAN FTP FOLDERS START -----------------\n";

echo "----------------- CLEAN FTP FOLDERS FINISHED --------------\n";

$logs = ob_get_contents();
ob_end_clean();
//writeOnLog("{$document_root}/cleanServer/logCronCleanEvents.txt", $logs);
utils::log($logs, "logCronCleanEvents");