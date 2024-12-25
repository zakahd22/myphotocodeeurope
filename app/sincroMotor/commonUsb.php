<?php

/*
//NOTA: el codi de generació del zip ha de ser id'entic tant per a getUSB com per a dlUSB
 */

if(!$USBidEvent) return;

    
$directoryToZip = "../../usbs/".$USBFolder."/";
$zipFileName = $directoryToZip."save-in-usb.zip";

if(isset($_REQUEST['zip'])){ $calFerZip = $_REQUEST['zip'];} 
if($calFerZip){

$directoryToZip2 = "../../printPhoto/e$idEvent/";
if (file_exists($zipFileName)) unlink($zipFileName);
ini_set("max_execution_time", 300);

$zip = new ZipArchive();
if ($zip->open($zipFileName, ZIPARCHIVE::CREATE) !== TRUE){
    fesLog("Error - $Mtr_script, Error04: $sql.");
//		echo "ERROR";           
      return;

}
$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToZip));
	foreach ($iterator as $key=>$value)
	{
               
		$keyZip = str_replace($directoryToZip,"",$key);
                 $keyZip2 = substr($keyZip, -1);
                if($keyZip2!="." && $keyZip2!= ".." && $keyZip2!= ""){
		$zip->addFile(realpath($key), $keyZip) or die ("$F22".$keyZip);
                }
            
	}
        if(file_exists($directoryToZip2)){
        $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToZip2));
	foreach ($iterator as $key=>$value)
	{
               
		$keyZip = str_replace($directoryToZip2,"",$key);
                 $keyZip2 = substr($keyZip, -1);
                if($keyZip2!="." && $keyZip2!= ".." && $keyZip2!= ""){
		$zip->addFile(realpath($key), $keyZip) or die ("$F22".$keyZip);
                }
            
	}      
        }       
 $zip->close();
 
}

?>
