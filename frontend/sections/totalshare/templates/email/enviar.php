<?php
//require_once "../../../../common/global.php";
//require_once '../../../../common/conexio.php';
$to = $contact;
$code = $code;
$textClick = "";
$consulta = "SELECT `serialnumber`, `code`, `location` FROM App_booths, gestor WHERE gestor.`idBooth` = App_booths.`idBooth` AND gestor.`code`='$code'";

$CLD_CON->OpenRs($consulta);
    while ($CLD_CON->FetchArray()) {
        $SNumber = $CLD_CON->GetArrayField("serialnumber");
        $pblocation = $CLD_CON->GetArrayField("location");
    }

//select subjecte modificat
$CLD_CON->openRS("SELECT `text` FROM CLD_emailsText, photos WHERE `type` = 0 AND `event` = photos.`event_id` AND photos.`code` = '$code'");
while ($CLD_CON->FetchArray()) {
    $subject = $CLD_CON->GetArrayField("text");
   
}


$consulta2 = "SELECT gestor.`code`, CLD_boothTypes.`id`, CLD_boothTypes.`name` FROM gestor, App_booths, CLD_boothTypes WHERE gestor.`code` = '$code' AND gestor.`idBooth` = App_booths.`idBooth` AND CLD_boothTypes.`id` = App_booths.`CLD_idType`";
    $CLD_CON->OpenRs($consulta2);
    while ($CLD_CON->FetchArray()) {
            $pbtype = $CLD_CON->GetArrayField("name");
            $pblink = $CLD_CON->GetArrayField("id");
    }

$consulta3 = "SELECT gestor.`code`, App_boothDongle.`idBooth`, App_boothDongle.`idDongle` booths,`rand_string` FROM gestor, App_booths, App_boothDongle, booths WHERE gestor.`code` = '$code' AND gestor.`idBooth` = App_booths.`idBooth` AND App_boothDongle.`idBooth` = App_booths.`idBooth` AND App_boothDongle.`idDongle` = booths.`id`";
    $CLD_CON->OpenRs($consulta3);
    while ($CLD_CON->FetchArray()) {
        $dongle = $CLD_CON->GetArrayField("rand_string");
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
    $textClick = "Click on the link to see your photo";
}
//select text2 modificat
$CLD_CON->openRS("SELECT `text` FROM CLD_emailsText, photos WHERE `type` = 2 AND `event` = photos.`event_id` AND photos.`code` = '$code'");
while ($CLD_CON->FetchArray()) {
    $txt2 = $CLD_CON->GetArrayField("text");
   
}
if($txt2 == ""){
    $txt2 = "Come visit our DC Photobooth.";
}


$subject = str_replace("#PoV#", "photo", $subject);
$txt1 = str_replace("#PoV#", "photo", $txt1);
$txt2 = str_replace("#PoV#", "photo", $txt2);
// echo $subject."<br>";
// echo $txt1."<br>";
// echo "www.myphotocode.com/index.php?code=$code&method=email&v=3<br>";
// echo $txt2."<br>";

            if($pblink == '1'){
            $PBVariable = "<a href='https://www.digital-centre.com/photobooths/buy-a-photobooth-for-rental/strip/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='6'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/panther/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='22'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/buy-a-photobooth-for-rental/mini-i-go/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='34'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/buy-a-photobooth-for-rental/nexus-2/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='35'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/eclipse/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
             elseif($pblink =='36'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/phanter-revolution/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
              elseif($pblink =='40'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/dlight/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
               elseif($pblink =='41'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/duplo/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
               elseif($pblink =='42'){
               $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/panther-cube/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
              elseif($pblink =='44'){
                $PBVariable = "<a href='https://www.digital-centre.com/surfingmalibu/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='45'){
                $PBVariable = "<a href='https://www.digital-centre.com/selfie-photo-mask/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
              elseif($pblink =='47'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/letsprint/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
              elseif($pblink =='48'){
                $PBVariable = "<a href='https://www.digital-centre.com/photobooths/coin-op-industry-photobooths/smilenstick/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            elseif($pblink =='48'){
                $PBVariable = "<a href='https://www.digital-centre.com/safepanther/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }
            else{
               $PBVariable = "<a href='https://www.digital-centre.com/photobooths/' target='_blank'>$pbtype</a> / $pblocation</p>";
            }


$message = <<< HTML
    <html>
    <head>
    </head>
    <body>
        <p style='font-size:20px;font-weight:bold;'>$txt1</p>
        <p style='font-size:18px;'>$textClick <a href="https://www.myphotocode.com/index.php?code=$code&method=sms&v=4" target="_blank">https://www.myphotocode.com/index.php?code=$code&method=sms&v=4</a></p>
        <p style='font-size:18px;'>Photobooth: $PBVariable</p>
        <tr>
            <td colspan="7">
                <table align="center" border="0" style="background-color:#ffffff;" width="800">
                <tbody>
                    <tr align="center">
                        <td align="center" bgcolor="#ffffff" colspan="3" scope="col" width="100%"><a href="https://www.digital-centre.com/welcome/share-secure.html" target="_blank"><img alt="share-secure" border="none" height="40" src="https://www.digital-centre.com/newsletter/images/buttons/button-share-secure3(1).png" title="Share Secure" width="109" /></a> <a href="https://www.digital-centre.com/qr-photo.html" target="_blank"><img alt="qr-photo" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/QRphotoIcon(1).png" title="QR-Photo" width="40" /></a> <a href="https://www.facebook.com/digitalcentre" target="_blank"><img alt="facebook" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/facebook(1).png" title="Facebook" width="40" /></a> <a href="https://twitter.com/photobooth_DC" target="_blank"><img alt="twitter" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/twitter(1).png" title="Twitter" width="40" /></a> <a href="https://youtube.com/digitalcentrepb" target="_blank"><img alt="youtube" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/youtube(1).png" title="YouTube" width="40" /></a> <a href="https://instagram.com/digital_centre" target="_blank"><img alt="instagram" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/instagram(1).png" title="Instagram" width="40" /></a> <a href="https://photoboothparts.com/" target="_blank"><img alt="photoboothparts.com" border="none" height="40" src="https://www.digital-centre.com/newsletters/images/0interest/pbpLong(1).png" title="Online Store" width="77" /></a>
                        <hr style="background-color:#57c66;border-color:#57c66;" /></td>
                    </tr>
                </tbody>
            </table>
            </td>
        </tr>
        <br/>
            You received this email because someone used a DC Photobooth and typed your email address to share the Photo. If you are not the intended recipient, please delete the email and images immediately, and contact main@dc-image.com to notify the error. If you need any support, please contact the DC Team at main@dc-image.com<br/>
            Digital Centre &copy; All Rights reserved<br/><br/>

            
            <b>DISCLAIMER:</b><br/>
            The information contained in this electronic message is privileged and/or confidential and is intended only for the use of the individual or entity named above. If you are not the intended recipient, or if you are responsible for delivering it to the intended recipient, you are hereby notified that any dissemination, distribution or copying of the communication is not authorized, allowed or intended by the sender. If you have received this communication in error, please immediately notify us by telephone at the above number and forward the original message to the sender above. Thank you.
        </p>

        <p style='font-size:12px;'>$SNumber,OW$owner,STR$dongle,PB$idBooth</p> 
        <p style='font-size:12px;'>
        <br/>
            Vous avez re&ccedil;u cet e-mail parce que quelqu'un a utilis&eacute; un photomaton DC et a saisi votre adresse e-mail pour partager la photo. Si vous n'&ecirc;tes pas le destinataire pr&eacute;vu, veuillez supprimer imm&eacute;diatement l'e-mail et les images et contactez main@dc-image.com pour signaler l'erreur. Si vous avez besoin d'assistance, veuillez contacter l'&eacute;quipe DC &agrave; l'adresse main@dc-image.com<br/>
            Digital Centre &copy; TTous les droits sont r&eacute;serv&eacute;s<br/><br/>
            <b>AVERTISSEMENT:</b><br/>
            Les informations contenues dans ce message &eacute;lectronique sont privil&eacute;gi&eacute;es et/ou confidentielles et sont destin&eacute;es uniquement &agrave; l'usage de la personne ou de l'entit&eacute; nomm&eacute;e ci-dessus. Si vous n'&ecirc;tes pas le destinataire pr&eacute;vu, ou si vous &ecirc;tes responsable de la remettre au destinataire pr&eacute;vu, vous &ecirc;tes inform&eacute; par la pr&eacute;sente que toute diffusion, distribution ou copie de la communication n'est pas autoris&eacute;e, autoris&eacute;e ou pr&eacute;vue par l'exp&eacute;diteur. Si vous avez re&ccedil;u cette communication par erreur, veuillez nous en informer imm&eacute;diatement par t&eacute;l&eacute;phone au num&eacute;ro ci-dessus et transmettre le message original &agrave; l'exp&eacute;diteur ci-dessus. Merci.</p>

    </body>
</html>
HTML;
        
$statesuccess="6";
$statefailure="5";
$error = "No s'ha pogut enviar, esperant retry";
