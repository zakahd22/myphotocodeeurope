<?php
    require_once "../../../../common/global.php";
    require_once("src/facebook.php");
    include G_PATH.'common/general.php';
    
    ini_set('allow_url_fopen', 'On');

    $country = "-";
    $city = "-";
    $state = "-";

    if (!$link)
        die('ko');
   
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

//    $ip_data = @json_decode(file_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));

    if($ip_data && $ip_data->geoplugin_countryName != null) {
        $continentCode = $ip_data->geoplugin_continentCode;
        $countryCode = $ip_data->geoplugin_countryCode;
        $country = $ip_data->geoplugin_countryName;
        $city = $ip_data->geoplugin_city;
        $state = $ip_data->geoplugin_region;
    }

    $description = 'Video Made in a Digital Centre´s Photobooth (www.digital-centre.com)';

    switch ($continentCode) {
        case "NA":
            break;

        case "AF":
            break;

        case "AS":
            break;

        case "EU":
            if ($countryCode == "ES") {
                $description = 'Vídeo hecho en un Fotomatón de Digital Centre. (www.digital-centre.com)'; //Espanyol
            }
            if ($countryCode == 'DE') {
                $description = 'Video gemacht in einer FotoKabine von Digital Centre (www.digital-centre.com)';  //Alemany
            }
            if ($countryCode == "FR") {
                $description = "Vidéo Fabriqué dans un Photo Booth du Digital Centre (www.digital-centre.com)"; //Frances
            }
            if ($countryCode == "IT") {
                $description = "Video fatto in una cabina della foto di Digital Centre (www.digital-centre.com)"; //Italia
            }
            if ($countryCode == "RO") {
                $description = "Video realizat într-o cabină foto de Digital Centre (www.digital-centre.com)"; //Rumanes
            }
            if ($countryCode == "PT") {
                $description = 'Vídeo feito num Digital Centre Fotomatón. (www.digital-centre.com)'; //Portogues
            }
            
            break;

    	case "SA":
	    $description = 'Vídeo hecho en un Fotomatón de Digital Centre. (www.digital-centre.com)';
	    if ($countryCode == "BR") {
	        $description = 'Vídeo feito num Digital Centre Fotomatón. (www.digital-centre.com)'; //Portogues
	    }
	    break;
        
        case "OC":
            break;

        case "AN":
            break;
        
        default:
            break;
    }

    $photoCode = $_SESSION['photoCode'];
    $photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$photoCode'"));
    $eventID = $photo[event_id];
    $event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$eventID"));
    $ruta = "events/" . $event['start_date'] . $event['id'];
    $eventTitle = $event['title'];
    $DDD = date("Y-m-d H:i:s");
    $typeInfo = 6;
    $description = $description . " " . $event['hashtag'];
    
    if ($eventTitle == "Untitled") {
        $eventTitle = "My Video!!";
    }
    
    if (file_exists("../../../" . $ruta . "/" . $photoCode . ".mp4")) {
        $photoUrl = "../../../events/" . $event['start_date'] . $event['id'] . "/" . $photoCode . ".mp4";
        $ruta2 = "../../../" . $ruta . "/" . $photoCode . ".mp4";
    }
    
    if (file_exists("../../../" . $ruta . "/" . $photoCode . ".wmv")) {
        $photoUrl = "../../../events/" . $event['start_date'] . $event['id'] . "/" . $photoCode . ".wmv";
        $ruta2 = "../../../" . $ruta . "/" . $photoCode . ".wmv";
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
    <div id="header">
        <div id="content">
            <div style="position:absolute;top:14px;left:0px;">
                <a href="http://www.digital-centre.com" target="_blank">
                    <img src="../../../assets/images/header-logo-digitalcentre.jpg" width="115" height="30" border="0" />
                </a>
            </div>
        </div>
    </div>

    <div id="wrap">
        <div id="inputCode">
            <div id="top">
                <img src="../../../assets/images/blank.gif" width="1" height="1" />
            </div>
            <div id="body">
                <div style="margin-bottom:12px;">
                    <img src="../../../assets/images/loading-bar.gif">
                </div>
                <div>
                    <img src="../../../assets/images/txt-uploadingtofacebook.png">
                </div>
            </div>
            <div id="bottom">
                <img src="../../../assets/images/blank.gif" width="1" height="1" />
            </div>
        </div>
    </div>
    <div id="popupWait">
        <img src='../../../assets/images/loading.gif' width="24" height="24" />
    </div>
    <div id="popup">
    </div>
    <div id="backgroundPopup">
    </div>

    <?
        //Facebook connection

        /*another APP, undocumented and losed
        $app_id = "162595210519645";
        $app_secret = "2b9be3ff42e391ab3ab4ccfdc753c6f3";
        */

        $app_id = "127533357397300"; //App at the count of fb -> cris@dc-image.com                       
        $app_secret = "d0ef26cf75bb1f9fd4445f5818821a49";
        $post_login_url = G_PAGE . "assets/php/templates/facebook-video.php";
	$album_name = 'MyPhotoCode';
	$album_description = 'Strip videos';

	//Obtain the access_token with publish_stream permission 
	$code = $_REQUEST["code"];
	
	//If not logged, we need the permissions
	/*if(empty($code)){
	    $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=". $app_id . "&redirect_uri=". urlencode($post_login_url) . "&scope=publish_stream";
            echo("<script>top.location.href='" . $dialog_url . "'</script>");
	*/

	//NEED to refresh the access_token relogin again
	$facebook = new Facebook(array());
	if($code){
            $facebook->getLogoutUrl();	
	}
        else{
	    $dialog_url = "https://www.facebook.com/dialog/oauth?client_id=". $app_id . "&redirect_uri=". urlencode($post_login_url) . "&scope=publish_actions";
            echo("<script>top.location.href='{$dialog_url}'</script>");
	}

	   /*
	    $token_url = "https://graph.facebook.com/oauth/access_token?client_id="
	    . $app_id . "&redirect_uri=" 
	    . urlencode($post_login_url)
	    . "&client_secret=" . $app_secret
	    . "&code=" . $code;

	    echo ("<script> alert('Acces_token: ".$token_url."');</script>");

	    $response = file_get_contents(urlencode($access_token));
	    $response = $token_url;
	    $p = null;
	    parse_str($response, $p);
	    */
	    //$access_token = $p['access_token'];
 	    $access_token = "CAABzZCax29TQBAJvy6xb5GOYDOxky0Gya4hYEe6nRJDb2hd8Qma1VTuvQlOm6VO97XBRW7qSBDBDUTUBRWE3wkFT9929OoRs16ZAKNhPdpaDLkMYWDBM6ZArtpBNE9hCNTM2oEmQ3bMPmKGsT62VgbHvdcFDEJoCjPVJHyZADUQbjgX6r7Ywb1krcJBFOZBndQRsvA8lN9yxyCIKg115U&expires=5180295";
	    $facebook->setFileUploadSupport(true);

	    $video_details = array(
		'access_token' => $access_token,
		'title' => "$eventTitle",
		'description' => "$description",
		'source' => '@' . realpath($ruta2)
	    );

	    try{
	    	$facebook->api('/me/videos', 'post', $video_details);        
	    	mysql_query("INSERT INTO CLD_estadistiques_photos(photo ,data , type_info , ip , country , state , city) VALUES('$photoCode' ,'$DDD' , $typeInfo , '$ip' , '$country' , '$state' , '$city')");
	    	echo("<script> alert('Your video is uploaded.');top.location.href='../../../photo/" . $photoCode . "'</script>");
	    } 
	    catch (FacebookApiException $e){
	    	echo("<script> alert('Your video is NOT uploaded, error: ".$e.", please try again');top.location.href='../../../photo/" . $photoCode . "'</script>");
	    }   
 
    ?>
</body>
</html>
