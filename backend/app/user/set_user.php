<?php
$APP_register = true;
require("common.php");


$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><return>";
$APP_xmlOKcomm = "<comm_status>OK</comm_status>";
$sql = "";
if(isset($_REQUEST['username'])){
//    $user = $_REQUEST['username'];
    $user = str_replace("'","",$_REQUEST['username']);
    
//20121019 INICI mirem si existeix    
    $sql = "SELECT id FROM Appusr_user WHERE username='$user'; ";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<comm_status>Error - Database error code: 0001</comm_status></return>";
        return;
    }
    if($APP_BdD->FetchRs()){
        $APP_BdD->CloseRs();
        echo "$APP_xml<comm_status>Error - Username not available</comm_status></return>";
        return;
    }
    $APP_BdD->CloseRs();
    
    
//20121019 FINAL mirem si existeix    
    
    
    if(isset($_REQUEST['password'])){    
//        $psw = $_REQUEST['password'];
        $psw = str_replace("'","",$_REQUEST['password']);
        $insertMail = "";
        if(isset($_REQUEST['e-mail'])){
            //str_replace("'","",$_REQUEST['e-mail'])
            $insertMail = ", email='".str_replace("'","",$_REQUEST['e-mail'])."'";
        }
        
//20140331tonto        $codi = rndm32(12);//?????  $codi = APP_QRdo($APP_userId);
        
        
//20131028        $sql = "INSERT INTO Appusr_user SET username='$user', password='$psw' $insertMail, qrcode='$codi' ";
        $sql = "INSERT INTO Appusr_user SET username='$user', password='$psw' $insertMail ";//20131028
        $APP_userId = $APP_BdD->ExecuteInsert($sql);
        if(!$APP_userId){
            echo "$APP_xml<comm_status>Error - Database error code: 0002</comm_status></return>";
            return;
        }
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

//hauria de crear el qr !!!!!!
//20131028 INICI
//20131028    APP_QRfile($APP_userId,$codi);
    
$codi = APP_QRdo($APP_userId);

$sql = "UPDATE `Appusr_user` SET qrcode='$codi' WHERE id=$APP_userId;"; 
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
    return;
}
//20131028 FINAL



//20121027 i volen una imatge per defecte
// però com aquesta és png i les imatges d'usuari seran jpg millor no ho faig aqui        $nomFitxer = "userimage/img$APP_userId.jpg";
//hauré de modificar get_profile...  comprovant si existeix la imetge d'usuari i, si no, enviar aquesta        


echo "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més
?>
