<div id="content">

	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> Online Events</div>
	<div id="title" style="line-height:1.2em;">Manage the Online Events.</div>

	<div style="margin-top:32px;"> 
		
		<div style="float:left;">
		<span class="light">Sort by:</span>
		<? if (!isset($_SESSION['events_order'])) $_SESSION['events_order'] = "date"; ?>
		<? if ($_SESSION['events_order'] == "date") { echo "Date"; } else { ?><a href="<? echo $baseUrl; ?>/assets/php/events-order.php?order=date">Date</a><? } ?>
		<span class='raquo'>|</span>
		<? if ($_SESSION['events_order'] == "name") { echo "Name"; } else { ?><a href="<? echo $baseUrl; ?>/assets/php/events-order.php?order=name">Name</a><? } ?>
		</div>
		
		<?/*
		<? $available = mysql_num_rows(mysql_query("SELECT * FROM events WHERE rental_id=$rental[id]")); ?>
		<div style="float:right"><? echo $available; ?> available</div>
		*/?>
		
		<?/*
		<div style="float:right">
			<span class="light">Show events by:</span>
			<select onChange="document.location.href='https://www.myphotocode.com/assets/php/events-filter-booth.php?booth='+this.options[this.selectedIndex].value;">
				<?
					
					if(!isset($_SESSION['events_filter_booth'])) $_SESSION['events_filter_booth'] = 0;
					
					echo "<option value='0'"; if($_SESSION['events_filter_booth'] == 0) { echo " SELECTED"; } echo ">All photo booths</option>";
					echo "<option value='0'>-------------------------------</option>";
				
				$q = mysql_query("SELECT * FROM booths WHERE rental_id=$rental[id]");
				while ($booth = mysql_fetch_array($q))
				{
					
					$booth_type_char = substr($booth['reference'],0,1);

					$q2 = mysql_query("SELECT * FROM booth_types");
					while($booth_type = mysql_fetch_array($q2))
					{					
						if ($booth_type['char'] == $booth_type_char)
						{						
							echo "<option value='".$booth['id']."'"; if($_SESSION['events_filter_booth'] == $booth['id']) { echo " SELECTED"; } echo ">".$booth_type['name']." (".$booth['dongle'].")</option>";							
						}
					}
					
				}
				
				?>
			</select>
		</div>
		*/?>
		
		<div style="clear:both;"></div>
		
	</div>
	
	<div style="margin-top:18px;">
		<table width="100%" cellpadding="0" cellspacing="0" border="0" />
			<tr class="header">
				<td width="100">Date</td>
				<td>Name</td>
				<td>Photo booth</td>
				<td width="80" align="center">Status</td>
				<td width="80" align="center">Photos</td>
				<td width="80" align="center">Videos</td>
				<td width="200">Actions</td>
			</tr>
	<?
		
		switch ($_SESSION['events_order'])
		{
			case "date" : $orderby = "start_date DESC, id DESC"; break;
			case "name" : $orderby = "title ASC"; break;
		}
		
		$tr_dark = true;
		$q = mysql_query("SELECT * FROM events WHERE rental_id=$rental[id] ORDER BY $orderby");
		while ($event = mysql_fetch_array($q))
		{
		
			//start date
			$event_start_date = date8($event['start_date'],"/");
		
			//title
			$event_title = $event['title'];
		
			//booths
			
			$filtered = false;
			
			$event_booths = "";
			$booths = array();
			$booths_total = 0;
			$q_photos = mysql_query("SELECT * FROM photos WHERE event_id=$event[id] ORDER BY code ASC");
			while ($photo = mysql_fetch_array($q_photos))
			{
				
				$char = strtoupper(substr($photo['code'],0,1));
				if ($char != $booths[$booths_total-1])
				{
					array_push($booths,$char);
					$booths_total++;
				}
				
				if ($char == $_SESSION['events_filter_booth']) $filtered = true;
				
			}
			for ($x = 0; $x < $booths_total; $x++)
			{	
				
				$booth = mysql_fetch_array(mysql_query("SELECT * FROM booth_types WHERE `char`='$booths[$x]'"));
				
				if ($x > 0) $event_booths .= "<br />";						
				$event_booths .= $booth['name'];
				
			}
		
			//status
			if ($event['start_date'] > date('Ymd'))
			{
				$event_status = "Waiting";
			}
			else
			{
				
				$config = mysql_fetch_array(mysql_query("SELECT * FROM config WHERE id=1"));
				$months_caducity = $config['months_caducity'];
				
				$year = date('Y');
				$month = date('m');
				$day = date('d');
				$month = $month - $months_caducity;
				if ($month < 1)
				{
					$year--;
					$month = $month + 12;
				}
				//if ($month == 0) { $month = 12; $year--; }
				//if ($month == -1) { $month = 11; $year--; }
				if ($month < 10) $month = "0".$month;
				//if ($day < 10) $day = "0".$day;
				$check_date = $year.$month.$day;
				trace($event['start_date']." < ".$check_date);
				if ($event['start_date'] < $check_date)
				{
					trace(" (y)<br />");
					$event_status = "Expired";
				}
				else
				{
					trace(" (n)<br />");
					$event_status = "Online";
				}
			}
		
			//photos
			$event_photos = mysql_num_rows(mysql_query("SELECT * FROM photos WHERE event_id=$event[id]"));

			//videos
			
			$event_videos = 0;
			
			$photoDir = "events/".$event['start_date'].$event['id'];

			$qw = mysql_query("SELECT * FROM photos WHERE event_id=$event[id]");
			while ($photo = mysql_fetch_array($qw))
			{
				
				$code = $photo['code'];
				
				$file1 = $photoDir."/".$code.".wmv";
				$file2 = "../".$photoDir."/".$code.".wmv";
				$file3 = "../../".$photoDir."/".$code.".wmv";
				$file4 = "../../../".$photoDir."/".$code.".wmv";

			    if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4))
				{
					$event_videos++;
				}
			
			}
			
		
			//line output
			if ($_SESSION['events_filter_booth'] == 0 || $filtered)
			{
			
				if ($tr_dark)
				{
					echo "<tr class='dark'>";
					$tr_dark = false;
				}
				else
				{
					echo "<tr class='light'>";
					$tr_dark = true;
				}
		
					echo "<td>".$event_start_date."</td>";
					echo "<td>".$event_title."</td>";
					echo "<td>".$event_booths."</td>";
					echo "<td align='center'>".$event_status."</td>";
					echo "<td align='center'>".$event_photos."</td>";
					echo "<td align='center'>".$event_videos."</td>";
					echo "<td><a href='".$baseUrl."/rental/events/photos/".$event['start_date'].$event['id']."'>Photos</a> <span class='raquo'>|</span> <a target='_blank' href='https://www.myphotocode.com/assets/php/event-emails.php?event=".$event['start_date'].$event['id']."'>Emails</a> <span class='raquo'>|</span> <a href='".$baseUrl."/rental/events/edit/".$event['start_date'].$event['id']."'>Edit</a></td>";
		
				echo "</tr>";
			
			}
			
		}

	?>
		</table>
	</div>
	
	<div class="button" style="margin:32px 0px;"><a href="<? $baseUrl; ?>/rental/events/new"><img alt="Add new" src="<? echo $baseUrl; ?>/assets/images/button-addnew.png" width="220" height="63" /></a></div>
	
</div> <? //id=content?>