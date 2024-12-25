<?
include '../../sessio.php';
include '../../conexio.php';

	
	$usb_id = $_REQUEST['usb_id'];
        $CLD_CON2 = clone($CLD_CON);
        $set = $_REQUEST['set'];
        $CLD_CON->OpenRs("SELECT * FROM usbs WHERE id = $usb_id");
        if ($CLD_CON->FetchArray()) {
            $usb_ref = $CLD_CON->GetArrayField("creation_date") . "" . $usb_id;
            $usb_welcome_type= $CLD_CON->GetArrayField("welcome_type");
            $boothType = $CLD_CON->GetArrayField("boothtype_char");
            $CLD_CON2->OpenRs("SELECT * FROM booth_types WHERE `char`='$boothType'");
            $CLD_CON2->FetchArray();
            $screens = $CLD_CON2->GetArrayField("screens");
                    
	
	
	
	
	for ($x = 1; $x <=  $screens; $x++)
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
		if ( $screens == 1) $char = "";
		
		$file = "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb_welcome_type."/".$set.$char.".jpg";
		if (file_exists($file)) unlink($file);			
		
	}
	
	if ( $usb_welcome_type == "Custom")
	{
		$CLD_CON2->Execute("UPDATE usbs SET welcome=0 WHERE id=$usb_id");
	}
	else
	{
		$CLD_CON2->Execute("UPDATE usbs SET welcome$set=0 WHERE id=$usb_id");
		
		for ($x = ($set+1); $x < 10; $x++)
		{
		$welcome = $CLD_CON->GetArrayField("welcome$x");
			if ($welcome != 0)
			{
			
				$prev = $x - 1;
			
				for ($xx = 1; $xx <= $screens; $xx++)
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
					if ($screens == 1) $char = "";
					
					$file = "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb_welcome_type."/".$x.$char.".jpg";
					if (file_exists($file)) rename($file, "../../usbs/".$usb_ref."/PhotoIdUpload/Welcome/".$usb_welcome_type."/".$prev.$char.".jpg");
								
				}
			
				$CLD_CON2->Execute("UPDATE usbs SET welcome$prev=1 WHERE id=$usb_id");
				$CLD_CON2->Execute("UPDATE usbs SET welcome$x=0 WHERE id=$usb_id");
			
			}
		
		}

	}
	        }
	echo $usb_id;

?>