<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$ID = $_POST['id'];
$p = $_POST['p'];
$p1 = $_POST['p1'];
$CLD_CON2= clone($CLD_CON);

$CLD_CON->OpenRs("SELECT * FROM rentals WHERE id=$ID AND password='$p'");
if ($CLD_CON->FetchArray()) {
    if($CLD_CON->Execute("UPDATE rentals SET password ='$p1' WHERE id=$ID")){
        if($CLD_CON->Execute("UPDATE CLD_Login SET password ='$p1' WHERE id_user=$ID AND userType=4")){
            
// $sg         = $_GET["p1"];
// $tact       = $_GET["p2"];
// $function   = $_GET["p3"];
//        $mypc_id_owner  = $_GET["p4"];
//        $password       = $_GET["p5"];
//$sg_local = strtoupper(sha1($tact . $function . $_GET["p4"] . $_GET["p5"]. $_GET["p6"]. G_MYPC_KEY));   
    
    $function = "ChgOwnPasswd";
    $date     = date("YmdHis");
    $sg = strtoupper(sha1($date . $function . $ID . $p1 . "ARt32qX"));
    $data = "p1=$sg&p2=$date&p3=$function&p4=$ID&p5=".urlencode($p1);
    
    $res = utils::motor_conect("mypc_service.php", $data);
    utils::log("mypc_service  res: $res", G_PATH . "log/logCTRLConnect", "saveOwnerPassword");
    
    $p = stripos($res, '{');
    utils::log("mypc_service  { in res: $p. ", G_PATH . "log/logCTRLConnect", "saveOwnerPassword");
    if($p == false){
        $response = json_decode($res);
    }
    elseif($p > 4){
        $response = json_decode($res);
        
    }
    else {
        utils::log("mypc_service  new res: ".substr($res,$p), G_PATH . "log/logCTRLConnect", "saveOwnerPassword");
        $response = json_decode(substr($res,$p));
    }
    utils::log("mypc_service  response:", G_PATH . "log/logCTRLConnect", "saveOwnerPassword");
    utils::log($response, G_PATH . "log/logCTRLConnect", "saveOwnerPassword");
    //20150322 FINAL
            
            
            echo "OK";
        }
    }
}else{
    echo "The Actual password is not correct";
}
?>
