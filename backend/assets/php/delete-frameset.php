<?
    require_once "../../common/global.php";
    include G_PATH.'common/general.php';
		
	$usb_id = $_REQUEST['usb_id'];
	$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
	$set = $_REQUEST['set'];
	
	if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");
	
	$usb_ref = $usb['creation_date'].$usb['id'];

	
	$file1 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."a.png";
	if (file_exists($file1)) unlink($file1);			

	$file2 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."b.png";
	if (file_exists($file2)) unlink($file2);			

	$file3 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."c.png";
	if (file_exists($file3)) unlink($file3);			

	$file4 = "../../usbs/".$usb_ref."/PhotoIdUpload/Frames/".$set."d.png";
	if (file_exists($file4)) unlink($file4);			

	mysql_query("UPDATE usbs SET frame$set=0 WHERE id=$usb_id");
	
	
	for ($x = ($set+1); $x < 12; $x++)
	{
		
		if ($usb['frame'.$x] != 0)
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
			
			$frameId = $usb['frame'.$x];
			mysql_query("UPDATE usbs SET frame$prev=$frameId WHERE id=$usb_id");
			mysql_query("UPDATE usbs SET frame$x=0 WHERE id=$usb_id");
			
		}
		
	}
	
	header("Location:../../rental/usbs/edit/".$usb_ref."#frames");

?>