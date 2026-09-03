<?php
	
include "EVUPcommon.php";

VIC_fesLog("EVUPevents of $EVUPid");
        

$sql = "SELECT id,title,start_date from events where rental_id=$EVUPid ORDER BY start_date DESC LIMIT 0,30;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){echo "ko#1"; return; }
$nEvents = 0;
$strEvents = "";
while($APP_BdD->FetchRs()){
    $id =  $APP_BdD->GetField(1);
    $nom =  $APP_BdD->GetField(2);
    $quan =  APP_myDateStr($APP_BdD->GetField(3));//Nota: és int
    $strEvents.= "$id#$nom ($id) date: $quan#";
    $nEvents++;
    
}
$APP_BdD->CloseRs();

echo "$nEvents#$strEvents";


//
//
//
//
//	
//	$event = mysql_fetch_array(mysql_query("SELECT * from events where rental_id=$EVUPid"));
//	if (!$event) 
//	{
//            echo "ko#no events found";
//VIC_fesLog("EVUPevents, no events found for $EVUPid");
//
//	return;
//	}	
//        
//        $ret = "ok#";
//	
//        $ftpInfo = getFtpInfo($event_id,$event['ftp_folder_id']);
//        die ("ok#".$event_id."#".$ftpInfo);


?>