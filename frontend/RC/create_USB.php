<?php
include '../common/global.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_GET['ev'];
$owner = "0";
$type = "A";
$typeNum = "1";
$date = date("Ymd");
$idUSB = $CLD_CON->ExecuteInsert("INSERT INTO usbs (rental_id , creation_date , title , boothtype_char , event_id , CLD_idTypeBooth) VALUES ($owner , '$date' , '-USB$date-' , '$type' , $ID , $typeNum)");
echo $idUSB;
$USBFolder = $date.$idUSB;


                        mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder,0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdDownload",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdDownload/myphotocode",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Welcome",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Welcome/Custom",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Welcome/Random",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Bye",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Bye/Custom",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Bye/Random",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdUpload/Frames",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdEvents",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdEvents/CustomShots",0777);
                        if($type=="A"){
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdEvents/Wedding",0777);
			mkdir($_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdEvents/Wedding/Header",0777);
                        }

$archivo = $_SERVER['DOCUMENT_ROOT']."/usbs/".$USBFolder."/PhotoIdDownload/myphotocode/myphotocode.dat";
$fp = fopen($archivo, "w+");
$string = $ID;
fputs($fp, $string);
fclose($fp);  
?>

