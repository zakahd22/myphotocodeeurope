<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
utils::log("hola", "logasd");
$ID = $_POST['id'];
$owner = $_POST['owner'];
$t= explode("##", $_POST['type']);
$type = $t[0];
$typeNum = $t[1];
$date = date("Ymd");
$idUSB = $CLD_CON->ExecuteInsert("INSERT INTO usbs (rental_id , creation_date , title , boothtype_char , event_id , CLD_idTypeBooth) VALUES ($owner , '$date' , '-USB$date-' , '$type' , $ID , $typeNum) ");

$USBFolder = $date.$idUSB;
mkdir("../../../usbs/".$USBFolder,0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdDownload",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdDownload/myphotocode",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome/Custom",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome/Random",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye/Custom",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye/Random",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Frames",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents",0777);
mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/CustomShots",0777);

if($type=="A"){
    mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/Wedding",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/Wedding/Header",0777);
}

    $archivo = "../../../usbs/".$USBFolder."/PhotoIdDownload/myphotocode/myphotocode.dat";
    $fp = fopen($archivo, "w+");
    $string = $ID;
    fputs($fp, $string);
    fclose($fp);  


if($type =="D"){
    $idUSB = $CLD_CON->ExecuteInsert("INSERT INTO usbs (rental_id , creation_date , title , boothtype_char , event_id , CLD_idTypeBooth) VALUES ($owner , '$date' , '-USB$date-' , '$type' , $ID , $typeNum) ");

    $USBFolder = $date.$idUSB;
    mkdir("../../../usbs/".$USBFolder,0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdDownload",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdDownload/myphotocode",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome/Custom",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Welcome/Random",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye/Custom",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Bye/Random",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdUpload/Frames",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents",0777);
    mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/CustomShots",0777);
    if($boothType=="A"){
        mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/Wedding",0777);
        mkdir("../../../usbs/".$USBFolder."/PhotoIdEvents/Wedding/Header",0777);
    }

    $archivo = "../../../usbs/".$USBFolder."/PhotoIdDownload/myphotocode/myphotocode.dat";
    $fp = fopen($archivo, "w+");
    $string = $ID;
    fputs($fp, $string);
    fclose($fp);  
}

echo "OK";
                  
?>
