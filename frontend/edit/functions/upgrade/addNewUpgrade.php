<?php

include '../../../sessio.php';

require_once G_PATH . 'common/conexio.php';


$baseController = new baseController();



$baseController->createModel('App_bootDCAllowed');




$json = json_decode($_POST["dades"], TRUE);


$upgArray = Array();
for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        if ($json[$i]['name']=="UPGRADEidArr"){
            $upgArray[] = $json[$i]['value'];
        }else{
           $array[$json[$i]['name']] = $json[$i]['value']; 
        }
        
    }
}

$keyTime = date('Ymd', strtotime('-17 days')).'Allow';
//print_r($array);exit;
if($array["idBootDC"] && $array["secKey"]==$keyTime){  

//    $UPGRADEid = $array["UPGRADEid"];
    $idBootDC = $array["idBootDC"];    
    $allowedIds = !empty($array['allowedIds']) ? $array['allowedIds']  : 'NULL';
    if($array['allowedIds']===0) $allowedIds = 0;
    $response = $array["response"];
    
    
    
    foreach($upgArray as $UPGRADEid){
        
       $CLD_CON->Execute("INSERT INTO App_bootDCAllowed (UPGRADEid, idBootDC, allowedIds, response) VALUES('$UPGRADEid', '$idBootDC', $allowedIds, '$response')");
    }
   

        
    

    

$result = "Ok"; 
}else{
   $result = "Ko"; 
}



echo $result;
