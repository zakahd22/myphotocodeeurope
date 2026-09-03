<?php

$country = "-";
$city = "-";
$state = "-";
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

//20151023 --> Aleix
    //$ip_data = @json_decode(url_get_contents("https://www.geoplugin.net/json.gp?ip=" . $ip));
    $ip_data = null;
//20151023 --> Aleix

if ($ip_data && $ip_data->geoplugin_countryName != null) {
    $country = $ip_data->geoplugin_countryName;
}
if ($ip_data && $ip_data->geoplugin_city != null) {
    $city = $ip_data->geoplugin_city;
}
if ($ip_data && $ip_data->geoplugin_region != null) {
    $state = $ip_data->geoplugin_region;
}

$DDD = date("Y-m-d H:i:s");            
$CLD_CON2->Execute("INSERT INTO CLD_ownerConnections(user ,type_user , data , pais , state , ciutat) VALUES( $userId , $typeUser , '$DDD' , '$country' , '$state' , '$city')");
?>
