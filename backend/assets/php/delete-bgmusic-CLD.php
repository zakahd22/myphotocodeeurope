<?
include '../../sessio.php';
include '../../conexio.php';
		
	$usb_id = $_REQUEST['usb'];
	
	
	$CLD_CON->OpenRs("SELECT creation_date FROM usbs WHERE id = $usb_id");
        if ($CLD_CON->FetchArray()) {
            $usb_ref = $CLD_CON->GetArrayField("creation_date") . "" . $usb_id;
            if (file_exists("../../usbs/" . $usb_ref . "/PhotoIdUpload/Logo.jpg")) {
                unlink("../../usbs/".$usb_ref."/PhotoIdUpload/BGmusic.mp3");			
            }
	$CLD_CON->Execute("UPDATE usbs SET bgmusic=0 WHERE id=$usb_id");
        }
	echo $usb_id;

?>