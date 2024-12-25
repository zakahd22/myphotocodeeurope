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

ob_start();
echo "----------------- CHECK DB INTEGRITY START -----------------\n";
include "./DBIntegrity/checkEventsFolders.php"; // Recorrer les carpetes existents a el directori events, i si n'hi ha alguna que no estigui registrada a la db, esborrar-la.
echo "----------------- CHECK DB INTEGRITY FINISHED --------------\n";

$logs = ob_get_contents();
ob_end_clean();
//writeOnLog(G_PATH . "cleanServer/logCronDBIntegrity.txt", $logs);
utils::log($logs, "logCronDBIntegrity");
echo $logs;