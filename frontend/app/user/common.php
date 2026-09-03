<?php //common d'users, sempre hi haurà username i password, no cal guardar res a la session       session_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type:text/xml; charset=UTF-8');
require("../common/APP_BdD.php");


require("../common/APP_common.php");//20121019 abans el teniem mes abaix

////20131025 log user agent INICI 
//// a eliminar
//APP_fesLog("User agent:");
//APP_fesLog($_SERVER['HTTP_USER_AGENT']);
////20131025 log user agent INICI 





if($APP_register) return;

//Codis d'error
$APPERROR_noid = "Error code:001 missing id";//
$APPERROR_noemail = "Error code:002 missing eMail";//


//
//$APP_estatsBooth = array('OK','Error');
//$APP_estatsAlert = array('Active','Active','Solved');


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1

// checkUser(){
$APP_userOK = false;
$APP_userId = "";
$APP_user = "";
$APP_userEmail = "";
$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><return>";

$APP_userQR = "";//20131028

if($APP_BdD_error){
    echo "$APP_xml<comm_status>Error - Database access: $APP_BdD_error</comm_status></return>";
    return;
    
}

$APP_xmlOKcomm = "<comm_status>OK</comm_status>";
$sql = "";
if(isset($_REQUEST['username'])){
//    $user = $_REQUEST['username'];
    $user = str_replace("'","",$_REQUEST['username']);
    if(isset($_REQUEST['password'])){
//        $psw = $_REQUEST['password'];
        $psw = str_replace("'","",$_REQUEST['password']);
//20131028        $sql = "SELECT id,email FROM Appusr_user WHERE username='$user' AND password='$psw'; ";
        $sql = "SELECT id,email,qrcode FROM Appusr_user WHERE username='$user' AND password='$psw'; ";//20131028
        
        
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<comm_status>Error - Database error code: 0000</comm_status></return>";
        return;
        }
        if($APP_BdD->FetchRs()){
            $APP_userId =  $APP_BdD->GetField(1);
            $APP_userEmail =  $APP_BdD->GetField(2); 
            $APP_userQR =  $APP_BdD->GetField(3);//20131028
            $APP_user =  $user;
            $APP_userOK = true;
        }
        $APP_BdD->CloseRs();
    }
    else{
        echo "$APP_xml<comm_status>Error - Invalid username or password (no psw)</comm_status></return>";
        return;
        
    }
}
else{
    //20121023 INICI
    if($APP_open){
        $APP_user = "none";
        $APP_userId = 1;//és un usuari especial
        return;
    }
    //20121023 FINAL
    echo "$APP_xml<comm_status>Error - Invalid username or password (no usr)</comm_status></return>";
    return;

}

if(!$APP_user){
    //20121023 INICI
    if($APP_open){
        $APP_user = "none";
        $APP_userId = 1;
        return;
    }
    //20121023 FINAL
    echo "$APP_xml<comm_status>Error - Invalid username or password</comm_status></return>";
    return;

}

//20121019 cal incloure'l més amunt    require("../common/APP_common.php");

function APP_checkBoothAlerts($idBooth,$APP_BdD){
$sql = "SELECT * FROM `App_boothAlert` WHERE `idBooth`=$idBooth AND `estat`<2; ";

echo "TRACE 01 $sql";
return 2;

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error $sql";
return;
}
$estat = 0;
if($APP_BdD->FetchRs()){
    $estat = 1;
}    
$APP_BdD->CloseRs();

//modifiquuem estat
$sql = "UPDATE  `App_booths` SET `estat`=$estat WHERE `idBooth`=$idBooth; ";
$esOK = $APP_BdD->Execute($sql); if(!$esOK){ echo "Error $sql";  }


return $estat;
}

?>
