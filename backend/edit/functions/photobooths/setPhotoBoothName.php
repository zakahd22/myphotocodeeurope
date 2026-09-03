<?php

include '../../../sessio.php';
require_once "../../../common/global.php";

$baseController = new baseController;
$baseController->createModel('App_booths');

$nom = $_POST['pbn'];
$ID = $_POST['id'];


$name = str_replace("'","''",$nom);
$array = array('name' => $name);
$upd = $baseController->App_boothsModel->updateAppBooths($ID, $array);
        
if($upd){
    $function = "ChgPbsName";
    $date     = date("YmdHis");
    $sg = strtoupper(sha1($date . $function . $ID . $nom . "ARt32qX"));
    $data = "p1=$sg&p2=$date&p3=$function&p4=$ID&p5=".urlencode($nom);
         
    utils::log(" motor_conect  to: https://pre.control.alquilafotomaton.es/mypc_service.php?$data", "log_motor_conect");//TRACE 20160323

    $res = utils::motor_conect("control.alquilafotomaton.es","mypc_service.php",$data);
    utils::log("mypc_service  res: $res", "log_motor_conect");
    
    $p = stripos($res, '{');
    utils::log("mypc_service  { in res: $p. ", "log_motor_conect");
    if($p == false){
        $response = json_decode($res);
    }
    elseif($p > 4){
        $response = json_decode($res);
    }
    else {
        utils::log("mypc_service  new res: ".substr($res,$p), "log_motor_conect");
        $response = json_decode(substr($res,$p));
    }
    utils::log("mypc_service  response:", "log_motor_conect");
    utils::log($response, "log_motor_conect");
    
    echo "OK";
}
else{
    echo "ERROR";
}
