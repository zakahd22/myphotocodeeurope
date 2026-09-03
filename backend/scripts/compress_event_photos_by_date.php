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








