<?
	
	///////////////////////////////////////
	// SECURITY
	///////////////////////////////////////
	
	require_once dirname (__FILE__) . "/common/global.php";
        include G_PATH.'common/general.php';
	
	$usb_folder = $_REQUEST['usb_id'];
	$usb_id = substr($usb_folder,8);
	$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
	
	if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:../../index.php");


	///////////////////////////////////////
	// FAKE DELETE
	///////////////////////////////////////
	
	mysql_query("UPDATE usbs SET available=0 WHERE id=$usb_id");
	
	header("Location:../../rental/usbs");
	
?>