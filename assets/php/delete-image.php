<?
    require_once dirname (__FILE__) . "/common/global.php";
    include G_PATH.'common/general.php';
		
	$id = $_REQUEST['id'];
	
	switch ($id)
	{
		
		case "logo" :
		
			$usb_id = $_REQUEST['usb_id'];
			$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

			if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");
			
			$usb_ref = $usb['creation_date'].$usb['id'];
			
			unlink("../../usbs/".$usb_ref."/PhotoIdUpload/Logo.jpg");			
		
			mysql_query("UPDATE usbs SET logo=0 WHERE id=$usb_id");
			
			header("Location:../../rental/usbs/edit/".$usb_ref."#logo");
		
			break;
			
		case "banner" :

			$usb_id = $_REQUEST['usb_id'];
			$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

			if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");

			$usb_ref = $usb['creation_date'].$usb['id'];

			unlink("../../usbs/".$usb_ref."/PhotoIdEvents/Wedding/Header/1.jpg");			

			mysql_query("UPDATE usbs SET banner=0 WHERE id=$usb_id");

			header("Location:../../rental/usbs/edit/".$usb_ref."#banner");

			break;
			
		case "background" :
			
			$event = $_REQUEST['event'];
			$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$event"));
			$event_ref = $event['start_date'].$event['id'];
			
			unlink("../../events/".$event_ref."/background.jpg");			

			header("Location:../../rental/events/edit/".$event_ref);

			break;
		
	}

?>