<?
    // NO es cridat
	trace("event id: ".$event_id."<br />");

	if ($_REQUEST['form_id'] == "event")
	{
	
		trace("form event (y)<br />");
	
		$rental_id = $_SESSION['rental_id'];
		$title = $_REQUEST['title'];
		$background_id = $_REQUEST['background_id'];
		$private = $_REQUEST['private'];
		$autocreated = $_REQUEST['autocreated'];
		$available = $_REQUEST['available'];
		
		$start_month = $_REQUEST['month'];
		if ($start_month < 10) $start_month = "0".$start_month;
		$start_day = $_REQUEST['day'];
		if ($start_day < 10) $start_day = "0".$start_day;
		$start_date = $_REQUEST['year'].$start_month.$start_day;
		
		$values = "rental_id=".$rental_id.", start_date=".$start_date.", title='".$title."', background_id=".$background_id.", private=".$private.", autocreated=".$autocreated.", available=".$available;
		
		if ($event_id == 0)
		{
			$action = "INSERT INTO events SET ";
			$condition = "";
		}
		else
		{
			$action = "UPDATE events SET ";
			$condition = " WHERE id=".$event_id;
		}
		
		trace($action.$values.$condition);
		mysql_query($action.$values.$condition);
		
		if ($event_id == 0)
		{
			$event_id = mysql_insert_id();
			mkdir("events/".$start_date.$event_id,0777);
		}
		
		if($_FILES['image_url']['tmp_name'] != "")
		{
			
			include("assets/php/class-image.php");
		
			$myImage = new _image;
			$myImage->uploadTo = 'events/'.$start_date.$event_id.'/';
			$myImage->newName = 'background';
			$myImage->duplicates = 'o';
			$myImage->upscale = 'true';
			$myImage->newImgType = 'jpg';
		    $myImage->imgQuality = '100';

			$i = $myImage->upload($_FILES['image_url']);
			
			if (file_exists('events/'.$start_date.$event_id.'background.png')) unlink('events/'.$start_date.$event_id.'background.png');
			if (file_exists('events/'.$start_date.$event_id.'background.gif')) unlink('events/'.$start_date.$event_id.'background.gif');
		
		} //isset file logo
		
		$form_succeed = true;
	
	}
	else
	{
		trace("form event (n)<br />");	
	}
		
?>

<div id="content">

	<?
	
		if ($event_id == 0)
		{
			$nav_bar_title = "New";
			$content_title = "Create a new Online Event.";
		}
		else
		{
			$nav_bar_title = "Edition";
			$content_title = "Edit the Online Event.";
		}
	
	?>

	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental/events">Online Events</a> <span class="raquo">&raquo;</span> <? echo $nav_bar_title; ?></div>
	<div id="title" style="line-height:1.2em;"><? echo $content_title; ?></div>
	
	<div style="margin-top:32px;">
		
		<?
		
		if ($form_succeed)
		{
			
			echo "<div class='succeedMsg'>";
			
			if ($event_id == 0) { echo "Event created succesfully."; } else { echo "Online Event edited successfully."; } 
			echo "<br /><a href='".$baseUrl."/rental/events'>&raquo; Go back to Online Events.</a>";
			
			echo "</div>";
			
		}
		else
		{
		
		?>
		
		<form method="post" action="<? echo $PHP_SELF; ?>" enctype="multipart/form-data">

			<div class="form_area">
			
					<div class="label">Event title</div>
					<div class="textfield"><input type="text" name="title" value="<? echo $event['title']; ?>" /></div>

					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

					<div class="label">Start date</div>
					<div class="textfield">
						
						<?php
						
							$event_year = substr($event['start_date'],0,4);
							$event_month = substr($event['start_date'],4,2);
							$event_day = substr($event['start_date'],6);
						
						?>
						
						
						<?php
							
							echo "<select "; if ($event_id != 0) { echo "disabled"; } else { echo "name='day'"; } echo ">";
							
							for ($x = 1; $x < 32; $x++) {

								if ($x == $event_day || ($event_id == 0 && $x == date('d'))) { $addSelect = "selected"; } else { $addSelect = ''; }
								echo "<option $addSelect value=$x>$x</option>";

							}

						?>
						</select>

						<?php 
						echo "<select "; if ($event_id != 0) { echo "disabled"; } else { echo "name='month'"; } echo ">";
						
							echo "<option"; if (1 == $event_month || ($event_id == 0 && 1 == date('m'))) { echo " selected"; } echo " value=1>January</option>";
							echo "<option"; if (2 == $event_month || ($event_id == 0 && 2 == date('m'))) { echo " selected"; } echo " value=2>February</option>";
							echo "<option"; if (3 == $event_month || ($event_id == 0 && 3 == date('m'))) { echo " selected"; } echo " value=3>March</option>";
							echo "<option"; if (4 == $event_month || ($event_id == 0 && 4 == date('m'))) { echo " selected"; } echo " value=4>April</option>";
							echo "<option"; if (5 == $event_month || ($event_id == 0 && 5 == date('m'))) { echo " selected"; } echo " value=5>May</option>";
							echo "<option"; if (6 == $event_month || ($event_id == 0 && 6 == date('m'))) { echo " selected"; } echo " value=6>June</option>";
							echo "<option"; if (7 == $event_month || ($event_id == 0 && 7 == date('m'))) { echo " selected"; } echo " value=7>July</option>";
							echo "<option"; if (8 == $event_month || ($event_id == 0 && 8 == date('m'))) { echo " selected"; } echo " value=8>August</option>";
							echo "<option"; if (9 == $event_month || ($event_id == 0 && 9 == date('m'))) { echo " selected"; } echo " value=9>September</option>";
							echo "<option"; if (10 == $event_month || ($event_id == 0 && 10 == date('m'))) { echo " selected"; } echo " value=10>October</option>";
							echo "<option"; if (11 == $event_month || ($event_id == 0 && 11 == date('m'))) { echo " selected"; } echo " value=11>November</option>";
							echo "<option"; if (12 == $event_month || ($event_id == 0 && 12 == date('m'))) { echo " selected"; } echo " value=12>December</option>";

						?>
						</select>

						<?php 
						echo "<select  "; if ($event_id != 0) { echo "disabled"; } else { echo "name='year'"; } echo ">";
						
							for ($x = date('Y'); $x < date('Y')+5; $x++) {

								if ($x == $event_year || ($event_id == 0 && $x == date('Y'))) { $addSelect = "selected"; } else { $addSelect = ''; }
								echo "<option $addSelect value=$x>$x</option>";

							}

						?>
						</select>
						
						<?php
						
						if ($event_id != 0)
						{
							echo "<input type='hidden' name='day' value='".(int)$event_day."' />";
							echo "<input type='hidden' name='month' value='".(int)$event_month."' />";
							echo "<input type='hidden' name='year' value='".(int)$event_year."' />";
						}
						
						?>
						
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<script type='text/javascript'>
						
						$(document).ready(function() {

							$('.background_selector').change(function(){

								var value = $(this).val();
								
								if (value == 99)
								{
									$('#custom_bg').show();
								}
								else
								{
									$('#custom_bg').hide();
								}

							});

						});

					</script>
					
					<div class="label">Background</div>
					<div class="textfield">
						<select name="background_id" class='background_selector'>
						<?php
							
							echo "<option "; if(!$event['background_id']) { echo "selected "; } echo "value='0'>None</option>";
							echo "<option value='0'>---------------------------------</option>";
							echo "<option "; if($event['background_id'] == 99) { echo "selected "; } echo "value='99'>Upload custom</option>";
							echo "<option value='0'>---------------------------------</option>";
							
							$q = mysql_query("SELECT * FROM event_backgrounds");
							while ($bg = mysql_fetch_array($q))
							{
								if($bg['rental_id'] == 0 || $bg['rental_id'] == $rental_id)
								{
									echo "<option"; if ($bg['id'] == $event['background_id']) { echo " selected"; } echo " value='".$bg['id']."'>".$bg['title']."</option>";		
								}
							}
							
						?>
						</select>
					</div>
					
					<?
					if ($event['background_id'] == 99)
					{
						echo "<div id='custom_bg' style='display:block;'>";
					}
					else
					{
						echo "<div id='custom_bg' style='display:none;'>";
					}
					?>
						<?php
						$file = "events/".$event['start_date'].$event['id']."/background.jpg";
						if (file_exists($file))
						{
							echo "<img src='".$baseUrl.'/'.$file."' width='200' /><br /><a href='".$baseUrl."/assets/php/delete_image.php?id=background&event=".$event['id']."'>Delete</a>";
						}
						else
						{
							echo "<input type='file' name='image_url' />";
						}
						?>
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<div class="label">Private pictures</div>
					<div class="textfield" style="margin-top:4px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
						<? echo "<input style='' type='radio' "; if ($event['private'] == 1) { echo "checked "; } echo "name='private' value='1' /> Yes"; ?>
						<? echo "<input style='margin-left:24px;' type='radio' "; if ($event['private'] == 0) { echo "checked "; } echo "name='private' value='0' /> No"; ?>
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>

					<div class="label">Single day event</div>
					<div class="textfield" style="margin-top:4px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
						<? echo "<input style='' type='radio' "; if ($event['autocreated'] == 1) { echo "checked "; } echo "name='autocreated' value='1' /> Yes"; ?>
						<? echo "<input style='margin-left:24px;' type='radio' "; if ($event['autocreated'] == 0) { echo "checked "; } echo "name='autocreated' value='0' /> No"; ?>
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					
					<div class="label">Available online</div>
					<div class="textfield" style="margin-top:4px;font-family:'Lucida Sans Unicode', 'Lucida Grande', sans-serif;font-size:0.9em;">
						<? echo "<input style='' type='radio' "; if ($event['available'] == 1) { echo "checked "; } echo "name='available' value='1' /> Yes"; ?>
						<? echo "<input style='margin-left:24px;' type='radio' "; if ($event['available'] == 0) { echo "checked "; } echo "name='available' value='0' /> No"; ?>
					</div>
					
					<div style="border-top:1px dotted #b42f56;width:100%;height:20px;"></div>
					<div class="button" style="float:left;width:220px;padding:0px;"><input style="margin:0px;padding:0px;" type="image" alt="Submit!" src="<? echo $baseUrl; ?>/assets/images/button-save.jpg" width="220" height="63" /></div>
					<div class="button" style="float:left;margin:0px 0px 0px 20px;"><a href="<? $baseUrl; ?>/rental/events"><img alt="Cancel" src="<? echo $baseUrl; ?>/assets/images/button-cancel.jpg" width="220" height="63" /></a></div>
					<div style="clear:both;"></div>
					
					<input type="hidden" name="form_id" value="event">
					
			</div>

		</form>
		
		<?
		} //form succeed
		?>
		
	</div>
	
</div> <? //id=content?>