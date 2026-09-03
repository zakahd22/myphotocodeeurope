<?php

require "../common/utils.php";
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
$continue = 1;
$command = "find ../events/ -type f | cut -d '/' -f 2,3 | sort | uniq -c | sort -nr ";
exec($command, $array);

    foreach ($array as $event) {
        $event = substr($event, -5);
	//echo $event . "\n";
        $command = "find ../events/compressed_events/ -name '$event*' ";
        if(exec($command, $find)){
	    //echo "trobat $event \n";
        }
        else{
	    //echo $event;
            $date=  date('Y-m-d');
            $ultima_foto = strtotime ( '-30 days' , strtotime ( $date ) );
            $start_date = strtotime ( '-3 month' , strtotime ( $date ) );
            $ultima_foto = date('Ymd', $ultima_foto);
            $start_date = date('Ymd', $start_date);
            $sql = "SELECT id , start_date FROM events WHERE (CLD_date_lastPhoto < '$ultima_foto' OR CLD_date_lastPhoto IS NULL) AND start_date < {$start_date} AND id = $event AND compressed IS NOT NULL";
            echo $sql . "\n";
            $CLD_CON->OpenRs($sql);
            while($CLD_CON->FetchArray()){
                $id = $CLD_CON->GetArrayField("id");
                echo $id . "\n";
                $update = "UPDATE events SET compressed = NULL WHERE id = $id";
		echo $update . "\n";
                $CLD_CON->Execute($update);
                
		echo "continua = $continue";
		if($continue == 10){
		    exit;
		}
		$continue = $continue + 1;
           }      
        }
    }

