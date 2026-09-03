<?php
require_once '../../../common/global.php';
include G_PATH."common/general.php";

ini_set('allow_url_fopen', 'On');

$photoCode = $_SESSION['photoCode'];
$typeInfo = $_GET['typeInfo'];
$country = "-";
$city = "-";
$state = "-";
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
} elseif (filter_var($remote, FILTER_VALIDATE_IP)) {
    $ip = $remote;
}
else{
     $ip = null;
}

$ip_data = @json_decode(file_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));

if ($ip_data && $ip_data->geoplugin_countryName != null) {
    $continentCode = $ip_data->geoplugin_continentCode;
    $countryCode = $ip_data->geoplugin_countryCode;
    $country = $ip_data->geoplugin_countryName;
    $city = $ip_data->geoplugin_city;
    $state = $ip_data->geoplugin_region;
}
$DDD = date("Y-m-d H:i:s");
mysql_query("INSERT INTO CLD_estadistiques_photos(photo ,data , type_info , ip , country , state , city) VALUES('$photoCode' ,'$DDD' , $typeInfo , '$ip' , '$country' , '$state' , '$city')");

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
