<?php
//$APP_register = true;//20121026
//
//require("common.php");
//if(!$APP_user) return;

//l'usuari no recorda les dades d'accès

header('Content-Type:text/xml; charset=UTF-8');
require("../common/APP_BdD.php");
$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?"."><return>";
$APP_xmlOKcomm = "<comm_status>OK</comm_status>";

if(isset($_REQUEST['email'])){
    $email = $_REQUEST['email'];

}
else{
    echo "$APP_xml<comm_status>Error - No email data</comm_status></return>";
    return;
}

//busquem usuaris amb aquest email
$sql = "SELECT username, password FROM Appusr_user WHERE email='$email'; ";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}

$mail_email = $email;
$nUsers = 0;
$mail_cont = "<h1>Forgot your password?</h1><p>You requested to remember your password. Below is the login information:</p><table>";
while($APP_BdD->FetchRs()){
    $tmp = $APP_BdD->GetField(1);
    $mail_cont.= "<tr><td>Username:</td><td><b>$tmp</b></td>";
    $tmp = $APP_BdD->GetField(2);
    $mail_cont.= "<td>Password:</td><td><b>$tmp</b></td></tr>";
    
    $nUsers++;
}

if(!$nUsers){
echo "$APP_xml<comm_status>Error - No user found</comm_status></return>";
return;
}



$mail_cont.= "</table>";

include("../common/APP_mail.php");

if(!$mail_ret){
    echo "$APP_xml<comm_status>Error Mail not sent: $mail_retMsg</comm_status></return>";
    return;
    
}


$APP_xml.= "<email_status>OK</email_status>";
//$APP_xml.= "<email_msg>$mail_retMsg</email_msg>";//??
echo "$APP_xml$APP_xmlOKcomm</return>"; // de moment no fem res més



?>
