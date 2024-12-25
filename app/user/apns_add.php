<?php
require("common.php");

if(!$APP_user) return;

if(isset($_REQUEST['appname'])){ $appname = $_REQUEST['appname'];} else $appname = '';
if(isset($_REQUEST['appversion'])){ $appversion = $_REQUEST['appversion'];} else $appversion = '';
if(isset($_REQUEST['deviceuid'])){ $deviceuid = $_REQUEST['deviceuid'];} else $deviceuid = '';
if(isset($_REQUEST['devicetoken'])){ $devicetoken = $_REQUEST['devicetoken'];} else $devicetoken = '';
if(isset($_REQUEST['devicename'])){ $devicename = $_REQUEST['devicename'];} else $devicename = '';
if(isset($_REQUEST['devicemodel'])){ $devicemodel = $_REQUEST['devicemodel'];} else $devicemodel = '';
if(isset($_REQUEST['deviceversion'])){ $deviceversion = $_REQUEST['deviceversion'];} else $deviceversion = '';
if(isset($_REQUEST['pushbadge'])){ $pushbadge = $_REQUEST['pushbadge'];} else $pushbadge = '';
if(isset($_REQUEST['pushalert'])){ $pushalert = $_REQUEST['pushalert'];} else $pushalert = '';
if(isset($_REQUEST['pushsound'])){ $pushsound = $_REQUEST['pushsound'];} else $pushsound = '';

//20170220apns
//include("../easyapns/src/php/APP_apns.php");
//APNS_registerDeviceUser($appname, $appversion, $deviceuid, $devicetoken, $devicename, $devicemodel, $deviceversion, $pushbadge, $pushalert, $pushsound, $APP_userId);

echo "$APP_xml$APP_xmlOKcomm</return>";

?>
