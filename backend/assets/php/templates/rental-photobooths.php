<div id="content">

	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> PhotoBooths</div>
	
	<div id="title" style="line-height:1.2em;">Manage your PhotoBooths.</div>


			<div style="margin-top:18px;">
				<table width="100%" cellpadding="0" cellspacing="0" border="0" />
					<tr class="header">
						<td width="160">Type</td>
						<td>Name</td>
						<td>S/N</td>
						<td>Location</td>
						<td width="90">Actions</td>
					</tr>
			<?
				
				
				$tr_dark = true;//`serialnumber`, `location`
                                $sql = "SELECT App_booths.idBooth,  `booth_types`.`name` AS tipus, 
                                    `App_booths`.`name` AS nom,  `App_booths`.`serialnumber` ,  `App_booths`.`location` 
                                FROM  `App_booths` 
                                LEFT JOIN  `booth_types` ON  `App_booths`.`type` =  `booth_types`.`char`  WHERE owner=$rental[id];";
				$q = mysql_query($sql);
                                
				while ($myRS = mysql_fetch_array($q))
				{

					$pb_id = $myRS['idBooth'];
					$pb_type = $myRS['tipus'];
					$pb_name = $myRS['nom'];
					$pb_sn = $myRS['serialnumber'];
					$pb_loc = $myRS['location'];


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

						echo "<td>".$pb_type."</td>";
						echo "<td>".$pb_name."</td>";
						echo "<td>".$pb_sn."</td>";
						echo "<td>".$pb_loc."</td>";
						echo "<td><a href='".$baseUrl."/rental/photobooths/edit/$pb_id'>Edit</a></td>";

					echo "</tr>";

				}

			?>
				</table>
                            <p>&nbsp;</p>
			</div>
			
			
		</div> <? //id=content?>