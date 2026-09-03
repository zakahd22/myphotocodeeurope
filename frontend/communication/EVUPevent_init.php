<?
	
include "EVUPcommon.php";
	
	function getFtpInfo($APP_BdD,$event_id,$ftp_folder_id)
	{
		
		if ($ftp_folder_id == 0)
		{
                    $sql = "SELECT id FROM ftp_folders ORDER BY RAND() LIMIT 1";
                    $esOK = $APP_BdD->OpenRs($sql);
                    if(!$esOK){ return "ko#ko#/#ko#1"; }
                    if($APP_BdD->FetchRs()){
                        $ftp_folder_id =  $APP_BdD->GetField(1);
                    }
                    $APP_BdD->CloseRs();
                    if ($ftp_folder_id){
                        $sql = "UPDATE events SET ftp_folder_id=$ftp_folder_id WHERE id=$event_id";
                        $esOK = $APP_BdD->Execute($sql);
                        if(!$esOK) {
                            return "ko#ko#/#ko#2#$sql.";
                        }
                    }
                    else return "ko#ko#/#ko#3";
                }
		else
		{
                    $sql = "SELECT host,user,password FROM ftp_folders WHERE id=$ftp_folder_id";
                    $esOK = $APP_BdD->OpenRs($sql);
                    if(!$esOK){ return "ko#ko#/#ko#4"; }
                    if($APP_BdD->FetchRs()){
                        $ftp_folder_id =  $APP_BdD->GetField(1);
                        $host = $APP_BdD->GetField(1);
                        $user = $APP_BdD->GetField(2);
                        $pass = $APP_BdD->GetField(3);
                    }
                    $APP_BdD->CloseRs();
		}
		return $host."#".$user."#/#".$pass;
		
	}



	$event_id = $_REQUEST['event_id'];

VIC_fesLog("EVUPevent_init, event_id: $event_id");
        

$sql = "SELECT title,start_date,ftp_folder_id from events where id=$event_id and rental_id=$EVUPid";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){echo "ko#1"; return; }

if($APP_BdD->FetchRs()){
    $nom =  $APP_BdD->GetField(1);
    $quan =  APP_myDateStr($APP_BdD->GetField(2));//Nota: és int
    $folder =  $APP_BdD->GetField(3);
    $ret = "ok#$nom, date: $quan";
$APP_BdD->CloseRs();
    
}
else{
    $ret = "ko#event not found or invalid owner";
    VIC_fesLog("EVUPevent_init, event off or from diferent owners ");
    echo $ret; return;
$APP_BdD->CloseRs();
}

$ftpInfo = getFtpInfo($APP_BdD,$event_id,$folder);

 echo "$ret#$ftpInfo";





?>