<?php
ob_start();
require dirname (__FILE__) . '/../../common/global.php';

ob_clean();

$function = $_REQUEST["f"];

switch ($function) {
    case "getF":
        require_once G_PATH . "app/myphotocodeManager/controller/trashController.php";
        header('Content-Type: application/json');
        
        $TrashfCtrl = new TrashControler();
        
        $TrashfCtrl->getTimeScript(true);

        $data =  $TrashfCtrl->setEvents3DFolders();
        
        $TrashfCtrl->getTimeScript();
        
        $time = bcsub($TrashfCtrl->time_end, $TrashfCtrl->time_start, 4);
//        utils::log("<------------------------->", "logTimeScripts");
//        utils::log("TrashController->SetEvents3DFolders", "logTimeScripts");
//        utils::log("Time: " . $time, "logTimeScripts");
//        utils::log("<------------------------->", "logTimeScripts");
        
        echo json_encode($data);

    break;

    case "setF":
        require_once G_PATH . "app/myphotocodeManager/controller/trashController.php";
        header('Content-Type: application/json');
        
        $TrashfCtrl = new TrashControler();
        
        $json = $_REQUEST["p1"];        
        $arrayFiles = json_decode($json);
        
        $response = $TrashfCtrl->delEvents3DFolders($arrayFiles);
        
        echo json_encode($response);
        
    break;

    case "checkF":
        require_once G_PATH . "app/myphotocodeManager/controller/trashController.php";
        header('Content-Type: application/json');
        
        $TrashfCtrl = new TrashControler();
        
        $json = $_REQUEST["p1"];        
        $arrayFiles = json_decode($json);
        
        $response = $TrashfCtrl-> check3DFilesExits($arrayFiles);
        
        echo json_encode($response);
        
    break;
}