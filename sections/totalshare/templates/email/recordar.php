<?php
//require_once "../../../../common/global.php";
//require_once '../../../../common/conexio.php';
$to = $entry['contact'];
$code = $entry['code'];

//select subjecte modificat
$CLD_CON->openRS("SELECT `text` FROM CLD_emailsText, photos WHERE `type` = 0 AND `event` = photos.`event_id` AND photos.`code` = '$code'");
while ($CLD_CON->FetchArray()) {
    $subject = $CLD_CON->GetArrayField("text");
   
}
if($subject == ""){
    $subject = "Hey, take a look at this photo that I took at a DC Photobooth.";
}
//select text1 modificat
$CLD_CON->openRS("SELECT `text` FROM CLD_emailsText, photos WHERE `type` = 1 AND `event` = photos.`event_id` AND photos.`code` = '$code'");
while ($CLD_CON->FetchArray()) {
    $txt1 = $CLD_CON->GetArrayField("text");
   
}
if($txt1 == ""){
    $txt1 = "Check this out!  I took this photo at a DC Photobooth.";
}
//select text2 modificat
$CLD_CON->openRS("SELECT `text` FROM CLD_emailsText, photos WHERE `type` = 2 AND `event` = photos.`event_id` AND photos.`code` = '$code'");
while ($CLD_CON->FetchArray()) {
    $txt2 = $CLD_CON->GetArrayField("text");
   
}
if($txt2 == ""){
    $txt2 = "Come visit our DC Photobooth.";
}


// echo $subject."<br>";
// echo $txt1."<br>";
// echo "www.myphotocode.com/index.php?code=$code&method=email<br>";
// echo $txt2."<br>";

$message = <<< HTML
    <html>
    <head>
    </head>
    <body>
        <p style='font-size:20px;font-weight:bold;'>$txt1</p>
        <p style='font-size:18px;'>Click on the link to see your photo <a href="https://www.myphotocode.com/index.php/$code/email" target="_blank">$code</a></p>
        <p style='font-size:18px;'>$txt2</p> 
        <p style='font-size:12px;'>
        <br/>
            You received this email because someone used a DC Photobooth and typed your email address to share the Photo. If you are not the intended recipient, please delete the email and images immediately, and contact main@dc-image.com to notify the error. If you need any support, please contact the DC Team at main@dc-image.com<br/>
            Digital Centre &copy; All Rights reserved<br/><br/>
            <b>DISCLAIMER:</b><br/>
            The information contained in this electronic message is privileged and/or confidential and is intended only for the use of the individual or entity named above. If you are not the intended recipient, or if you are responsible for delivering it to the intended recipient, you are hereby notified that any dissemination, distribution or copying of the communication is not authorized, allowed or intended by the sender. If you have received this communication in error, please immediately notify us by telephone at the above number and forward the original message to the sender above. Thank you.
        </p>

    </body>
</html>
HTML;
        
$statesuccess="6";
$statefailure="5";
$error = "No s'ha pogut enviar, esperant retry";