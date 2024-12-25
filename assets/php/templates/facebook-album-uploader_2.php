<?php
require_once dirname (__FILE__) . "/../../../common/global.php";
include G_PATH.'common/general.php';

ini_set('allow_url_fopen', 'On');
ini_set("display_errors", 0);

ini_set('session.auto_start', 'Off');
ini_set('session.bug_compat_42', 'On');
ini_set('session.bug_compat_warn', 'On');
ini_set('session.cache_expire', 180);
ini_set('session.cache_limiter', 'nocache');
ini_set('session.cookie_lifetime', 0);
ini_set('session.cookie_path', '/');
ini_set('session.cookie_secure', 'Off');
ini_set('session.entropy_length', 0);
ini_set('session.gc_divisor', 100);
ini_set('session.gc_maxlifetime', 1440);
ini_set('session.gc_probability', 1);
ini_set('session.name', 'PHPSESSID');
ini_set('session.save_handler', 'files');
ini_set('session.save_path', '/tmp');
ini_set('session.serialize_handler', 'php');
ini_set('session.use_cookies', 'On');
ini_set('session.use_only_cookies', 'Off');
ini_set('session.use_trans_sid', 'On');

$html = "";
$html .= <<<HTML
    <script>
        function closeWindow() {
            window.open("", "_parent", "");
            var ventana = window.self;
            ventana.opener = window.self;
            ventana.close();
        }
        window.onload = closeWindow;
    </script>
    
    <html>
    <head>
        <title>MyPhotoCode</title>
        <style type="text/css">
            html,* { border:0; padding:0; margin:0; }
            body { background: #64164e; color: #fcebec; }
        </style>
    </head>
    <body>
        <div style="position:absolute;top:0px;left:0px;width:100%;height:100%;">
            <table width="100%" height="100%">
                <tr>
                    <td align="center" valign="middle">
                        <span style="font-family:Arial;font-size:42px;font-weight:bold;">Uploading, please wait!</span>
                        <br /><br />
                    </td>
                </tr>
            </table>
        </div>
        <div style="position:absolute;top:0px;left:0px;">
        </div>
    </body>
    </html>                        
HTML;
utils::log("facebock", "logasd");
//echo $html;

if (isset($_REQUEST['id'])) {
    $event_folder = $_REQUEST['id'];
    $limit = (isset($_REQUEST['l'])? $_REQUEST['l'] : 0);
    
    $_SESSION['event_folder'] = $event_folder;
//    utils::log($event_folder, G_PATH . "log/logFacebook", "TRACE 0");
    $start_date = substr($event_folder, 0, 8);
    $id = substr($event_folder, 8);

    $event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$id"));

    //FB vars
    //$app_id = "162595210519645";
    $app_id = "127533357397300";                        
    //$app_secret = "2b9be3ff42e391ab3ab4ccfdc753c6f3";
    $app_secret = "d0ef26cf75bb1f9fd4445f5818821a49";
    $post_login_url = G_PAGE . "assets/php/templates/facebook-album-uploader_2.php?id=$event_folder&l={$limit}";
    $album_name = $event['title'];
    $album_description = $event['start_date'];
    
    //Obtain the access_token with publish_stream permission 
    $code = $_REQUEST["code"];
    if (empty($code)) {
        $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=" . $app_id . "&redirect_uri=" . urlencode($post_login_url). "&scope=publish_actions";
//        utils::log($dialog_url, G_PATH . "log/logFacebook", "TRACE 1");
        echo $html;
        echo "<script>top.location.href='" . $dialog_url . "'</script>";
    } else {
        echo $html;
        $q = mysql_query("SELECT * FROM photos WHERE event_id=$id LIMIT {$limit}, 1");
        $limit++;
        while ($photo = mysql_fetch_array($q)) {
            if (imagecreatefromjpeg("../../../events/" . $event_folder . "/" . $photo['code'] . ".jpg")) {
//                utils::log($photo['code'], G_PATH . "log/logFacebook", "TRACE 2");
                $photoUrl = G_PAGE . "events/" . $event_folder . "/" . $photo['code'] . ".jpg";
//                utils::log($photoUrl, G_PATH . "log/logFacebook", "TRACE 3");
                $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
                        . $app_id . "&redirect_uri=" . urlencode($post_login_url)
                        . "&client_secret=" . $app_secret
                        . "&code=" . $code;

                $response = file_get_contents($token_url);
//                utils::log($token_url, G_PATH . "log/logFacebook", "TRACE 4");
//                utils::log($response, G_PATH . "log/logFacebook", "TRACE 5");
                $p = null;
                parse_str($response, $p);
                $access_token = $p['access_token'];
                $graph_url = "https://graph.facebook.com/me/photos?access_token=" . $access_token;

                $params = array();
                $params['url'] = $photoUrl;

                // Start the Graph API call
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $graph_url);

                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
                $result = curl_exec($ch);
                $decoded = json_decode($result, true);
                curl_close($ch);

                if (is_array($decoded) && isset($decoded['id'])) {
                    echo "<script>top.location.href='facebook-album-uploader_2.php?id={$event_folder}&l={$limit}';</script>";
//                    utils::log("success {$photo['code']}", G_PATH . "log/logFacebook", "TRACE OK");
                }
            }
        }
    }
}