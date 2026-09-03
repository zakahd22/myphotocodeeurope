<?
include '../../sessio.php';
include '../../conexio.php';
		
	$usb_id = $_REQUEST['usb_id'];
	
	$set = $_REQUEST['set'];
        $CLD_CON2 = clone($CLD_CON);
        $CLD_CON2->OpenRs("SELECT * FROM usbs WHERE id = $usb_id");
	if ($CLD_CON2->FetchArray()) {
        $usb_ref = $CLD_CON2->GetArrayField("creation_date") . "" . $usb_id;	
	
	$file1 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."a.png";
	if (file_exists($file1)) unlink($file1);			

	$file2 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."b.png";
	if (file_exists($file2)) unlink($file2);			

	$file3 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."c.png";
	if (file_exists($file3)) unlink($file3);			

	$file4 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."d.png";
	if (file_exists($file4)) unlink($file4);			

        
	$CLD_CON->Execute("UPDATE usbs SET frame$set=0 WHERE id=$usb_id");
      
	
	for ($x = ($set+1); $x < 12; $x++)
	{
		$usb = $CLD_CON2->GetArrayField("frame$x");
		if ( $usb != 0)
		{
			
			$prev = $x - 1;

			$file1 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$x."a.png";
			if (file_exists($file1)) rename($file1, "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$prev."a.png");
			
			$file2 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$x."b.png";
			if (file_exists($file2)) rename($file2, "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$prev."b.png");
			
			$file3 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$x."c.png";
			if (file_exists($file3)) rename($file3, "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$prev."c.png");
			
			$file4 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$x."d.png";
			if (file_exists($file4)) rename($file4, "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$prev."d.png");
			
			$frameId = $CLD_CON2->GetArrayField("frame$x");
			$CLD_CON->Execute("UPDATE usbs SET frame$prev=$frameId WHERE id=$usb_id");
			$CLD_CON->Execute("UPDATE usbs SET frame$x=0 WHERE id=$usb_id");
			
		}
		
	}
	  }
	echo $usb_id;

?>