<?php

header('Content-Type:text/xml; charset=UTF-8');
require("../common/APP_BdD.php");

$APP_xml = "<?xml version=\"1.0\" encoding=\"utf-8\" ?" . "><return>";
$APP_xmlOKcomm = "<comm_status>OK</comm_status>";



if (isset($_REQUEST['email'])) {
    $email = $_REQUEST['email'];
} else {
    echo "$APP_xml<comm_status>Error - No email data</comm_status></return>";
    return;
}

//busquem usuaris amb aquest email `App_email` FROM rentals
$sql = "SELECT username, password FROM rentals WHERE App_email='$email'; ";

$esOK = $APP_BdD->OpenRs($sql);
if (!$esOK) {
//caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}

$mail_email = $email;
$mail_subject = "PhotoBooth APP - Forgot your password?";
$nUsers = 0;
$mail_cont = "<p>Forgot your password? Don't worry we give you again your username & password to login into the PhotoBooth APP.</p><table>";
while ($APP_BdD->FetchRs()) {
    $tmp = $APP_BdD->GetField(1);
    $mail_cont .= "<tr><td>Username:</td><td><b>$tmp</b></td>";
    $tmp = $APP_BdD->GetField(2);
    $mail_cont .= "<td>Password:</td><td><b>$tmp</b></td></tr>";

    $nUsers++;
}
$mail_cont .= "</table>";
if (!$nUsers) {
    echo "$APP_xml<comm_status>Error - No user found</comm_status></return>";
    return;
}



$mail_cont .= "</table>";
$mail_cont .= "<p>Remember, you can use this username &amp; password to have access on  MyPhotoCode.com, where you can customize your Cloud, your events, your paypal  payment, etc.</p>
<p>Click on this link to log in on DC's world and have direct access wherever  you want.<br />
<a href='http://www.digital-centre.com/global/'>http://www.digital-centre.com/global/</a></p>
<p>Best Regards,</p>
<p>Digital Centre – www.dc-image.com </p>
<p>&nbsp;</p>
";

$mail_nomremitent = "DC PhotoBooth APP";
$mail_replayto = "main@dc-image.com";

include("../common/APP_mail.php");

if (!$mail_ret) {
    echo "$APP_xml<comm_status>Error Mail not sent</comm_status></return>";
    return;
}



$APP_xml .= "<email_status>OK</email_status>";
echo "$APP_xml$APP_xmlOKcomm</return>";
?>
