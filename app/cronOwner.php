<?php

require("common/APP_BdD.php");


//de moment un email
$mail_email = "victor.carretero@treemes.com";
$mail_nom = "Jo mateix";
$mail_subject = "Missatge à";
$mail_cont = "<p>Prova de mail</p>";
include("common/APP_mail.php");

////SELECT `idBooth`, `estat`, `type`, `owner`, `name`, `obs`, `serialnumber`,
//// `location`, `latitude`, `longitude`, `alertOffline`, `hS`, `mS`, `hE`, `mE`, `report`, `cardReaderSN` FROM `App_booths` WHERE 1
//
//$sql = "SELECT idBooth,estat, `owner`, `hS`, `mS`, `hE`, `mE` FROM rentals WHERE username='$user' AND password='$psw'; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
////caldria controlar l'error
//echo "$APP_xml<comm_status>Error - Database error code: 0001</comm_status></return>";
//return;
//}
////        echo "$APP_xml TRACE 02</return>";
////        return;
//if($APP_BdD->FetchRs()){
//    $APP_userId =  $APP_BdD->GetField(1);
//    $APP_user =  $APP_BdD->GetField(2);
//    $APP_userOK = true;
////           $APP_xml.= "<status>OK</status>";
//}
//$APP_BdD->CloseRs();


?>
