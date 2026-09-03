<?php
ob_start();
require dirname (__FILE__) . '/../../common/global.php';

ob_clean();
        
utils::log("TRACE 0", "logBackupService");

$function = $_REQUEST["f"];

switch ($function) {
    case "getB":
        require_once G_PATH . "app/myphotocodeManager/controller/backupController.php";
        header('Content-Type: application/json');
        
        $BackupCtrl = new backupController();
        
        $data = array();
        utils::log("TrashController->getDB_backup", "logBackupService");
        $data = $BackupCtrl->getDB_backup();
        utils::log("-------------------------", "logBackupService");
        
        echo json_encode($data);

        break;
    
    case "rmB":
        require_once G_PATH . "app/myphotocodeManager/controller/backupController.php";
        header('Content-Type: application/json');
        
        $BackupCtrl = new backupController();
        
        utils::log("TrashController->removeDB_backup", "logBackupService");
        $data = $BackupCtrl->removeDB_backup();
        utils::log("-------------------------", "logBackupService");
        
        echo json_encode($data);

        break;
    
    case "getMirrorF":
        utils::log("TRACE 0", "logBackupService");

        require_once G_PATH . "app/myphotocodeManager/controller/backupController.php";
        header('Content-Type: application/json');
        
        $BackupCtrl = new backupController();

        utils::log("TrashController->getMirrorF", "logBackupService");        
        $data = $BackupCtrl->getFiles_mirroring();
        utils::log("-------------------------", "logBackupService");
        
        echo json_encode($data);

        break;
    
    case "setAsMirrored":
        require_once G_PATH . "app/myphotocodeManager/controller/backupController.php";
        header('Content-Type: application/json');
        
        $BackupCtrl = new backupController();

        utils::log("TrashController->setAsMirrored", "logBackupService", "setAsMirrored");        
        $data = $BackupCtrl->setSuccess_mirroring();
        utils::log("-------------------------", "logBackupService", "setAsMirrored");
        
        echo json_encode($data);

        break;
    
    case "setAsUnmirrorable":
        require_once G_PATH . "app/myphotocodeManager/controller/backupController.php";
        header('Content-Type: application/json');
        
        $BackupCtrl = new backupController();

        utils::log("TrashController->setAsMirrored", "logBackupService", "setAsMirrored");        
        $data = $BackupCtrl->setAsUnmirrorable_mirroring();
        utils::log("-------------------------", "logBackupService", "setAsMirrored");
        
        echo json_encode($data);

        break;
}