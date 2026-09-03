<?php
require("common.php");

if(!$APP_user) return;

if(isset($_REQUEST['devicetoken'])){ $devicetoken = $_REQUEST['devicetoken'];} else $devicetoken = '';

//20170220apns
//include("../easyapns/src/php/APP_apns.php");
//APNS_unregisterDeviceOwner($devicetoken);

echo "$APP_xml$APP_xmlOKcomm</return>";

?>
