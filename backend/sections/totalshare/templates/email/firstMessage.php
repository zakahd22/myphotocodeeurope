<?php

$to = $entry['contact'];

$subject = "Sorry, your picture is not Online yet";

$code = $entry['code'];
$message = <<< HTML
        
            <html>
    <head>
    </head>
    <body>
        
        <p style='font-size:20px;font-weight:bold;'>Here is your Photo https://www.myphotocode.com/index.php?code=$code&method=email. </p>
        <p style='font-size:18px;'>The photo is not available yet. Please stand by, we'll send it to you as soon as it is available</p>
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

$statesuccess = "1";
$statefailure = "3";
$error = "No s'ha pogut enviar el First Message";