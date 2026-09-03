<?php //common d'owners, sempre hi haurà username i password, no cal guardar res a la session       session_start();
error_reporting(0);
ini_set('display_errors', 0);

header('Content-Type:text/xml; charset=UTF-8');
require("../common/APP_BdD.php");

//Codis d'error
$APPERROR_noid = "Error code:001 missing id";//
$APPERROR_noemail = "Error code:002 missing eMail";//



//
$APP_estatsBooth = array('OK','Error');
$APP_estatsAlert = array('Active','Active','Solved');


//NOTA: ara és REQUEST, després hauran de ser POST

// checkUser(){
$APP_userOK = false;
$APP_userId = "";
$APP_user = "";
$APP_username = "";//20140329
$APP_user_email = "";//20130530
$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><return>";
$APP_xmlOKcomm = "<comm_status>OK</comm_status>";
$sql = "";
if(isset($_REQUEST['username'])){
    $user = $_REQUEST['username'];
    if(isset($_REQUEST['password'])){
        $psw = $_REQUEST['password'];
//20130530        $sql = "SELECT id,name FROM rentals WHERE username='$user' AND password='$psw'; ";
        $sql = "SELECT id,name,`App_email` FROM rentals WHERE username='$user' AND password='$psw'; ";//20130530
        
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<comm_status>Error - Database error code: 0001</comm_status></return>";
        return;
        }
//        echo "$APP_xml TRACE 02</return>";
//        return;
        if($APP_BdD->FetchRs()){
            $APP_userId =  $APP_BdD->GetField(1);
            $APP_user =  $APP_BdD->GetField(2);
            $APP_user_email =  $APP_BdD->GetField(3);//20130530
            $APP_username = $user;//20140329
            $APP_userOK = true;
 //           $APP_xml.= "<status>OK</status>";
        }
        $APP_BdD->CloseRs();
    }
    else{
        echo "$APP_xml<comm_status>Error - Invalid username or password (no psw)</comm_status></return>";
        return;
        
    }
}
else{
    echo "$APP_xml<comm_status>Error - Invalid username or password (no usr)</comm_status></return>";
    return;

}

if(!$APP_user){
    echo "$APP_xml<comm_status>Error - Invalid username or password</comm_status></return>";
    return;

}

require("../common/APP_common.php");

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


//SELECT `idOwner`, `preference`, `address`, `city`, `state`, `code`, `country`  FROM `App_ownerAddres` WHERE 1

//SELECT `idOwner`, `preference`, `address`, `city`, `state`, `code`, `country` FROM `App_ownerAddress` WHERE 1
//SELECT `idPack`, `label`, `descr`, `price`, `active` FROM `App_ordersPack` WHERE 1

//SELECT `id`, `when`, `email`, `estat` FROM `App_forgot` WHERE 1

?>
