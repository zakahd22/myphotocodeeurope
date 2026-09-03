<?
	
	$type = $_REQUEST['type'];
	
	switch ($type)
	{
		
		case "preset" :
			
			$id = $_REQUEST['id'];			
?>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/library/frames/<? echo $id; ?>_1_800.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/library/frames/<? echo $id; ?>_2_800.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/library/frames/<? echo $id; ?>_3_800.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');"><img src='https://www.myphotocode.com/library/frames/<? echo $id; ?>_4_800.png' width="200" height="150" /></div>
			<div style="clear:both;height:26px;"></div>			
<?

			break;
			
		case "usb" :
			
			include("../general.php");
			
			$usbId = $_REQUEST['usbId'];
			$frameNum = $_REQUEST['frameNum'];
			
			$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usbId"));
?>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usbId; ?>/PhotoIdUpload/Frames/<? echo $frameNum; ?>a.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usbId; ?>/PhotoIdUpload/Frames/<? echo $frameNum; ?>b.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');margin-right:26px;"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usbId; ?>/PhotoIdUpload/Frames/<? echo $frameNum; ?>c.png' width="200" height="150" /></div>
			<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usbId; ?>/PhotoIdUpload/Frames/<? echo $frameNum; ?>d.png' width="200" height="150" /></div>
			<div style="clear:both;height:26px;"></div>			
<?
			break;
			
	}

?>