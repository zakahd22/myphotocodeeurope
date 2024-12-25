<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

utils::log("Trace0", "logTestPhotobooth");

$baseController = new baseController();

utils::log("Trace1", "logTestPhotobooth");

$baseController->createModel('App_booths');



$boothTypes = $baseController->App_boothsModel->getBoothstypefil($USERID);

$boothTypes = $boothTypes['CLD_boothTypes'];

$array = [''];
foreach ($boothTypes as $boothType){
    array_push($array, $boothType['name']);
}

utils::log($array, "logTestPhotobooth");

