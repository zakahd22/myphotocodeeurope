<?php
require_once '../../../common/global.php';
include_once G_PATH . 'common/general.php';

$id = $_REQUEST['id'];



if($_REQUEST['form_id'] == "email"){
    $email = $_REQUEST['email'];
    if(eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email)) {
        $photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$id'"));
        
        $event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$photo[event_id]"));
        $hashtags = $event['hashtag'];
        $hashtags = str_replace(" ", "" , $hashtags);
        $hashtags2 = explode("#", $hashtags);      
        $eventTitle = $event['title'];
        $photoDir = "events/" . $event['start_date'] . $event['id'];
        $name = "";
        $eventID = $event['id'];
        $hashtags3 = "";
        
        if ($xxx = mysql_fetch_array(mysql_query("SELECT text FROM CLD_emailsText WHERE type=0 AND event=$eventID"))) {
            $subject = $xxx['text'];
        }
        else{
            $subject = "Hey, take a look at this #PoV# that I took at a DC Photobooth.";
        }

        if ($xxx = mysql_fetch_array(mysql_query("SELECT text FROM CLD_emailsText WHERE type=1 AND event=$eventID"))) {
            $text1 = $xxx['text'];
        }
        else{
            $text1 = "Check this out!  I took this #PoV# at a DC Photobooth";
        }

        if ($xxx = mysql_fetch_array(mysql_query("SELECT text FROM CLD_emailsText WHERE type=2 AND event=$eventID"))) {
            $text2 = $xxx['text'];
        } 
        else{
            $text2 = "Come visit our DC Photobooth.";
        }

        if (isset($_REQUEST['video2'])) {
            if(isset($_REQUEST['v3d'])){
                if (file_exists(G_PATH . $photoDir . "/" . $id . "-3D.mp4")) {
                    $imgUrl = G_PATH . $photoDir . "/" . $id . "-3D.mp4";
                    $name = $id . "-3D.mp4";
                    $xx = "YourVideo-3D.mp4";
                }
            }
            else{
                if (file_exists("../../../" . $photoDir . "/" . $id . ".mp4")) {
                    $imgUrl = G_PATH . $photoDir . "/" . $id . ".mp4";
                    $name = $id . ".mp4";
                    $xx = "YourVideo.mp4";
                }
                if (file_exists("../../../" . $photoDir . "/" . $id . ".wmv")) {
                    $imgUrl = G_PATH . $photoDir . "/" . $id . ".wmv";
                    $name = $id . ".wmv";
                    $xx = "YourVideo.wmv";
                }
            }
            $subject = str_replace("#PoV#", "video", $subject);
            $text1 = str_replace("#PoV#", "video", $text1);
            $text2 = str_replace("#PoV#", "video", $text2);
            $image = "";
        }
        else{
            if (isset($_REQUEST['D3'])) {
                $imgUrl = G_PATH . $photoDir . "/". $id ."-T3D.gif";
                $name = $id ."-3D/". $id ."-T3D.gif";
                $xx = "Your3DPhoto.gif";
            } 
            else {
                if (isset($_REQUEST['gif'])) {
                    $imgUrl = G_PATH . $photoDir . "/" . $id . "GIF.gif";
                    $name = $id . "GIF.gif";
                    $xx = "YourPhoto.gif";
                } 
                else {
                    $imgUrl = G_PATH . $photoDir . "/" . $id . ".jpg";
                    $name = $id . ".jpg";
                    $xx = "YourPhoto.jpg";
                }
            }

            $subject = str_replace("#PoV#", "photo", $subject);
            $text1 = str_replace("#PoV#", "photo", $text1);
            $text2 = str_replace("#PoV#", "photo", $text2);
            $yy = 1;
            
            while($yy<  sizeof($hashtags2)){
                $hashtags3 .= "<a href='https://facebook.com//hashtag/".$hashtags2[$yy]."'>#".$hashtags2[$yy]."</a> ";
                $yy++;
            }
            
            $image = "<img src='$photoDir/$name'>";
        }
        $to = $email;
        $to_str = "Dear user";
        
//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
        $mail_retMsg = "";
        $mail_ret = 0;
        ob_start();
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        
        
        require_once(G_PATH . 'common/mail.php');

        $mail = new mail();
        $mail->addAdress($to, $to_str);
        $mail->setSubject($subject);
        
        $mail->setTemplate(G_PATH . "common/resources/templates/html/en/photo_send.html");
        $mail->addTemplateField("#text1#", $text1);
        $mail->addTemplateField("#text2#", $text2);
        $mail->addTemplateField("#hastags#", $hashtags3);
        
        $mail->addTemplateField("#urlFile#", $image);
            
        $mail->addTemplateField("#nameOfLocation#", ($nameOfLocation != ""?'at '. $nameOfLocation:""));
        
        $mail->applyTempplateFields();
        
        $mail->addAttachment($imgUrl);
        if(!$mail->send()){
             $mail_ret = 0;
             utils::log($mail->retMsg, "logMail", "popup_email_form");
         }

//        require_once('../../../includes/classes/class.phpmailer.php');
        
//        $mail = new PHPMailer();
//        $mail->CharSet = "utf-8";
////	$mail->PluginDir = "common/";
//        $mail->PluginDir = "";
//        $mail->IsSMTP(); // telling the class to use SMTP
//        $mail->Host = $host;
//        $mail->SMTPAuth = true;   // enable SMTP authentication
//        $mail->Port = 25; // set the SMTP port for the GMAIL server
//        $mail->Username = $username; // SMTP account username
//        $mail->Password = $password; // SMTP account password
//        $mail->SetFrom($from, $from_str);
//        $mail->Timeout = 30;
//        $mail->ClearReplyTos();
//        $mail->Subject = $subject . "";
//        $mail->AddAddress($to, $to_str);
//        $mail->AltBody = "To view the message, please use an HTML compatible email viewer!";
//        $mail->MsgHTML($message);
//        $mail->AddAttachment($imgUrl, $xx);
//        if (!$mail->Send()){
//            $mail_ret = 0;
//        } 
//        else {
//            $mail_ret = 1;
//        }

        /*     $random_hash = md5(date('r', time()));

          $headers = "From: MyPhotoCode.com <noreply@myphotocode.com>\r\nReply-To: MyPhotoCode.com <noreply@myphotocode.com>";

          $headers .= "\r\nContent-Type: multipart/mixed; boundary=\"PHP-mixed-" . $random_hash . "\"";

          $attachment = chunk_split(base64_encode(file_get_contents($imgUrl)));

          $output = "
          --PHP-mixed-$random_hash;
          Content-Type: multipart/alternative; boundary=\"PHP-alt-$random_hash\"
          --PHP-alt-$random_hash
          Content-Type: text/plain; charset=\"utf-8\"
          Content-Transfer-Encoding: 7bit

          MyPhotoCode.com
          Here's your photo, thank you!

          --PHP-alt-$random_hash
          Content-Type: text/html; charset=\"utf-8\"
          Content-Transfer-Encoding: 7bit

          <h2>MyPhotoCode.com</h2>
          <p>" . $subject . "</p>

          --PHP-alt-$random_hash--

          --PHP-mixed-$random_hash
          Content-Type: application/zip; name=" . $name . "
          Content-Transfer-Encoding: base64
          Content-Disposition: attachment

          $attachment
          --PHP-mixed-$random_hash--";

          @mail($to, $subject, $output, $headers); */

        //if (mysql_num_rows(mysql_query("SELECT * FROM registre_emails WHERE email='$email'")) == 0)
        //{
        mysql_query("INSERT registre_emails set email='$email', code='$id'");
        //}
        if (isset($_REQUEST['video2'])) {
            $error = "The video has been sent, thank you!";
            $typeInfo = 7;
        } else {
            $error = "The photo has been sent, thank you!";
            $typeInfo = 4;
        }

        $client = @$_SERVER['HTTP_CLIENT_IP'];
        $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
        $remote = $_SERVER['REMOTE_ADDR'];
        $result = "Unknown";
        if (filter_var($client, FILTER_VALIDATE_IP)) {
            $ip = $client;
        } 
        elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
            $ip = $forward;
        } 
        else {
            $ip = $remote;
        }

        $ip_data = @json_decode(file_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));
        $country = "-";
        $city = "-";
        $state = "-";
        if ($ip_data && $ip_data->geoplugin_countryName != null) {
            $continentCode = $ip_data->geoplugin_continentCode;
            $countryCode = $ip_data->geoplugin_countryCode;
            $country = $ip_data->geoplugin_countryName;
            $city = $ip_data->geoplugin_city;
            $state = $ip_data->geoplugin_region;
        }
        $DDD = date("Y-m-d H:i:s");
        mysql_query("INSERT INTO CLD_estadistiques_photos(photo ,data , type_info , ip , country , state , city) VALUES('$id' ,'$DDD' , $typeInfo , '$ip' , '$country' , '$state' , '$city')");
    } 
    else {
        $error = "Invalid email address, try again.";
    }
}

if(isset($error)) $div_error = "<div class='error'>" . $error . "</div>";
 
if(isset($_REQUEST['video']) || isset($_REQUEST['video2'])){
    $video_input = "<input type='hidden' name='video2' value='1'>";
}

//echo <<<HTML
//    <DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
//    <html xmlns="https://www.w3.org/1999/xhtml" lang="en">
//    <head>
//        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
//        <link rel="stylesheet" type="text/css" media="screen, projection" href="../../../assets/css/style.css" />
//    </head>
//    <body style="background:none;overflow:hidden;">
//        <div id="inputCode" style="margin:0px;overflow:hidden;">
//            <div id="top1">
//                <img src="../../../assets/images/blank.gif" width="1" height="1" /></div>
//            <div id="body1">
//                <form method="post" action="{$PHP_SELF}">
//                    {$div_error}
//                    <div class="title"><img src="../../../assets/images/txt-insertyouremail.png"></div>
//                    <div class="textfield"><input type="text" name="email" /></div>
//                    <div class="button"><input type="image" alt="Submit!" src="../../../assets/images/button-submit.png" width="220" height="63" /></div>
//                    <div style='margin-top:20px;font-style:italic;font-size:13px;'>
//                        <a href='email-legal-notice.php' target='_blank' style='color:#b8365c;'>
//                            Legal notice
//                        </a>
//                    </div>
//                    <input type="hidden" name="form_id" value="email">
//                    {$video_input}
//                </form>								
//            </div>
//            <div id="bottom1"><img src="../../../assets/images/blank.gif" width="1" height="1" /></div>
//        </div>
//    </body>
//    </html>
//HTML;
echo <<<HTML
    <DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
    <html xmlns="https://www.w3.org/1999/xhtml" lang="en">
    <head>
        <meta http-equiv="content-type" content="text/html; charset=utf-8" />
        <link href="../../../assets/css/popupV2.css" rel="stylesheet" type="text/css">
    </head>
    <body>
    <form method="post" action="{$PHP_SELF}">
        {$div_error}
        <input type="text" class="popup-input-large" name="email" />
        <div class="button popup-center popup-margin-top"><input type="button" class='popup-confirm' value="Submit" /></div>
        <input type="hidden" name="form_id" value="email">
        {$video_input}
    </form>
    <div class='popup-center' style='font-style:italic;font-size:13px;'>
        <a href='email-legal-notice.php' target='_blank' style='color:#b8365c;'>Legal notice</a>
    </div>
    </body>
    </html>
HTML;
