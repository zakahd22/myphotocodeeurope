<?php

$esLogin = true;
include "EVUPcommon.php";

$EVUPidOk = false;
$EVUPid = 0;

if (isset($_REQUEST['usr'])){ $username = $_REQUEST['usr'];} else{   echo "ko#1"; return; }
if (isset($_REQUEST['psw'])){ $password = $_REQUEST['psw'];} else{   echo "ko#2"; return; }


$sql = "SELECT id FROM rentals where username='$username' AND password='$password'";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){echo "ko#3"; return; }

if($APP_BdD->FetchRs()){
    $EVUPid =  $APP_BdD->GetField(1);
    $EVUPidOk = true;
    
}
$APP_BdD->CloseRs();

if(!$EVUPidOk) echo "ko#4";
else{
    $_SESSION['EVUPid'] = $EVUPid;
    echo "ok";
}

?>
