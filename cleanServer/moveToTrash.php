<?php
ini_set('max_execution_time', 0);//300 seconds = 5 minutes
//$document_root = "/homepages/46/d399659235/htdocs";

//include "../common/utils.php";
require_once dirname(__FILE__) . "/../common/global.php";
include G_PATH . "common/conexio.php";
include G_PATH . 'cleanServer/functions/cleanFunctions.php';

$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON4 = clone($CLD_CON);
$d= date("Ymd");

ob_start();

//$sc = null;
//if (PHP_SAPI === 'cli'){
//    $sc = $argv[1];
//}

$d= date("Ymd");
$dateToTrash = date('Y-m-d',strtotime('-1 year', strtotime($d)));
echo $dateToTrash;
//exit;
//
//$dateToTrash = '2015-10-04';

include G_PATH . "cleanServer/cleanEvents/trashedManually.php"; //Esborrar els directoris dels events to trush fets manualment o fallits

$logs = ob_get_contents();
ob_end_clean();
utils::log($logs, "logMoveToTrash");

echo $logs;