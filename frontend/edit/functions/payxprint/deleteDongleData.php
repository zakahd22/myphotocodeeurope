<?php
include '../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

    $error = false;
    
    if(isset($_POST['id'])){
        $id = $_POST['id'];
        
        $sql = "DELETE FROM Pay_print_dongle
                WHERE idDongle = {$id}";
        
        if($CLD_CON->Execute($sql) != 1){
            $error = true;
        }
    }
    
    
    if(!$error){
        echo "OK";
    }
    else{
        echo "ERROR";
    }