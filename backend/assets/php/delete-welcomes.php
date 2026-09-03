<?

	require_once dirname (__FILE__) . "/common/global.php";
        include G_PATH.'common/general.php';
	
	$usb_id = $_REQUEST['usb_id'];
	$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
	$set = $_REQUEST['set'];

	//if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");
	
	$photobooth = mysql_fetch_array(mysql_query("SELECT * FROM booth_types WHERE `char`='$usb[boothtype_char]'"));
	
	$usb_ref = $usb['creation_date'].$usb['id'];
	
	for ($x = 1; $x <= $photobooth['screens']; $x++)
	{
		switch ($x)
		{
			case 1: $char = "a"; break;
			case 2: $char = "b"; break;
			case 3: $char = "c"; break;
			case 4: $char = "d"; break;
			case 5: $char = "e"; break;
			case 6: $char = "f"; break;
			case 7: $char = "g"; break;
			case 8: $char = "h"; break;
			case 9: $char = "i"; break;
			case 10: $char = "j"; break;
			case 11: $char = "k"; break;
			case 12: $char = "l"; break;
		}
		if ($photobooth['screens'] == 1) $char = "";
		
		$file = "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb['welcome_type']."/".$set.$char.".jpg";
		if (file_exists($file)) unlink($file);			
		
	}
	
	if ($usb['welcome_type'] == "Custom")
	{
		mysql_query("UPDATE usbs SET welcome=0 WHERE id=$usb_id");
	}
	else
	{
		mysql_query("UPDATE usbs SET welcome$set=0 WHERE id=$usb_id");
		
		for ($x = ($set+1); $x < 10; $x++)
		{
		
			if ($usb['welcome'.$x] != 0)
			{
			
				$prev = $x - 1;
			
				for ($xx = 1; $xx <= $photobooth['screens']; $xx++)
				{
					
					switch ($xx)
					{
						case 1: $char = "a"; break;
						case 2: $char = "b"; break;
						case 3: $char = "c"; break;
						case 4: $char = "d"; break;
						case 5: $char = "e"; break;
						case 6: $char = "f"; break;
						case 7: $char = "g"; break;
						case 8: $char = "h"; break;
						case 9: $char = "i"; break;
						case 10: $char = "j"; break;
						case 11: $char = "k"; break;
						case 12: $char = "l"; break;
					}
					if ($photobooth['screens'] == 1) $char = "";
					
					$file = "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb['welcome_type']."/".$x.$char.".jpg";
					if (file_exists($file)) rename($file, "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb['welcome_type']."/".$prev.$char.".jpg");
								
				}
			
				mysql_query("UPDATE usbs SET welcome$prev=1 WHERE id=$usb_id");
				mysql_query("UPDATE usbs SET welcome$x=0 WHERE id=$usb_id");
			
			}
		
		}

	}
	
	header("Location:../../rental/usbs/edit/".$usb_ref."#welcome");

?>