<div id="content">

	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> USB Set Ups</div>
	
	<div id="title" style="line-height:1.2em;">Manage your USB Set Ups.</div>

	<div style="margin-top:32px;"> 
		
		<div style="float:left;">
		<span class="light">Sort by:</span>
		<? if (!isset($_SESSION['usbs_order'])) $_SESSION['usbs_order'] = "date"; ?>
		<? if ($_SESSION['usbs_order'] == "date") { echo "Date"; } else { ?><a href="<? echo $baseUrl; ?>/assets/php/usbs-order.php?order=date">Date</a><? } ?>
		<span class='raquo'>|</span>
		<? if ($_SESSION['usbs_order'] == "booth") { echo "Photo booth"; } else { ?><a href="<? echo $baseUrl; ?>/assets/php/usbs-order.php?order=booth">Photo booth</a><? } ?>
		<span class='raquo'>|</span>
		<? if ($_SESSION['usbs_order'] == "name") { echo "Name"; } else { ?><a href="<? echo $baseUrl; ?>/assets/php/usbs-order.php?order=name">Name</a><? } ?>
		</div>
		
		<?/*
		<? $available = mysql_num_rows(mysql_query("SELECT * FROM usbs WHERE rental_id=$rental[id]")); ?>
		<div style="float:right"><? echo $available; ?> available</div>
		*/?>
		
		<div style="clear:both;"></div>
		
	</div>

			<div style="margin-top:18px;">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" />
					<tr class="header">
						<td width="100">Date</td>
						<td width="160">Photo booth</td>
						<td>Name</td>
						<td width="220">Actions</td>
					</tr>
			<?
				
				switch ($_SESSION['usbs_order'])
				{
					case "date" : $orderby = "creation_date DESC, id DESC"; break;
					case "booth" : $orderby = "boothtype_char ASC"; break;
					case "name" : $orderby = "title ASC"; break;
				}
				
				$tr_dark = true;
				$q = mysql_query("SELECT * FROM usbs WHERE rental_id=$rental[id] AND available=1 ORDER BY $orderby");
				while ($usb = mysql_fetch_array($q))
				{

					//start date
					$usb_creation_date = date8($usb['creation_date'],"/");

					//photo booth
					$q2 = mysql_query("SELECT * FROM booth_types");
					while($bt = mysql_fetch_array($q2))
					{
						if ($bt['char'] == $usb['boothtype_char']) $usb_photobooth = $bt['name'];
					}

					//title
					$usb_title = $usb['title'];

					//line output
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

						echo "<td>".$usb_creation_date."</td>";
						echo "<td>".$usb_photobooth."</td>";
						echo "<td>".$usb_title."</td>";
						echo "<td><a href='https://myphotocode.com/assets/php/download-usb.php?usb_id=".$usb['creation_date'].$usb['id']."'>Download</a> <span class='raquo'>|</span> <a href='".$baseUrl."/rental/usbs/edit/".$usb['creation_date'].$usb['id']."'>Edit</a> <span class='raquo'>|</span> <a href='https://myphotocode.com/assets/php/delete-usb.php?usb_id=".$usb['creation_date'].$usb['id']."'>Delete</a></td>";

					echo "</tr>";

				}

			?>
				</table>
			</div>
			
			<div class="button" style="margin:32px 0px;"><a href="<? $baseUrl; ?>/rental/usbs/new"><img alt="Add new" src="<? echo $baseUrl; ?>/assets/images/button-addnew.png" width="220" height="63" /></a></div>
			
		</div> <? //id=content?>