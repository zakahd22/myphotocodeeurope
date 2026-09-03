<?php
require("common.php");

if(!$APP_user) return;


//Parámetros:
//- username (string)
//- password (string) sin ningún tipo de encriptación
//- text (string)
//- context (int) actualmente 1: news
//- id (int) news_id

//email a sales@dc-image.com

//20140329...
$infoContext = "";
if(isset($_REQUEST['context'])){
if($_REQUEST['context'] == "1"){
    if(isset($_REQUEST['id'])){
        $sql = "SELECT url FROM App_news WHERE id=".$_REQUEST['id'];
        $esOK = $APP_BdD->OpenRs($sql);
        if($esOK){
            if($APP_BdD->FetchRs()){
                $tmp =  $APP_BdD->GetField(1);
                if($tmp){
                    $infoContext = "<tr><td colspan='3'>Context:</td></tr>";
                    $infoContext.= "<tr><td>Interested in the news:</td><td colspan='2'><a href='$tmp'>$tmp</a></td></tr>";
                }

            }            
        }
    }
    //$idContext 
}
}

// un email
//20140329  $mail_email = "victor.carretero@treemes.com";//email a sales@dc-image.com
//20140329  $mail_nom = "APP Owners";
//20140329  $mail_subject = "Contact from APP Owners: $APP_user";
//$mail_cont = "<h1>We have a Contact from the APP</h1><table>";
//$mail_cont.= "<tr><td>User name:</td><td colspan='2'><b>$APP_user</b></td></tr>";
//$mail_cont.= "<tr><td>Email:</td><td colspan='2'><b>$APP_user_email</b></td></tr>";
//
//$mail_cont.= "<tr><td colspan='3'>Text:</td></tr>";
//$mail_cont.= "<tr><td colspan='3'><b>{$_REQUEST['text']}</b></td></tr>";
$mail_email = "APPowners@dc-image.com";//20140329
//$mail_nom = "APPowners";//20140329
$mail_subject = "Contact PhotoBooth APP";//20140329  

$mail_cont = "<h1>We have a Contact from the APP</h1><table>";
$mail_cont.= "<tr><td>Reply to:</td><td colspan='2'><b><a href='mailto:$APP_user_email'>$APP_user_email</a></b></td></tr>";
$mail_cont.= "<tr><td>Username:</td><td colspan='2'><b>$APP_username</b></td></tr>";

$mail_cont.= "<tr><td colspan='3'>Message:</td></tr>";
$mail_cont.= "<tr><td colspan='3'><b>{$_REQUEST['text']}</b></td></tr>";

//$mail_cont.= "<tr><td>Context:</td><td colspan='2'><b>{$_REQUEST['context']}</b></td></tr>";
//$mail_cont.= "<tr><td>Id context:</td><td colspan='2'><b>{$_REQUEST['id']}</b></td></tr>";
$mail_cont.= $infoContext;//20140329


$mail_cont.= "<tr><td colspan='3'>&nbsp;</td></tr>";


$mail_cont.= "</table>";

//$mail_remitent = "main@dc-image.com";
$mail_nomremitent = "DC PhotoBooth APP";
//$mail_hostremitent = "smtp.altecom.com";
//$mail_usrremitent = "main@dc-image.com";
//$mail_pswremitent = "d1g1t4lc3ntr3";
$mail_replayto = "main@dc-image.com";

include("../common/APP_mail.php");

if(!$mail_ret){
    echo "$APP_xml<comm_status>Error mail not sent - $mail_retMsg</comm_status></return>";
    return;
    
}

//20140329 INICI

$mail_email = $APP_user_email;
$mail_nom = $APP_user;
$mail_cont = "<p>Dear $APP_user,</p>";
$mail_cont.= "<p>Thank you for contacting Digital Centre. We value and appreciate your choice in selecting Digital Centre as your Photobooth supplier.  Someone from our company will be in touch with you as soon as possible.</p>";
$mail_cont.= "<p>Your message:</p><p>{$_REQUEST['text']}</p>";
$mail_cont.= "<p></p><p>Best Regards,</p><p>Digital Centre – www.digital-centre.com</p>";
include("../common/APP_mail.php");

//if(!$mail_ret){
//    echo "$APP_xml<comm_status>Error mail2 not sent - $mail_retMsg</comm_status></return>";
//    return;
//    
//}

//20140329 FINAL


echo "$APP_xml$APP_xmlOKcomm</return>"; // de moment no fem res més
?>
