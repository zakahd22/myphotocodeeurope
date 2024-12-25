<?php
require_once "../../../common/global.php";
require_once G_PATH . "assets/php/templates/src/facebook.php";
include G_PATH . 'common/general.php';

//INICI log
function logFace($text) 
{
    $fh = fopen("logFace.dat", 'a');
    fwrite($fh, $text."\r\n");
    fclose($fh);
}
//FINAL log

$photoCode = $_SESSION['photoCode'];
$_SESSION['nSts'] == 1;

if(isset($_SERVER)){
    $client = @$_SERVER['HTTP_CLIENT_IP'];
    $forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
    $remote = $_SERVER['REMOTE_ADDR'];
}
$result = "Unknown";
if (filter_var($client, FILTER_VALIDATE_IP)) {
    $ip = $client;
} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
    $ip = $forward;
} elseif(filter_var($remote, FILTER_VALIDATE_IP)) {
    $ip = $remote;
}
else{
    $ip=$result;
}

$ip_data = null;
//$ip_data = @json_decode(file_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));

if ($ip_data && $ip_data->geoplugin_countryName != null) {
    $continentCode = $ip_data->geoplugin_continentCode;
    $countryCode = $ip_data->geoplugin_countryCode;
    $country = $ip_data->geoplugin_countryName;
    $city = $ip_data->geoplugin_city;
    $state = $ip_data->geoplugin_region;
}

$photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$photoCode'"));
$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$photo[event_id]"));
$eTitle = $event['title'];
$photoUrl = G_PAGE . "events/" . $event['start_date'] . $event['id'] . "/" . $photoCode . ".jpg";

$DDD = date("Y-m-d H:i:s");
$typeInfo = 3;
?>

<DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "https://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

         <html xmlns="https://www.w3.org/1999/xhtml" lang="en">

        <head>

            <meta http-equiv="content-type" content="text/html; charset=utf-8" />
            <meta name="viewport" content="width=1080" />

            <link rel="shortcut icon" href="../../../favicon.ico" type="image/x-icon" />

            <title>MyPhotoCode</title>

            <link rel="stylesheet" type="text/css" media="screen, projection" href="../../../assets/css/style.css" />

            <link rel="stylesheet" type="text/css" media="screen, projection" href="../../../assets/css/popup.css" />

            <script type="text/javascript" src="../../../assets/js/jquery-1.5.1.min.js"></script>
            <script type="text/javascript" src="../../../assets/js/popup.js"></script>

        </head>

        <body>

            <div id="header"><div id="content">

                    <div style="position:absolute;top:14px;left:0px;"><a href="http://www.digital-centre.com" target="_blank"><img src="../../../assets/images/header-logo-digitalcentre.jpg" width="115" height="30" border="0" /></a></div>


                </div></div>

            <div id="wrap"><div id="inputCode">

                    <div id="top"><img src="../../../assets/images/blank.gif" width="1" height="1" /></div>
                    <div id="body">

                        <div style="margin-bottom:12px;"><img src="../../../assets/images/loading-bar.gif"></div>
                        <div><img src="../../../assets/images/txt-uploadingtofacebook.png"></div>

                    </div>
                    <div id="bottom"><img src="../../../assets/images/blank.gif" width="1" height="1" /></div>

                </div></div>
            <div id="popupWait"><img src='../../../assets/images/loading.gif' width="24" height="24" /></div>
            <div id="popup"></div>
            <div id="backgroundPopup"></div>

            <?php
//FB vars
            //$app_id = "162595210519645";
	    $app_id = "127533357397300";                        
	    //$app_secret = "2b9be3ff42e391ab3ab4ccfdc753c6f3";
	    $app_secret = "d0ef26cf75bb1f9fd4445f5818821a49";                        
            $post_login_url = G_PAGE . "assets/php/templates/facebook2.php";
            $album_name = 'MyPhotoCode';
            $album_description = 'Strip photos';


//Obtain the access_token with publish_stream permission 
            $code = $_REQUEST["code"];

            
            if (empty($code)) {
 //               $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" . $app_id . "&redirect_uri=" . urlencode($post_login_url);// . "&scope=publish_stream";
                $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" . $app_id . "&redirect_uri=" . urlencode($post_login_url) . "&scope=publish_actions";
                echo("<script>top.location.href='" . $dialog_url . "'</script>");
            } else {
                $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
                        . $app_id . "&redirect_uri=" . urlencode($post_login_url)
                        . "&client_secret=" . $app_secret
                        . "&code=" . $code;
                $response = file_get_contents($token_url);
                $p = null;
                parse_str($response, $p);
                $access_token = $p['access_token'];
                $graph_url = "https://graph.facebook.com/me/photos?"
//                $graph_url = "https://graph.facebook.com/me/fb_myphotocode:photos?"
                        . "access_token=" . $access_token;
                utils::log("Token " . $access_token, "logFace");
                $params = array();
                $params['url'] = $photoUrl; //"@" . realpath($uploadfile);
                utils::log("URL " . $photoUrl, "logFace");
 //20150730noMessage               $params['message'] = 'Look the photo I posted of the "' . $eTitle . '"  event!'  . $event['hashtag'];
                // Start the Graph API call
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $graph_url);

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                $result = curl_exec($ch);
                utils::log("Result " . $result, "logFace");
                $decoded = json_decode($result, true);
                utils::log("decoded " . $decoded, "logFace");
                curl_close($ch);
                utils::log("Closed", "logFace");
                if (is_array($decoded) && isset($decoded['id'])) {
                    /*
                      ?>
                      <script>
                      function closeWindow() {
                      window.open("","_parent","");
                      var ventana = window.self;
                      ventana.opener = window.self;
                      ventana.close();
                      }
                      window.onload = closeWindow;
                      </script>
                      <?
                     * */
                    mysql_query("INSERT INTO CLD_estadistiques_photos(photo ,data , type_info , ip , country , state , city) VALUES('$photoCode' ,'$DDD' , $typeInfo , '$ip' , '$country' , '$state' , '$city')");
                    echo("<script> alert('Your photo is uploaded.');top.location.href='../../../photo/" . $photoCode . "'</script>");
                }
                else {
                    echo("<script> alert('Your photo could not be uploaded.');top.location.href='../../../photo/" . $photoCode . "'</script>");
                }
            }
            ?>

        </body>                 
    </html>
