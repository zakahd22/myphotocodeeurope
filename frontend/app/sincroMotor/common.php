<?php 
//error_reporting(0);
//ini_set('display_errors', 0);

fesLog("common for $Mtr_script");

header('Content-Type:text/plain; charset=UTF-8');
require("../common/APP_BdD.php");

//controls
$MTR_ok = false;
$MTR_status = "";
//paràmetres comuns
if(isset($_REQUEST['idm'])){ $MTR_idMtr = $_REQUEST['idm'];} 
else {
    fesLog("Error - code missing idm. Common00.");
    $MTR_status = "Error-Common00";
    return;
}
if(isset($_REQUEST['sg'])){ $MTR_sg = $_REQUEST['sg'];} else { $MTR_sg = "";}
if(isset($_REQUEST['tact'])){ $MTR_tact = $_REQUEST['tact'];} else { $MTR_tact = "";}
//if(isset($_REQUEST[''])){ $MTR_ = $_REQUEST[''];} else { $MTR_ = "";}
$MTR_MtrDescr = "";
$MTR_MtrControl = "";
//llegiré el codi de control i descripció a la BdD  NOTA: potser caldrà guardar la IP!!!!!!!!!!!!!!!!!!!!!!
$sql = "SELECT MtrDescr,MtrControl FROM Mtr_info WHERE idMtr=$MTR_idMtr;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - code Common01: $sql.");
    $MTR_status = "Error-Common01";
    return;
}
if($APP_BdD->FetchRs()){
    $MTR_MtrDescr = $APP_BdD->GetField(1);
    $MTR_MtrControl = $APP_BdD->GetField(2);
    $MTR_ok = true;
}
else{
    $MTR_ok = false;
    $MTR_status = "Error-Common02";// Motor not found";
}
$APP_BdD->CloseRs();



function rndm32($len) {
    $base32_table = array("A","B","C","D","E","F","G","H","J","K","L","M","N","P","Q","R","S","T","U","V","W","X","Y","Z","2","3","4","5","6","7","8","9");
    $out = "";
    for($i=0;$i<$len;$i++){
        $out.= $base32_table[rand(0,31)];
    }
    return $out;
}

function fesLog($text) 
{
    if(filesize("logMtr.dat") > 5000000){
        rename( "logMtr.dat" , "logMtr.".rndm32(3).".bak" );    
        $fh = fopen("logMtr.dat", 'w');
    }
    else $fh = fopen("logMtr.dat", 'a');
    
//20151221    fwrite($fh, $text."\r");
    
    fwrite($fh, date("Ymdhis")."-".$text."\r");//20151221
    fclose($fh);
}
//function send($metode,$data) 
//{
//$url = 'https://wwww.myphotocode.com/sincroMotor/$metode';
////$data = array('key1' => 'value1', 'key2' => 'value2');
//
//// use key 'http' even if you send the request to https://...
//$options = array(
//    'http' => array(
//        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
//        'method'  => 'POST',
//        'content' => http_build_query($data),
//    ),
//);
//$context  = stream_context_create($options);
//$result = file_get_contents($url, false, $context);    
//    
//return $result;
//}

?>
