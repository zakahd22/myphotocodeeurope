<?
        require_once "../../common/global.php";
        include G_PATH.'common/general.php';
	
	$usb_id = $_REQUEST['usb_id'];
	$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
	$set = $_REQUEST['set'];
	
	if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");
	
	$usb_ref = $usb['creation_date'].$usb['id'];
	
	$file = G_PATH."usbs/".$usb_ref."/PhotoIdEvents/CustomShots/".$set.".jpg";
	if (file_exists($file)) unlink($file);			
	
	mysql_query("UPDATE usbs SET custom$set=0 WHERE id=$usb_id");
		
	for ($x = ($set+1); $x < 12; $x++)
	{
	
		if ($usb['custom'.$x] != 0)
		{
		
			$prev = $x - 1;
		
			$file = G_PATH."usbs/".$usb_ref."/PhotoIdEvents/CustomShots/".$x.".jpg";
			if (file_exists($file)) rename($file, G_PATH."usbs/".$usb_ref."/PhotoIdEvents/CustomShots/".$prev.".jpg");
		
			mysql_query("UPDATE usbs SET custom$prev=1 WHERE id=$usb_id");
			mysql_query("UPDATE usbs SET custom$x=0 WHERE id=$usb_id");
		
		}
	
	}
	
	header("Location:../../rental/usbs/edit/".$usb_ref."#custom");

?>