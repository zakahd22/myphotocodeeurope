<?
require_once "../../../common/global.php";
require_once("src/facebook.php");
include G_PATH.'common/general.php';

ini_set('allow_url_fopen', 'On');

$country = "-";
$city = "-";
$state = "-";

$_SESSION['nSts'] == 1;

$client = @$_SERVER['HTTP_CLIENT_IP'];
$forward = @$_SERVER['HTTP_X_FORWARDED_FOR'];
$remote = $_SERVER['REMOTE_ADDR'];
$result = "Unknown";
if (filter_var($client, FILTER_VALIDATE_IP)) {
    $ip = $client;
} elseif (filter_var($forward, FILTER_VALIDATE_IP)) {
    $ip = $forward;
} else {
    $ip = $remote;
}

$ip_data = @json_decode(file_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));

if ($ip_data && $ip_data->geoplugin_countryName != null) {
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
if (file_exists("../../../" . $ruta . "/" . $photoCode . "-3D.mp4")) {
    $photoUrl = "../../../events/" . $event['start_date'] . $event['id'] . "/" . $photoCode .  "-3D.mp4";
    $ruta2 = "../../../" . $ruta . "/" . $photoCode .  "-3D.mp4";
}
ECHO $photoCode;
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

                    <div style="position:absolute;top:14px;left:0px;"><a href="http://www.digital-centre.com" target="_blank"><img src="https://www.myphotocode.com/assets/images/header-logo-digitalcentre.jpg" width="115" height="30" border="0" /></a></div>


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
<?
//FB vars

$post_login_url = "../../../assets/php/templates/facebook-video3D.php";
//$app_id = "162595210519645";
//$app_secret = "2b9be3ff42e391ab3ab4ccfdc753c6f3";
$app_id = "127533357397300";
$app_secret = "d0ef26cf75bb1f9fd4445f5818821a49";



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

    $facebook = new Facebook(array());
    $facebook->setFileUploadSupport(true);
    $video_details = array(
        'access_token' => $access_token,
        'title' => "$eventTitle",
        'description' => "$description",
        'source' => '@' . realpath($ruta2)
    );
    try {
        $facebook->api('/me/videos', 'post', $video_details);
        mysql_query("INSERT INTO CLD_estadistiques_photos(photo ,data , type_info , ip , country , state , city) VALUES('$photoCode' ,'$DDD' , $typeInfo , '$ip' , '$country' , '$state' , '$city')");
        echo "<script> alert('Your video 3D is uploaded.');top.location.href='../../../photo/" . $photoCode . "'</script>";
    } catch (Exception $e) {

        echo "<script> alert('Your video is NOT uploaded , please try again');top.location.href='../../../photo/" . $photoCode . "'</script>";
    }
}
?>


        </body>

    </html>



            <? /* Exemple de la variable
             * $ipdata =  array (
              'geoplugin_request' => '88.87.195.102',
              'geoplugin_status' => 200,
              'geoplugin_credit' => 'Some of the returned data includes GeoLite data created by MaxMind, available from https://www.maxmind.com.',
              'geoplugin_city' => 'Sabadell',
              'geoplugin_region' => 'Catalonia',
              'geoplugin_areaCode' => '0',
              'geoplugin_dmaCode' => '0',
              'geoplugin_countryCode' => 'ES',
              'geoplugin_countryName' => 'Spain',
              'geoplugin_continentCode' => 'EU',
              'geoplugin_latitude' => '41.543301',
              'geoplugin_longitude' => '2.1094',
              'geoplugin_regionCode' => '56',
              'geoplugin_regionName' => 'Catalonia',
              'geoplugin_currencyCode' => 'EUR',
              'geoplugin_currencySymbol' => '€',
              'geoplugin_currencySymbol_UTF8' => '€',
              'geoplugin_currencyConverter' => '0.7312',
              )
             * 
             * CODES PAISOS : 
              A1,"Anonymous Proxy"
              A2,"Satellite Provider"
              AD,"Andorra"
              AE,"United Arab Emirates"
              AF,"Afghanistan"
              AG,"Antigua and Barbuda"
              AI,"Anguilla"
              AL,"Albania"
              AM,"Armenia"
              AN,"Netherlands Antilles"
              AO,"Angola"
              AP,"Asia/Pacific Region"
              AQ,"Antarctica"
              AR,"Argentina"
              AS,"American Samoa"
              AT,"Austria"
              AU,"Australia"
              AW,"Aruba"
              AX,"Aland Islands"
              AZ,"Azerbaijan"
              BA,"Bosnia and Herzegovina"
              BB,"Barbados"
              BD,"Bangladesh"
              BE,"Belgium"
              BF,"Burkina Faso"
              BG,"Bulgaria"
              BH,"Bahrain"
              BI,"Burundi"
              BJ,"Benin"
              BM,"Bermuda"
              BN,"Brunei Darussalam"
              BO,"Bolivia"
              BR,"Brazil"
              BS,"Bahamas"
              BT,"Bhutan"
              BV,"Bouvet Island"
              BW,"Botswana"
              BY,"Belarus"
              BZ,"Belize"
              CA,"Canada"
              CC,"Cocos (Keeling) Islands"
              CD,"Congo, The Democratic Republic of the"
              CF,"Central African Republic"
              CG,"Congo"
              CH,"Switzerland"
              CI,"Cote d'Ivoire"
              CK,"Cook Islands"
              CL,"Chile"
              CM,"Cameroon"
              CN,"China"
              CO,"Colombia"
              CR,"Costa Rica"
              CU,"Cuba"
              CV,"Cape Verde"
              CX,"Christmas Island"
              CY,"Cyprus"
              CZ,"Czech Republic"
              DE,"Germany"
              DJ,"Djibouti"
              DK,"Denmark"
              DM,"Dominica"
              DO,"Dominican Republic"
              DZ,"Algeria"
              EC,"Ecuador"
              EE,"Estonia"
              EG,"Egypt"
              EH,"Western Sahara"
              ER,"Eritrea"
              ES,"Spain"
              ET,"Ethiopia"
              EU,"Europe"
              FI,"Finland"
              FJ,"Fiji"
              FK,"Falkland Islands (Malvinas)"
              FM,"Micronesia, Federated States of"
              FO,"Faroe Islands"
              FR,"France"
              GA,"Gabon"
              GB,"United Kingdom"
              GD,"Grenada"
              GE,"Georgia"
              GF,"French Guiana"
              GG,"Guernsey"
              GH,"Ghana"
              GI,"Gibraltar"
              GL,"Greenland"
              GM,"Gambia"
              GN,"Guinea"
              GP,"Guadeloupe"
              GQ,"Equatorial Guinea"
              GR,"Greece"
              GS,"South Georgia and the South Sandwich Islands"
              GT,"Guatemala"
              GU,"Guam"
              GW,"Guinea-Bissau"
              GY,"Guyana"
              HK,"Hong Kong"
              HM,"Heard Island and McDonald Islands"
              HN,"Honduras"
              HR,"Croatia"
              HT,"Haiti"
              HU,"Hungary"
              ID,"Indonesia"
              IE,"Ireland"
              IL,"Israel"
              IM,"Isle of Man"
              IN,"India"
              IO,"British Indian Ocean Territory"
              IQ,"Iraq"
              IR,"Iran, Islamic Republic of"
              IS,"Iceland"
              IT,"Italy"
              JE,"Jersey"
              JM,"Jamaica"
              JO,"Jordan"
              JP,"Japan"
              KE,"Kenya"
              KG,"Kyrgyzstan"
              KH,"Cambodia"
              KI,"Kiribati"
              KM,"Comoros"
              KN,"Saint Kitts and Nevis"
              KP,"Korea, Democratic People's Republic of"
              KR,"Korea, Republic of"
              KW,"Kuwait"
              KY,"Cayman Islands"
              KZ,"Kazakhstan"
              LA,"Lao People's Democratic Republic"
              LB,"Lebanon"
              LC,"Saint Lucia"
              LI,"Liechtenstein"
              LK,"Sri Lanka"
              LR,"Liberia"
              LS,"Lesotho"
              LT,"Lithuania"
              LU,"Luxembourg"
              LV,"Latvia"
              LY,"Libyan Arab Jamahiriya"
              MA,"Morocco"
              MC,"Monaco"
              MD,"Moldova, Republic of"
              ME,"Montenegro"
              MG,"Madagascar"
              MH,"Marshall Islands"
              MK,"Macedonia"
              ML,"Mali"
              MM,"Myanmar"
              MN,"Mongolia"
              MO,"Macao"
              MP,"Northern Mariana Islands"
              MQ,"Martinique"
              MR,"Mauritania"
              MS,"Montserrat"
              MT,"Malta"
              MU,"Mauritius"
              MV,"Maldives"
              MW,"Malawi"
              MX,"Mexico"
              MY,"Malaysia"
              MZ,"Mozambique"
              NA,"Namibia"
              NC,"New Caledonia"
              NE,"Niger"
              NF,"Norfolk Island"
              NG,"Nigeria"
              NI,"Nicaragua"
              NL,"Netherlands"
              NO,"Norway"
              NP,"Nepal"
              NR,"Nauru"
              NU,"Niue"
              NZ,"New Zealand"
              OM,"Oman"
              PA,"Panama"
              PE,"Peru"
              PF,"French Polynesia"
              PG,"Papua New Guinea"
              PH,"Philippines"
              PK,"Pakistan"
              PL,"Poland"
              PM,"Saint Pierre and Miquelon"
              PN,"Pitcairn"
              PR,"Puerto Rico"
              PS,"Palestinian Territory"
              PT,"Portugal"
              PW,"Palau"
              PY,"Paraguay"
              QA,"Qatar"
              RE,"Reunion"
              RO,"Romania"
              RS,"Serbia"
              RU,"Russian Federation"
              RW,"Rwanda"
              SA,"Saudi Arabia"
              SB,"Solomon Islands"
              SC,"Seychelles"
              SD,"Sudan"
              SE,"Sweden"
              SG,"Singapore"
              SH,"Saint Helena"
              SI,"Slovenia"
              SJ,"Svalbard and Jan Mayen"
              SK,"Slovakia"
              SL,"Sierra Leone"
              SM,"San Marino"
              SN,"Senegal"
              SO,"Somalia"
              SR,"Suriname"
              ST,"Sao Tome and Principe"
              SV,"El Salvador"
              SY,"Syrian Arab Republic"
              SZ,"Swaziland"
              TC,"Turks and Caicos Islands"
              TD,"Chad"
              TF,"French Southern Territories"
              TG,"Togo"
              TH,"Thailand"
              TJ,"Tajikistan"
              TK,"Tokelau"
              TL,"Timor-Leste"
              TM,"Turkmenistan"
              TN,"Tunisia"
              TO,"Tonga"
              TR,"Turkey"
              TT,"Trinidad and Tobago"
              TV,"Tuvalu"
              TW,"Taiwan"
              TZ,"Tanzania, United Republic of"
              UA,"Ukraine"
              UG,"Uganda"
              UM,"United States Minor Outlying Islands"
              US,"United States"
              UY,"Uruguay"
              UZ,"Uzbekistan"
              VA,"Holy See (Vatican City State)"
              VC,"Saint Vincent and the Grenadines"
              VE,"Venezuela"
              VG,"Virgin Islands, British"
              VI,"Virgin Islands, U.S."
              VN,"Vietnam"
              VU,"Vanuatu"
              WF,"Wallis and Futuna"
              WS,"Samoa"
              YE,"Yemen"
              YT,"Mayotte"
              ZA,"South Africa"
              ZM,"Zambia"
              ZW,"Zimbabwe"
             * 
             * 
              Continents :
             * 

              AF = Africa
              AS = Asia
              EU = Europe
              NA = North America
              SA = South America
              OC = Oceania
              AN = Antarctica

             */ ?>  
