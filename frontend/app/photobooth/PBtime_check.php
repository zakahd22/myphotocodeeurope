<?php

/*
 * Actualització de saldo de prints.
 * No hauriem de comprovar res del PB
 */
$APP_common_no_idb = true;

require("common.php");
if(!$APP_dongleOK) return;

$myScript = "PBtime_check";

//signatura:
$signature = strtoupper(sha1($APP_dongle.$APP_tact.$APP_seccode));
if($signature != $APP_sg){
    APP_fesLog("Error - $myScript, sg error local: $signature url:$APP_sg");
    echo "Error - snt";
    return;
}


////nota $APP_tact serà hora UTC, cal retornar els segons de desfase
//
//if(strlen($APP_tact) != 14){
//    APP_fesLog("ERROR - $myScript, APP_tact és curt $APP_tact");
//    return "ko#Error no 14";
//}
//
//$a = intval(substr($aaaammdd,0,4));
//$m = intval(substr($aaaammdd,4,2));
//$d = intval(substr($aaaammdd,6,2));
//$h = intval(substr($aaaammdd,8,2));
//$i = intval(substr($aaaammdd,10,2));
//$s = intval(substr($aaaammdd,12,2));
//
//
//$dongleUTC = new DateTime(substr($aaaammdd,0,4)."-".substr($aaaammdd,4,2)."-".substr($aaaammdd,6,2)." ".substr($aaaammdd,8,2).":".substr($aaaammdd,10,2).":".substr($aaaammdd,12,2),new DateTimeZone('UTC'));
//
//    APP_fesLog("TRACE $myScript - dongleUTC: {$dongleUTC->format("'Y-m-d H:i'")}");
//
//$araUTC = new DateTime("now",new DateTimeZone('UTC'));
//    APP_fesLog("TRACE $myScript - araUTC: {$araUTC->format("'Y-m-d H:i'")}");
//
//$diffInSeconds = $araUTC->getTimestamp() - $dongleUTC->getTimestamp();
//
//    APP_fesLog("TRACE $myScript, diffInSeconds: $diffInSeconds");

//millor amb timeStamp
if(isset($_REQUEST['tsp'])){ $dongleTimeStamp = $_REQUEST['tsp'];} 
else {
    APP_fesLog("ERROR - $myScript, no hi ha tsp");
    return "ko#Error no t";
    
}
if(!$dongleTimeStamp){
    APP_fesLog("ERROR - $myScript, tsp a zero");
    return "ko#Error null t";

}

$araUTC = new DateTime("now",new DateTimeZone('UTC'));
    APP_fesLog("TRACE $myScript - araUTC: {$araUTC->format("'Y-m-d H:i'")}");

$diffInSeconds = $araUTC->getTimestamp() - $dongleTimeStamp;
    APP_fesLog("TRACE $myScript, diffInSeconds: $diffInSeconds");

echo "ok#".$diffInSeconds;
?>
