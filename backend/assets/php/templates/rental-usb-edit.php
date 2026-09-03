<?php
	
	//get the photobooth..
	$q = mysql_query("SELECT * FROM booth_types");
	while ($bt = mysql_fetch_array($q))
	{
		if ($bt['char'] == $usb['boothtype_char'])
		{
			$photobooth = $bt;
		}
	}
	
	//////////////////////////////////////////////////////////////////
	// BASIQUES
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "usb")
	{
	
		$rental_id = $_SESSION['rental_id'];
		$title = $_REQUEST['title'];
		$creation_date = $_REQUEST['creation_date'];
		$photobooth_id = $_REQUEST['photobooth_id'];
		$event_id = $_REQUEST['event_id'];
		
		$values = "rental_id=".$rental_id.", title='".$title."', creation_date=".$creation_date.", boothtype_char='".$photobooth_id."', event_id=".$event_id;
		
		if ($usb_id == 0)
		{
			$action = "INSERT INTO usbs SET ";
			$condition = "";
		}
		else
		{
			$action = "UPDATE usbs SET ";
			$condition = " WHERE id=".$usb_id;
		}
		
		mysql_query($action.$values.$condition);
		
		if ($usb_id == 0)
		{
			
			$inserted_usb_id = mysql_insert_id();
			
			mkdir("usbs/".$creation_date.$inserted_usb_id,0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdDownload",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdDownload/myphotocode",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Welcome",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Welcome/Custom",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Welcome/Random",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Bye",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Bye/Custom",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Bye/Random",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdUpload/Frames",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdEvents",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdEvents/CustomShots",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdEvents/Wedding",0777);
			mkdir("usbs/".$creation_date.$inserted_usb_id."/PhotoIdEvents/Wedding/Header",0777);
			
			$event_file = "usbs/".$creation_date.$inserted_usb_id."/PhotoIdDownload/myphotocode/myphotocode.dat";
			
		}
		else
		{
			$event_file = "usbs/".$creation_date.$usb_id."/PhotoIdDownload/myphotocode/myphotocode.dat";
		}
		
		if (file_exists($event_file)) unlink($event_file);
		$fp = fopen($event_file, "w");
		fwrite($fp, $event_id);
		fclose($fp);
		
		$form_succeed = true;
	
	}


	//////////////////////////////////////////////////////////////////
	// LOGO
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "logo")
	{
		
		if(isset($_FILES['logo']))
		{
			
			include("assets/php/class-image.php");
		
			$myImage = new _image;
			$myImage->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/';
			$myImage->newName = 'Logo';
			$myImage->duplicates = 'o';
			$myImage->upscale = 'true';
			$myImage->newImgType = 'jpg';
		    $myImage->imgQuality = '100';

			$res = $myImage->upload($_FILES['logo']);

			if($res)
			{
			    $myImage->newWidth = $photobooth['logo_w'];
			    $myImage->newHeight = $photobooth['logo_h'];
			    $i = $myImage->resize();
			    //echo $i."<br /><img src='https://www.myphotocode.com/".$i."' style='border:1px solid #000;' />";
			}
			
			if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Logo.jpg'))
			{
				mysql_query("UPDATE usbs SET logo=1 WHERE id=$usb_id");
				$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
			}
			
			if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Logo.png')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Logo.png');
			if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Logo.gif')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Logo.gif');
		
		} //isset file logo
		
	}
	
	
	//////////////////////////////////////////////////////////////////
	// TEXT
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "text")
	{
	
		$text = $_REQUEST['text'];
		
		mysql_query("UPDATE usbs SET `text`='$text' WHERE id=$usb_id");
		$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
		
		$create = fopen('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/text.txt', "w");
		$write = fwrite($create, $text, strlen($text));
		$close = fclose($create);
	
	}
	
	
	//////////////////////////////////////////////////////////////////
	// BGMUSIC
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "bgmusic")
	{
	
		if($_FILES['bgmusic']['tmp_name'] != null)
		{	
			
			//if ($_FILES["bgmusic"]["type"] == "audio/mp3" || $_FILES["bgmusic"]["type"] == "audio/mpg" || $_FILES["bgmusic"]["type"] == "audio/mpeg")
			//{
				define("sql_error_reporting",true); 
				
				$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
				move_uploaded_file($_FILES['bgmusic']['tmp_name'], "usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/BGmusic.mp3") or die("upload");
				chmod("usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/BGmusic.mp3", 0777);
				mysql_query("UPDATE usbs set bgmusic=1 where id=$usb_id");
				$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));				
			//}
			//else
			//{
				//echo "<script type='text/javascript'>alert('Unsuported file type. It must be a MP3 file.');</script>";
			//}
			
		}
			
	}
	
	
	//////////////////////////////////////////////////////////////////
	// FRAMES
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "frames")
	{
		
		for ($x = 1; $x <= 12; $x++)
		{
			
			if (isset($_REQUEST['frameSet'.$x]))
			{
				
				$frameSet = $_REQUEST['frameSet'.$x];
				
				if ($frameSet != 0)
				{
					
					mysql_query("UPDATE usbs SET frame$x=$frameSet WHERE id=$usb_id");
					$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
					
					if ($frameSet == 99)
					{
						
						include("assets/php/class-image.php");
						
						for ($ii = 1; $ii <= 4; $ii++)
						{
							
							if($_FILES['frame'.$ii]['tmp_name'] != null)
							{
								
								switch ($ii)
								{
									case 1: $char = "a"; break;
									case 2: $char = "b"; break;
									case 3: $char = "c"; break;
									case 4: $char = "d"; break;
								}
							
								$myImage[$ii] = new _image;
								$myImage[$ii]->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Frames/';
								$myImage[$ii]->newName = $x.$char;
								$myImage[$ii]->duplicates = 'o';
								$myImage[$ii]->upscale = 'true';
								//$myImage[$ii]->padTransparent = 'true';
								$myImage[$ii]->padColour = 'transparent';
								$myImage[$ii]->newImgType = 'png';
							    $myImage[$ii]->imgQuality = '100';
							    
								$res = $myImage[$ii]->upload($_FILES['frame'.$ii]);

								if($res)
								{
								    $myImage[$ii]->newWidth = $photobooth['frames_w'];
								    $myImage[$ii]->newHeight = $photobooth['frames_h'];
								    $i = $myImage[$ii]->resize();
								}

								if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Frames/'.$x.$char.'.jpg')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Frames/'.$x.$char.'.jpg');
								if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Frames/'.$x.$char.'.gif')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Frames/'.$x.$char.'.gif');
							
							}
							
						}						
						
					}
					else
					{	
						
						copy("library/frames/".$frameSet."_1_".$photobooth['frames_w'].".png","usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/Frames/".$x."a.png");
						copy("library/frames/".$frameSet."_2_".$photobooth['frames_w'].".png","usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/Frames/".$x."b.png");
						copy("library/frames/".$frameSet."_3_".$photobooth['frames_w'].".png","usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/Frames/".$x."c.png");
						copy("library/frames/".$frameSet."_4_".$photobooth['frames_w'].".png","usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/Frames/".$x."d.png");
						
					}
					
				}
				
			}
			
		} //end for
			
	}//end form


	//////////////////////////////////////////////////////////////////
	// WELCOME
	//////////////////////////////////////////////////////////////////

	if ($_REQUEST['form_id'] == "welcome")
	{
		
		include("assets/php/class-image.php");
		
		$welcomeNr = $_REQUEST['welcomeNr']; //hidden 
		$welcome_type = $_REQUEST['welcome_type']; //radio
		
		for ($ii = 1; $ii <= $photobooth['screens']; $ii++)
		{
			
			if($_FILES['welcome'.$ii]['tmp_name'] != null)
			{
				
				switch ($ii)
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
				
				$myImage[$ii] = new _image;
				$myImage[$ii]->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Welcome/'.$welcome_type.'/';
				$myImage[$ii]->newName = $welcomeNr.$char;
				$myImage[$ii]->duplicates = 'o';
				$myImage[$ii]->upscale = 'true';
				$myImage[$ii]->newImgType = 'jpg';
			    $myImage[$ii]->imgQuality = '100';

				$res = $myImage[$ii]->upload($_FILES['welcome'.$ii]);

				if($res)
				{
				    $myImage[$ii]->newWidth = $photobooth['welcome_w'];
				    $myImage[$ii]->newHeight = $photobooth['welcome_h'];
				    $i = $myImage[$ii]->resize();
				}

				if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Welcome/'.$welcome_type.'/'.$welcomeNr.$char.'.png')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Welcome/'.$welcome_type.'/'.$welcomeNr.$char.'.png');
				if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Welcome/'.$welcome_type.'/'.$welcomeNr.$char.'.gif')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Welcome/'.$welcome_type.'/'.$welcomeNr.$char.'.gif');
				
				if ($welcome_type == "Custom")
				{
					mysql_query("UPDATE usbs SET welcome=1 WHERE id=$usb_id");
				}
				else
				{
					mysql_query("UPDATE usbs SET welcome$welcomeNr=1 WHERE id=$usb_id");
				}
				
			}

		} //end for
		
		mysql_query("UPDATE usbs SET welcome_type='$welcome_type' WHERE id=$usb_id");
		$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

	}//end if

	//////////////////////////////////////////////////////////////////
	// BYE
	//////////////////////////////////////////////////////////////////

	if ($_REQUEST['form_id'] == "bye")
	{

		include("assets/php/class-image.php");

		$byeNr = $_REQUEST['byeNr']; //hidden 
		$bye_type = $_REQUEST['bye_type']; //radio

		for ($ii = 1; $ii <= $photobooth['screens']; $ii++)
		{

			if($_FILES['bye'.$ii]['tmp_name'] != null)
			{

				switch ($ii)
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

				$myImage[$ii] = new _image;
				$myImage[$ii]->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Bye/'.$bye_type.'/';
				$myImage[$ii]->newName = $byeNr.$char;
				$myImage[$ii]->duplicates = 'o';
				$myImage[$ii]->upscale = 'true';
				$myImage[$ii]->newImgType = 'jpg';
			    $myImage[$ii]->imgQuality = '100';

				$res = $myImage[$ii]->upload($_FILES['bye'.$ii]);

				if($res)
				{
				    $myImage[$ii]->newWidth = $photobooth['welcome_w'];
				    $myImage[$ii]->newHeight = $photobooth['welcome_h'];
				    $i = $myImage[$ii]->resize();
				}

				if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Bye/'.$bye_type.'/'.$byeNr.$char.'.png')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Bye/'.$bye_type.'/'.$byeNr.$char.'.png');
				if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Bye/'.$bye_type.'/'.$byeNr.$char.'.gif')) unlink('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdUpload/Bye/'.$bye_type.'/'.$byeNr.$char.'.gif');

				if ($bye_type == "Custom")
				{
					mysql_query("UPDATE usbs SET bye=1 WHERE id=$usb_id");
				}
				else
				{
					mysql_query("UPDATE usbs SET bye$byeNr=1 WHERE id=$usb_id");
				}

			}

		} //end for

		mysql_query("UPDATE usbs SET bye_type='$bye_type' WHERE id=$usb_id");
		$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

	}//end if
	
	//////////////////////////////////////////////////////////////////
	// BANNER
	//////////////////////////////////////////////////////////////////
	
	if ($_REQUEST['form_id'] == "banner")
	{
		
		if(isset($_FILES['banner']))
		{
			
			include("assets/php/class-image.php");
		
			$myImage = new _image;
			$myImage->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/Wedding/Header/';
			$myImage->newName = '1';
			$myImage->duplicates = 'o';
			$myImage->upscale = 'true';
			$myImage->newImgType = 'jpg';
		    $myImage->imgQuality = '100';

			$res = $myImage->upload($_FILES['banner']);

			if($res)
			{
			    $myImage->newWidth = $photobooth['banner_w'];
			    $myImage->newHeight = $photobooth['banner_h'];
			    $i = $myImage->resize();
			    //echo $i."<br /><img src='https://www.myphotocode.com/".$i."' style='border:1px solid #000;' />";
			}
			
			if (file_exists('usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/Wedding/Header/1.jpg'))
			{
				mysql_query("UPDATE usbs SET banner=1 WHERE id=$usb_id");
				$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));
			}
			
			$file = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/Wedding/Header/1.png'; if (file_exists($file)) unlink($file);
			$file = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/Wedding/Header/1.gif'; if (file_exists($file)) unlink($file);
		
		} //isset file banner
		
	}
	
	//////////////////////////////////////////////////////////////////
	// CUSTOM
	//////////////////////////////////////////////////////////////////

	if ($_REQUEST['form_id'] == "custom")
	{

		include("assets/php/class-image.php");

		$customNr = $_REQUEST['customNr']; //hidden 
		
		if($_FILES['custom']['tmp_name'] != null)
		{

			$myImage[$ii] = new _image;
			$myImage[$ii]->uploadTo = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/CustomShots/';
			$myImage[$ii]->newName = $customNr;
			$myImage[$ii]->duplicates = 'o';
			$myImage[$ii]->upscale = 'true';
			$myImage[$ii]->newImgType = 'jpg';
		    $myImage[$ii]->imgQuality = '100';

			$res = $myImage[$ii]->upload($_FILES['custom']);

			if($res)
			{
			    $myImage[$ii]->newWidth = $photobooth['custom_w'];
			    $myImage[$ii]->newHeight = $photobooth['custom_h'];
			    $i = $myImage[$ii]->resize();
			}

			$file = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/CustomShots/'.$customNr.'.png'; if (file_exists($file)) unlink($file);
			$file = 'usbs/'.$usb['creation_date'].$usb['id'].'/PhotoIdEvents/CustomShots/'.$customNr.'.gif'; if (file_exists($file)) unlink($file);
			
			mysql_query("UPDATE usbs SET custom$customNr=1 WHERE id=$usb_id");

		}

		$usb = mysql_fetch_array(mysql_query("SELECT * FROM usbs WHERE id=$usb_id"));

	}//end if
	
?>

<script type="text/javascript">
	
	$(document).ready(function() {
					
		
		$('.btnUploading').click(function(){

			$("#uploading").fadeIn(300);
			
		});
	
	});
	
</script>

<div id="content">

	<?
	
		if ($usb_id == 0)
		{
			$nav_bar_title = "New";
			$content_title = "Create a new USB Set Up.";
		}
		else
		{
			$nav_bar_title = "Edition";
			$content_title = "Edit the USB Set Up.";
		}
	
	?>
	
	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental/usbs">USB Set Ups</a> <span class="raquo">&raquo;</span> <? echo $nav_bar_title; ?></div>
	
	<div id="title" style="line-height:1.2em;"><? echo $content_title; ?></div>
	
	<div style="margin-top:32px;">
		
		<?
		
		if ($form_succeed)
		{
			
			echo "<div class='succeedMsg'>";
			
			if ($usb_id == 0)
			{
				echo "USB Set Up created successfully.";
				echo "<br /><a href='".$baseUrl."/rental/usbs/edit/".$creation_date.$inserted_usb_id."'>&raquo; Start editing its content.</a>";
			}
			else
			{
				echo "USB Set Up edited successfully.";
				echo "<br /><a href='".$baseUrl."/rental/usbs'>&raquo; Go back to USB Set Ups.</a>";
			} 
			
			echo "</div>";
			
		}
		else
		{
		
		?>
		
		<form method="post" action="<? echo $PHP_SELF; ?>">

			<div class="form_area">
			
					<div class="label">Name</div>
					<div class="sublabel">Used to identify the USB Set Up in the list.</div>
					<div class="textfield"><input type="text" name="title" value="<? echo $usb['title']; ?>" /></div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<div class="label">Photo booth</div>
					<? if ($usb_id == 0) { ?><div class="sublabel">Choose the photo booth model where the USB will be plugged in.</div><? } else { $opacity = "opacity:0.6; filter:alpha(opacity=60);"; } ?>
					<div class="textfield" style="<? echo $opacity; ?>">
						
						<? 
							
							if ($usb_id != 0)
							{
								echo "<input type='hidden' name='photobooth_id' value='".$usb['boothtype_char']."' />";
								echo "<select name='' disabled>";
							}
							else
							{
								echo "<select name='photobooth_id'>";
							}

							$q = mysql_query("SELECT * FROM booth_types");
							while ($bt = mysql_fetch_array($q))
							{
								echo "<option";
								if ($bt['char'] == $usb['boothtype_char'])
								{
									echo " selected";
									$photobooth = $bt;
								}
								echo " value='".$bt['char']."'>".$bt['name']."</option>";		
							}
						
							echo "</select>";

						?>
						
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<div class="label">Online Event</div>
					<div class="sublabel">Is the Photo booth going to be linked to any Online Event?<br />If so, choose it from the list of already created events.</div>
					<div class="textfield">
						
						<? 
							
							echo "<select name='event_id'>";
							
							echo "<option"; if (0 == $usb['event_id']) { echo " selected"; } echo " value='0'>Not linked</option>";		
							echo "<option  value='0'>----------------------------------------------------</option>";		
							
							$q = mysql_query("SELECT * FROM events WHERE rental_id=$rental[id] ORDER BY start_date DESC, id DESC");
							while ($event = mysql_fetch_array($q))
							{
								echo "<option"; if ($event['id'] == $usb['event_id']) { echo " selected"; } echo " value='".$event['id']."'>".$event['title']."</option>";		
							}
							
							echo "</select>";
							
						?>
						
					</div>
					<div style="margin:-10px 0px 20px 0px;"><a href="https://www.myphotocode.com/rental/events/new">&raquo; Create a new Online Event</a></div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

					<div class="button" style="float:left;width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>
					<div class="button" style="float:left;margin:0px 0px 0px 20px;"><a href="<? $baseUrl; ?>/rental/usbs"><img alt="Cancel" src="<? echo $baseUrl; ?>/assets/images/button-cancel.jpg" width="220" height="63" /></a></div>
					<div style="clear:both;"></div>
					
					<? if ($usb_id == 0) { ?>
					<input type="hidden" name="creation_date" value="<? echo date('Ymd'); ?>">
					<? } else { ?>
					<input type="hidden" name="creation_date" value="<? echo $usb['creation_date']; ?>">
					<? } ?>
					<input type="hidden" name="form_id" value="usb">
					
			</div>

		</form>
		
			<?
			
			//////////////////////////////////////////////////////////
			// FORMS
			//////////////////////////////////////////////////////////
			
			if ($usb_id != 0)
			{
				
			?>
				
				<a name="logo"></a>
				<form action="<? echo $_SERVER['REQUEST_URI']."#logo"; ?>" method="post" enctype="multipart/form-data">
					
					<div class="form_area">

						<div class="label">Logo</div>
						
						<? if ($usb['logo']) { ?>

							<div class="textfield" style="margin-top:24px;">
								<div style="float:left;width:200px;height:150px;background:#eee;"><img src="<? echo $baseUrl; ?>/usbs/<? echo $usb['creation_date'].$usb['id']; ?>/PhotoIdUpload/Logo.jpg" width="200" height="150"/></div>
								<div style="clear:both;height:24px;"></div>
							</div>
							
							<div class="textfield">
								<a href="<? echo $baseUrl; ?>/assets/php/delete-image.php?id=logo&usb_id=<? echo $usb_id; ?>">&raquo; Delete image</a>
							</div>
						
						<? } else { ?>
							
							<div class="sublabel">Upload your image (This will be resized to <? echo $photobooth['logo_w']."x".$photobooth['logo_h']; ?>px).</div>
							
							<div class="textfield"><?/*><input type="hidden" name="MAX_FILE_SIZE" value="10" />*/?><input type="file" name="logo" /></div>

							<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

							<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

							<input type="hidden" name="form_id" value="logo">						
						
						<? } ?>

					</div>

				</form>
				
				<a name="text"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#text"; ?>">

					<div class="form_area">

						<div class="label">Text</div>
						<div class="sublabel">This is the text line which will be printed on a side of each print.</div>
						<div class="textfield"><input style="width:880px;" type="text" name="text" value="<? echo $usb['text']; ?>" /></div>

						<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

						<div class="button" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

						<input type="hidden" name="form_id" value="text">

					</div>

				</form>
				
				<a name="bgmusic"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#bgmusic"; ?>" enctype="multipart/form-data">

					<div class="form_area">

						<div class="label">Background music</div>
						<div class="sublabel">You can set any .mp3 file to be played during the game. (Max. 5Mb)</div>
						
						<?
						
						if (!$usb['bgmusic'])
						{
							
						?>
						
							<div class='textfield'><input type='file' name='bgmusic' /></div>
							
							<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

							<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

							<input type="hidden" name="form_id" value="bgmusic">
						
						<?
							
						}
						else
						{
							echo "<div class='textfield' style='margin-top:26px;'>";
							$mp3 = "https://www.myphotocode.com/usbs/".$usb['creation_date'].$usb['id']."/PhotoIdUpload/BGmusic.mp3";
							
							echo '<object type="application/x-shockwave-flash" data="https://www.myphotocode.com/assets/swf/dewplayer-mini.swf?mp3='.$mp3.'" width="160" height="20" id="dewplayer-mini">';
							echo '	<param name="wmode" value="transparent" />';
							echo '	<param name="movie" value="https://www.myphotocode.com/assets/swf/dewplayer-mini.swf?mp3='.$mp3.'" />';
							echo '</object>';
							
							echo "<br /><br /><a href='https://www.myphotocode.com/assets/php/delete-bgmusic.php?usb=".$usb['id']."'>&raquo; Delete</a></div>";
						}
						
						?>

					</div>

				</form>
				
				<a name="frames"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#frames"; ?>" enctype="multipart/form-data">

					<div class="form_area">

						<div class="label">Frames</div>
						<div class="sublabel">Choose any premade frames or upload your own. (New frames will be resized to <? echo $photobooth['frames_w']."x".$photobooth['frames_h']; ?>px.)</div>
						
						<?
						echo "<script type='text/javascript'>
							
							$(document).ready(function() {";
								
								for ($x = 1; $x <=12; $x++) {
									
									$xNext = $x+1;
									
									echo "
									$('#framesSelect".$x."').change(function(){
									
										$('#framesSample".$x."').fadeOut();
										$('#framesUpload".$x."').fadeOut();
										$('#framesContent".$xNext."').fadeOut();
									
										if ($('#framesSelect".$x."').val() == 99)
										{
											$('#framesUpload".$x."').fadeIn();
											$('#framesContent".$xNext."').fadeIn();
										}
										else
										{	
										 	if($('#framesSelect".$x."').val() != 0)				
											{
												$('#framesSample".$x."').load('https://www.myphotocode.com/assets/php/templates/frames-display.php?type=preset&id='+$('#framesSelect".$x."').val());
												$('#framesSample".$x."').fadeIn();
												$('#framesContent".$xNext."').fadeIn();
											}
										}
									
									});";
								
								}
								
							echo "});

						</script>";
						?>
						
						<?
						
						$lastFrame = 1;
						for ($x = 1; $x <= 12; $x++)
						{
							
							if ($usb['frame'.$x] != 0)
							{
								
								$lastFrame++;
							
						?> 
						
						<div id="framesContent<? echo $x; ?>">
								
							<div class="textfield" style="margin-top:12px;margin-bottom:26px;">
								
								<?
								switch($x)
								{
									case 1: echo "Set #1 on Screen 1"; break;
									case 2: echo "Set #2 on Screen 1"; break;
									case 3: echo "Set #3 on Screen 1"; break;
									case 4: echo "Set #4 on Screen 1"; break;
									case 5: echo "Set #5 on Screen 2"; break;
									case 6: echo "Set #6 on Screen 2"; break;
									case 7: echo "Set #7 on Screen 2"; break;
									case 8: echo "Set #8 on Screen 2"; break;
									case 9: echo "Set #9 on Screen 3"; break;
									case 10: echo "Set #10 on Screen 3"; break;
									case 11: echo "Set #11 on Screen 3"; break;
									case 12: echo "Set #12 on Screen 3"; break;
								}
									
									echo "<br />";
												
									if($usb['frame'.$x] == 99)
									{
										echo "Custom frames ";
									}
									else
									{
										$frameId = $usb['frame'.$x];
										$frame = mysql_fetch_array(mysql_query("SELECT * FROM frames WHERE id=$frameId"));
										echo $frame['title']." ";
									}
								
									?>
								
								</select>
							
								<a href="https://www.myphotocode.com/assets/php/delete-frameset.php?usb_id=<? echo $usb['id']; ?>&set=<? echo $x; ?>">&raquo; Delete.</a>
							
							</div>
						
							<div class="textfield" style="margin-top:-6px;" id="framesSample<? echo $x; ?>"></div>					
							
							<?
							echo "<script type='text/javascript'>";
							echo "$('#framesSample".$x."').load('https://www.myphotocode.com/assets/php/templates/frames-display.php?type=usb&usbId=".$usb['id']."&frameNum=".$x."');";
							echo "</script>";
							?>
							
							<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
							
						</div>
						
						<? } ?>
						
						<? } ?>
						
						<?
						
						if ($lastFrame <= 12)
						{
							
							$x = $lastFrame++;
							
							?>
							
							<div id="framesContent<? echo $x; ?>">

								<div class="textfield" style="margin-top:12px;margin-bottom:26px;">

									<?
									switch($x)
									{
										case 1: echo "Set #1 on Screen 1: "; break;
										case 2: echo "Set #2 on Screen 1: "; break;
										case 3: echo "Set #3 on Screen 1: "; break;
										case 4: echo "Set #4 on Screen 1: "; break;
										case 5: echo "Set #5 on Screen 2: "; break;
										case 6: echo "Set #6 on Screen 2: "; break;
										case 7: echo "Set #7 on Screen 2: "; break;
										case 8: echo "Set #8 on Screen 2: "; break;
										case 9: echo "Set #9 on Screen 3: "; break;
										case 10: echo "Set #10 on Screen 3: "; break;
										case 11: echo "Set #11 on Screen 3: "; break;
										case 12: echo "Set #12 on Screen 3: "; break;
									}
									?>						

									<select name="frameSet<? echo $x; ?>" style="width:424px; margin:0px 24px 0px 20px;" id="framesSelect<? echo $x; ?>">

										<?

										echo "<option value='0'>None</option>";

										echo "<option value='0'>-----------------------</option>";

										echo "<option "; if($usb['frame'.$x] == 99) { echo "selected "; } echo "value='99'>Upload custom</option>";

										echo "<option value='0'>-----------------------</option>";
										
										$q = mysql_query("SELECT * FROM frames ORDER BY ord ASC");
										while ($frame = mysql_fetch_array($q))
										{
											echo "<option "; if($usb['frame'.$x] == $frame['id']) { echo "selected "; } echo "value='".$frame['id']."'>".$frame['title']."</option>";
										}

										?>

									</select>

									<a href="https://www.myphotocode.com/assets/images/frames-layout.jpg" target="_blank">&raquo; View layout.</a>

								</div>

								<div class="textfield" style="margin-top:-6px;display:none;" id="framesSample<? echo $x; ?>"></div>

								<div class="textfield" style="margin-top:-6px;margin-bottom:26px; display:none;" id="framesUpload<? echo $x; ?>">
									<input type="file" name="frame1" /><br />
									<input type="file" name="frame2" /><br />
									<input type="file" name="frame3" /><br />
									<input type="file" name="frame4" />
								</div>

								<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

							</div>

							<? } ?>
							
					

						<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

						<input type="hidden" name="form_id" value="frames">

					</div>

				</form>

				<a name="welcome"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#welcome"; ?>" enctype="multipart/form-data">

					<div class="form_area">

						<div class="label">Welcome screen</div>
						<div class="sublabel"><? echo $photobooth['welcome_w']."x".$photobooth['welcome_h']; ?>px image<? if ($photobooth['screens'] > 1) echo "s"; ?> which will be displayed when the game starts.</div>
						
						<div style="margin-top:24px;">Choose image display type.</div>

						<div class="textfield" style="margin-top:20px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
							<? echo "<input onChange='this.form.submit();' style='' type='radio' "; if ($usb['welcome_type'] == 'Custom') { echo "checked "; } echo "name='welcome_type' value='Custom' /> Single"; ?>
							<? echo "<input onChange='this.form.submit();' style='margin-left:24px;' type='radio' "; if ($usb['welcome_type'] == 'Random') { echo "checked "; } echo "name='welcome_type' value='Random' /> Random"; ?>
						</div>
						
						<div style="border-top:1px dotted #b42f56;width:100%;height:18px;"></div>
						
						<?
							$numWelcomes = 1;
							if ($usb['welcome_type'] == "Random") $numWelcomes = 10;
						?>
						
						<? for ($x = 1; $x <= $numWelcomes; $x++) { ?>
							
							<?
								if ($numWelcomes  == 1)
								{
									$checkWelcome = $usb['welcome'];
								}
								else
								{
									$checkWelcome = $usb['welcome'.$x];
								}
							?>
							<? if ($checkWelcome) { ?>
								
								<? $lastWelcome = $x; ?>
								
								<div style="margin-bottom:10px;">
									<? if($numWelcomes > 1) { echo "Welcome #".$lastWelcome.": "; } else { echo "Images: "; } ?>
									<a href="https://www.myphotocode.com/assets/php/delete-welcomes.php?usb_id=<? echo $usb['id']; ?>&set=<? echo $x; ?>">&raquo; Delete.</a>
								</div>
								
								<? for ($xx = 1; $xx <= $photobooth['screens']; $xx++) { ?>

									<?
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
									?>									
									
									<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');<? if ($xx < 4) echo "margin-right:26px;"; ?>"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usb['id']; ?>/PhotoIdUpload/Welcome/<? echo $usb['welcome_type']?>/<? echo $x; ?><? echo $char; ?>.jpg' width="200" height="150" /></div>
									
								<? } ?>
								<div style="clear:both;height:26px;"></div>
						
								<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
							
							<? } ?>
						
						<? } ?>
						
						<? $lastWelcome++; ?>
						
						<? if ($lastWelcome <= $numWelcomes) { ?>
							<div style="margin-bottom:10px;">Welcome #<? echo $lastWelcome; ?>:</div>
							<?
								echo "<div class='textfield'><input type='hidden' name='welcomeNr' value='".$lastWelcome."' /></div>";
								for ($xx = 1; $xx <= $photobooth['screens']; $xx++)
								{
									echo "<div class='textfield'><input type='file' name='welcome".$xx."' /></div>";
								}
							?>
						<? } ?>
						
						<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

						<input type="hidden" name="form_id" value="welcome">

					</div>

				</form>
				
				
				<a name="bye"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#bye"; ?>" enctype="multipart/form-data">

					<div class="form_area">

						<div class="label">Bye screen</div>
						<div class="sublabel"><? echo $photobooth['welcome_w']."x".$photobooth['welcome_h']; ?>px image<? if ($photobooth['screens'] > 1) echo "s"; ?> which will be displayed when the game finishes.</div>

						<div style="margin-top:24px;">Choose image display type.</div>

						<div class="textfield" style="margin-top:20px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
							<? echo "<input onChange='this.form.submit();' style='' type='radio' "; if ($usb['bye_type'] == 'Custom') { echo "checked "; } echo "name='bye_type' value='Custom' /> Single"; ?>
							<? echo "<input onChange='this.form.submit();' style='margin-left:24px;' type='radio' "; if ($usb['bye_type'] == 'Random') { echo "checked "; } echo "name='bye_type' value='Random' /> Random"; ?>
						</div>

						<div style="border-top:1px dotted #b42f56;width:100%;height:18px;"></div>

						<?
							$numByes = 1;
							if ($usb['bye_type'] == "Random") $numByes = 10;
						?>

						<? for ($x = 1; $x <= $numByes; $x++) { ?>

							<?
								if ($numByes  == 1)
								{
									$checkBye = $usb['bye'];
								}
								else
								{
									$checkBye = $usb['bye'.$x];
								}
							?>
							<? if ($checkBye) { ?>

								<? $lastBye = $x; ?>

								<div style="margin-bottom:10px;">
									<? if($numByes > 1) { echo "Bye #".$lastBye.": "; } else { echo "Images: "; } ?>
									<a href="https://www.myphotocode.com/assets/php/delete-byes.php?usb_id=<? echo $usb['id']; ?>&set=<? echo $x; ?>">&raquo; Delete.</a>
								</div>

								<? for ($xx = 1; $xx <= $photobooth['screens']; $xx++) { ?>

									<?
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
									?>									

									<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');<? if ($xx < 4) echo "margin-right:26px;"; ?>"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usb['id']; ?>/PhotoIdUpload/Bye/<? echo $usb['bye_type']?>/<? echo $x; ?><? echo $char; ?>.jpg' width="200" height="150" /></div>

								<? } ?>
								<div style="clear:both;height:26px;"></div>

								<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

							<? } ?>

						<? } ?>

						<? $lastBye++; ?>

						<? if ($lastBye <= $numByes) { ?>
							<div style="margin-bottom:10px;">Bye #<? echo $lastBye; ?>:</div>
							<?
								echo "<div class='textfield'><input type='hidden' name='byeNr' value='".$lastBye."' /></div>";
								for ($xx = 1; $xx <= $photobooth['screens']; $xx++)
								{
									echo "<div class='textfield'><input type='file' name='bye".$xx."' /></div>";
								}
							?>
						<? } ?>

						<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

						<input type="hidden" name="form_id" value="bye">

					</div>

				</form>
				
				<? if ($photobooth['banner_w'] > 0) { ?>
					
					<a name="banner"></a>
					<form action="<? echo $_SERVER['REQUEST_URI']."#banner"; ?>" method="post" enctype="multipart/form-data">
					
						<div class="form_area">

							<div class="label">Top screen banner (Wedding version)</div>
						
							<? if ($usb['banner']) { ?>

								<div class="textfield" style="margin-top:24px;">
									<div style="float:left;width:200px;height:150px;background:#eee;"><img src="<? echo $baseUrl; ?>/usbs/<? echo $usb['creation_date'].$usb['id']; ?>/PhotoIdEvents/Wedding/Header/1.jpg" width="200" height="150"/></div>
									<div style="clear:both;height:24px;"></div>
								</div>
							
								<div class="textfield">
									<a href="<? echo $baseUrl; ?>/assets/php/delete-image.php?id=banner&usb_id=<? echo $usb_id; ?>">&raquo; Delete image</a>
								</div>
						
							<? } else { ?>
							
								<div class="sublabel">A <? echo $photobooth['banner_w']."x".$photobooth['banner_h']; ?>px image which will be placed on the top screen.</div>
								<!--<div class="sublabel"><a href="">&raquo; Download template</a></div>-->
							
								<div class="textfield"><?/*><input type="hidden" name="MAX_FILE_SIZE" value="10" />*/?><input type="file" name="banner" /></div>

								<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

								<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

								<input type="hidden" name="form_id" value="banner">						
						
							<? } ?>

						</div>

					</form>
				
				<? } ?>				
								
				<a name="custom"></a>
				<form method="post" action="<? echo $_SERVER['REQUEST_URI']."#custom"; ?>" enctype="multipart/form-data">

					<div class="form_area">

						<div class="label">Custom images on demo screen</div>
						<div class="sublabel"><? echo $photobooth['custom_w']."x".$photobooth['custom_h']; ?>px images which will be randomly displayed on the demo.</div>

						<? for ($x = 1; $x <= 12; $x++) { ?>

							<? if ($usb['custom'.$x]) { ?>
								
								<? $last = $x; ?>
								
								<div style="margin-bottom:10px;">
									Custom image #<? echo $x; ?>
									<a href="https://www.myphotocode.com/assets/php/delete-custom.php?usb_id=<? echo $usb['id']; ?>&set=<? echo $x; ?>">&raquo; Delete.</a>
								</div>

								<div style="float:left;width:200px;height:150px;background:url('https://www.myphotocode.com/assets/images/transparent-bg.jpg');<? if ($xx < 4) echo "margin-right:26px;"; ?>"><img src='https://www.myphotocode.com/usbs/<? echo $usb['creation_date'].$usb['id']; ?>/PhotoIdEvents/CustomShots/<? echo $x; ?>.jpg' width="200" height="150" /></div>

								<div style="clear:both;height:26px;"></div>

								<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

							<? } ?>

						<? } ?>

						<? $last++; ?>

						<? if ($last <= 12) { ?>
							<div style="margin-bottom:10px;">Custom image #<? echo $last; ?>:</div>
							<?
								echo "<div class='textfield'><input type='hidden' name='customNr' value='".$last."' /></div>";
								echo "<div class='textfield'><input type='file' name='custom' /></div>";
							?>
						<? } ?>

						<div class="button btnUploading" style="width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>

						<input type="hidden" name="form_id" value="custom">

					</div>

				</form>
				
			<?
			
			}
			
			//////////////////////////////////////////////////////////
			
			echo "<div class='succeedMsg' style='margin-bottom:42px;'>";
			
			echo "<a href='".$baseUrl."/rental/usbs'>&raquo; Go back to USB Set Ups.</a><br />";
			echo "<a href='https://myphotocode.com/assets/php/download-usb.php?usb_id=".$usb['creation_date'].$usb['id']."'>&raquo; Download the USB Set Up into a USB Drive.</a>";
			
			echo "</div>";
			
		} //form succeed
		
		?>
		
	</div>
	
</div> <? //id=content ?>

<div id="uploading"><table width="100%" height="100%"><tr><td align="center" valign="middle"><span style="font-size:42px;font-weight:bold;">Uploading, please wait!</span><br /><br /><img src="<? echo $baseUrl; ?>/assets/images/uploading.gif" width="220" height="19" /><br /></td></tr></table></div>