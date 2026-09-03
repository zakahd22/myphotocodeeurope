<?php
//OLD
//require_once dirname (__FILE__) . "../../includes/classes/APP_common.php"; //FA PETAR APP
//require_once dirname (__FILE__) . "../../includes/classes/APP_BdD_MySQL.php"; //canvis PHP7.4
require_once dirname (__FILE__) . "/../includes/classes/APP_BdD_MySQLi.php";

$CLD_CON = getNewBdD();

function getNewBdD(){
    require 'config/config.php';

    $newBdD = new BdD();
    $e = $newBdD->OpenBdD($DB_myphotocode_web['host'], $DB_myphotocode_web['user'], $DB_myphotocode_web['pass'], $DB_myphotocode_web['database']);
    if(!$e){
//        utils::log("Mysql ERROR {$newBdD->error}", dirname (__FILE__) . "../../log/logModel");
//        utils::log("Host = {$DB_myphotocode['host']}", "log/logModel");
//        utils::log("USERNAME = {$DB_myphotocode['user']}", dirname (__FILE__) . "../../log/logModel");
//        utils::log("PASSWORD = {$DB_myphotocode['pass']}", dirname (__FILE__) . "../../log/logModel");
//        utils::log("DBNAME = {$DB_myphotocode['database']}", dirname (__FILE__) . "../../log/logModel");
    }
    return $newBdD;
}
