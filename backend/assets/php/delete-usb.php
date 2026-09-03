<?
	
	///////////////////////////////////////
	// SECURITY
	///////////////////////////////////////
	
	require_once "../../common/global.php";
        include G_PATH.'common/general.php';
	
	$usb_folder = $_REQUEST['usb_id'];
	$usb_id = substr($usb_folder,8);
	$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
	
	if ($usb['rental_id'] != $_SESSION['owner_id']) header("Location:".G_PATH."index.php");


	///////////////////////////////////////
	// FAKE DELETE
	///////////////////////////////////////
	
	mysql_query("UPDATE usbs SET available=0 WHERE id=$usb_id");
	
	header("Location:../../rental/usbs");
	
?>