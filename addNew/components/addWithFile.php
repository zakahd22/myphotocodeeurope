<?php
include '../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$typeId= $_POST['type'];
$data = date("Y-m-d H:i:s");
$x=0;
$addeds ="";
$errors ="";
$fh = fopen("../../tmp/componentsFile.txt",'r');
while ($line = fgets($fh)) {
    $line = trim(preg_replace('/\s\s+/', '', $line));
 $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE serialnumber='$line'");
    if($CLD_CON->GetRsRows()<1){
        $CLD_CON2->Execute("INSERT INTO CLD_components (serialnumber , type, data_entrada) VALUES('$line',$typeId,'$data')");
        $text = "Introduced in BBDD.";
        $CLD_CON->Execute("INSERT INTO CLD_historyComponents(comment , data , component_sn) VALUES('$text' , '$data' , '$line')");
    }
}
fclose($fh);
unlink("../../tmp/componentsFile.txt");