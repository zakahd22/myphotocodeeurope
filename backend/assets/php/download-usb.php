<?PHP
	
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
	// FILENAMES & CONTENT
	///////////////////////////////////////
	
	$directoryToZip = "../../usbs/".$usb_folder."/";	
	$zipFileName = $directoryToZip."save-in-usb.zip";
	if (file_exists($zipFileName)) unlink($zipFileName);

	
	///////////////////////////////////////
	// ZIP
	///////////////////////////////////////
	
	// increase script timeout value
	ini_set("max_execution_time", 300);

	// create object
	$zip = new ZipArchive();

	// open archive
	if ($zip->open($zipFileName, ZIPARCHIVE::CREATE) !== TRUE){
		die ("Could not open archive");
	}

	// initialize an iterator
	// pass it the directory to be processed
	$iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($directoryToZip));

	// iterate over the directory
	// add each file found to the archive
	foreach ($iterator as $key=>$value){
		$keyZip = str_replace($directoryToZip,"",$key);
		$zip->addFile(realpath($key), $keyZip) or die ("ERROR: Could not add file: ".$keyZip);
	}
	
	// close and save archive
	$zip->close();
		
	//echo "Archive created successfully.";
	header("Location: ". G_PAGE ."usbs/".$usb_folder."/save-in-usb.zip");
	
?>