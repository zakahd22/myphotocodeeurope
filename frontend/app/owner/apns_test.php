<?php
require("common.php");

if(!$APP_user) return;



echo "$APP_xml$APP_xmlOKcomm$toXml</return>";//20170220apns
//20170220apns
//
////echo "TRACE01"; return;
//$text = 'test message: ';
//if(isset($_REQUEST['text'])){ $text.= $_REQUEST['text'];} 
//
//
//include("../easyapns/src/php/APP_apns.php");
//
//$toXml = "";
//
//$toXml.= "<TRACE>addMessage to user id $APP_userId</TRACE>";
//$toXml.= "<TRACE>alert text $text</TRACE>";
//
////201306 vaig fer un canvi a bagde if(isset($_REQUEST['badge'])){ $numBadge = $_REQUEST['badge']; $toXml.= "<TRACE>badge $numBadge</TRACE>";} else {$numBadge = 0;}
//if(isset($_REQUEST['badge'])){ $numBadge = $_REQUEST['badge']; $toXml.= "<TRACE>badge $numBadge</TRACE>";} else {$numBadge = -1;}//201306 
//
//if(isset($_REQUEST['acme1'])){ $Acme1 = $_REQUEST['acme2']; $toXml.= "<TRACE>acme1 $Acme1</TRACE>";  } else {$Acme2 = "";}
//
//if(isset($_REQUEST['acme2'])){ $Acme2 = $_REQUEST['acme2']; $toXml.= "<TRACE>acme2 $Acme2</TRACE>";} else {$Acme2 = "";}
//
//$APNS_MessageAdded = APNS_newAlertTestOwner($APP_userId,$text,$numBadge, $Acme1, $Acme2);
//
//$toXml.= "<TRACE>APNS_newAlertTestOwner returned $APNS_MessageAdded</TRACE>";
//
//$toResp =  "$APP_xml$APP_xmlOKcomm$toXml</return>";
//
//
//    if($APNS_MessageAdded){
//        ignore_user_abort(true);
//        header("Connection: close");
//        header("Content-Length: " . mb_strlen($toResp));
//        echo $toResp;
//        flush();    
//        APNS_sendMessages();
//    }
//    else{
//        echo $toResp;
//    }


?>
