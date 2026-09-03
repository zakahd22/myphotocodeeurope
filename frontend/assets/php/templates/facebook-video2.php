<?php
require_once "../../../common/global.php";
require_once("src/facebook.php");
include G_PATH.'common/general.php';

ini_set('allow_url_fopen', 'On');

$photoCode = $_SESSION['photoCode'];
$photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$photoCode'"));
$eventID = $photo[event_id];
$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$eventID"));
$ruta = "events/" . $event['start_date'] . $event['id'];

if (file_exists("../../../" . $ruta . "/" . $photoCode . ".mp4")) {
    $photoUrl = "../../../" . $ruta . "/" . $photoCode . ".mp4";
}
if (file_exists("../../../" . $ruta . "/" . $photoCode . ".wmv")) {
    $photoUrl = "../../../" . $ruta . "/" . $photoCode . ".wmv";
}

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

            $post_login_url = G_PAGE . "assets/php/templates/facebook-video.php";
            /*
	    $app_id = "162595210519645";
            $app_secret = "2b9be3ff42e391ab3ab4ccfdc753c6f3";
            */
	    $app_id = "127533357397300";
	    $app_secret = "d0ef26cf75bb1f9fd4445f5818821a49";
	    $album_name = 'MyPhotoCode';
            $album_description = 'Strip photos';



            $code = $_REQUEST["code"];
            if (empty($code)) {
                $dialog_url = "https://www.facebook.com/dialog/oauth?client_id="
                        . $app_id . "&redirect_uri="
                        . urlencode($post_login_url)
                        . "&scope=publish_actions";
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
                $facebook = new Facebook(array(
                    'appId' => $app_id,
                    'secret' => $app_secret,
                    'cookie' => true,
                ));
echo $photoUrl;
                $attachment = array(
//                    'access_token' => $access_token,
//                    'message' => 'My Video : ' . $photoUrl ,
                    'access_token' => $access_token
                    
                );

                $result = $facebook->api(
                        'me/feed', 'POST', $attachment
                );
                
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
                          <?php 
            }
            ?>


        </body>

    </html>
