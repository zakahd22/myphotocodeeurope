<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController();
$baseController->createModel('App_bootDCAllowed');

$json = json_decode($_POST["dades"], TRUE);

for($i=0; $i< count($json); $i++){
    if ($json[$i]['name']){
        $array[$json[$i]['name']] = $json[$i]['value'];
    }
}
$keyTime = date('Ymd', strtotime('-17 days')).'Allow';

if($array["secKey"]==$keyTime){

$result = "Error";
//$allowedIds = !empty($array['allowedIds']) ? $array['allowedIds']  : 'NULL';

if($array['allowedIds']==''){
    $upd = $CLD_CON->Execute("UPDATE App_bootDCAllowed set UPGRADEid = '".$array['UPGRADEid']."', idBootDC = '".$array['idBootDC']."', allowedIds = NULL, response = '".$array['response']."' WHERE id='".$array['id']."'");
}else{
    $upd = $CLD_CON->Execute("UPDATE App_bootDCAllowed set UPGRADEid = '".$array['UPGRADEid']."', idBootDC = '".$array['idBootDC']."', allowedIds = '".$array['allowedIds']."', response = '".$array['response']."' WHERE id='".$array['id']."'"); 
}

//$updates = array('UPGRADEid' => $array["UPGRADEid"], 'idBootDC' => $array["idBootDC"], 'allowedIds' => $allowedIds, 'response' => $array["response"]);
//
//$upd = $baseController->App_bootDCAllowedModel->updateApp_bootDCAllowed($array['id'], $updates);
  
if($upd){
    $result = TRUE;
}
//print "UPDATE App_bootDCAllowed set UPGRADEid = '".$array['UPGRADEid']."', idBootDC = '".$array['idBootDC']."', allowedIds = NULL, response = '".$array['response']."'";
//print "UPDATE App_bootDCAllowed set UPGRADEid = '".$array['UPGRADEid']."', idBootDC = '".$array['idBootDC']."', allowedIds = '".$array['allowedIds']."', response = '".$array['response']."'";

echo $result;
//print $array['id'];
//print_r($updates);
}else{
    echo  "Protected action. You are not allowed.".$array["secKey"];
}