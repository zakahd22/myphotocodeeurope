<?php
include '../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$sn = $_POST['sn'];
$type = $_POST['typeCmp'];
$cName= $_POST['cName'];
$data = date("Y-m-d H:i:s");

$CLD_CON->OpenRs("SELECT * FROM CLD_components WHERE serialnumber='$sn'");
if($CLD_CON->GetRsRows() > 0){
    echo "The serial number $sn already exists.";
}else{
    if($CLD_CON->Execute("INSERT INTO CLD_components (serialnumber ,  type, data_entrada) VALUES('$sn' , $type , '$data')")){   
        echo "The $cName $sn has been introduced";
        $text = "Introduced in BBDD.";
        $CLD_CON->Execute("INSERT INTO CLD_historyComponents(comment , data , component_sn) VALUES('$text' , '$data' , '$sn')");
    }else{
        echo "Error introducing the $cName $sn";
    }
}

?>
