<?php
require_once dirname(__FILE__) . '/../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$error = false;        
if(isset($_POST['id'])){

    $dongleId = $_POST['id'];
    $data = utils::get_date_std();

    $sql= "INSERT INTO Pay_print_dongle (idDongle , startDate , minStock , quantitat , preu , saldo) VALUES($dongleId , $data , 700, 1500, 0, 0)";
    if($CLD_CON->Execute($sql) == 0){
        $error = true;        
    }    
}
else{
    $error = true;
}

if(!$error){
    echo "OK";
}
else{
    echo "ERROR";
}