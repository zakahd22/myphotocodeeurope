<?
	
include "EVUPcommon.php";
	
	////////////////////////////////////////////////////////////////////
	// GO!
	////////////////////////////////////////////////////////////////////
	
//	dbConnect();

//	$dongle = $_REQUEST['dongle'];
	$event_id = $_REQUEST['event_id'];
	if (!$event_id) die("ko#event_id");
        
	$file = $_REQUEST['f'];
	if (!$file) die("ko#f");
        
        $randString = substr($file, 1, 3);
        
        
VIC_fesLog("EVUPconfirmupload, event_id: $event_id, file: $file, randString: $randString");
	
	
//	if (!$dongle) die ("ko#dongle");
	
	
$sql = "SELECT id from booths where rand_string='$randString'";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){echo "ko#1"; return; }

if($APP_BdD->FetchRs()){
    $booth_id =  $APP_BdD->GetField(1);
$APP_BdD->CloseRs();
    
}
else{echo "ko#booth"; $APP_BdD->CloseRs(); return; }


        
	
$sql = "SELECT path,start_date FROM `events` INNER JOIN ftp_folders ON events.`ftp_folder_id` = ftp_folders.id WHERE events.id = $event_id";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){echo "ko#2"; return; }

if($APP_BdD->FetchRs()){
    $path =  $APP_BdD->GetField(1);
    $start_date =  $APP_BdD->GetField(2);

$APP_BdD->CloseRs();
    
}
else{echo "ko#no event: no path"; $APP_BdD->CloseRs(); return; }

        
VIC_fesLog("EVUPconfirmupload, ftp_folder: $path; file: $file");

	
//	$old_file = "../uploads/".$ftp_folder['path']."/".$file;
	$old_file = "../uploads/$path/$file";
	if (file_exists($old_file))
	{
		
//		$new_file = "../events/".$event['start_date'].$event['id']."/".$file;
		$new_file = "../events/$start_date$event_id/$file";
		rename($old_file,$new_file) or die("ko#rename");
		chmod($new_file,0777) or die("ko#chmod");
                
                
VIC_fesLog("EVUPconfirmupload, new_file: $new_file;");

		
		$file_exploded = explode(".",$file);
		if ($file_exploded[1] == "jpg")
		{
		
			$photodate = $_REQUEST['s']; if(!$photodate) $photodate = date('Y-m-d H:i');
  
//20131005 INICI 
                        if(strlen($photodate) != 16){
                            $photodate = substr($photodate, 0, 4)."-".substr($photodate, 4, 2)."-".substr($photodate, 6);
                        }
  
//20131005 FINAL 
                        
                        
                $existeix = mysql_fetch_array(mysql_query("SELECT * from photos where code='$file_exploded[0]'"));
VIC_fesLog("EVUPconfirmupload, dongle: $dongle; booth: $booth; photodate: $photodate ; existeix: $existeix");
                if (!$existeix) {
                    mysql_query("INSERT INTO photos SET code='$file_exploded[0]', event_id=$event_id, booth_id=$booth_id, Appusr_datetime='$photodate' ") or die("ko#insert");

                }
		
		}
		

VIC_fesLog("EVUPconfirmupload, ok");
		
		die ("ok");
		
	}
	else
	{
		die ("ko#file");
	}

?>