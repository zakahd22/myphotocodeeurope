<div id="content">

	<div id="title" style="margin-top:32px; line-height:1.2em;">These are the photos of the Online Event.</div>
	
	<div style="margin-top:32px;">
		
		<?
			
			$row = 1;
			$col = 1;
			
			$q = mysql_query("SELECT * FROM photos WHERE event_id=$event[id]");
			while ($photo = mysql_fetch_array($q))
			{
				
				if ($col == 1 && $row != 1)
				{
					echo "<div style='clear:both;height:10px;'><a name='row".$row."'></a></div>";
				}
				
				if ($col == 5)
				{
					echo "<div style='float:left;width:184px;height:210px;'>";
					$row++;
					$col = 1;
				}
				else
				{
					echo "<div style='float:left;margin-right:10px;width:184px;height:210px;'>";
					$col++;
				}
				
					$photoDir = "events/".$event['start_date'].$event['id'];
					$photoImg = $photo['code'].".jpg";

					$photoInfo = GetImageSize($photoDir."/".$photoImg);
					$photoX = $photoInfo[0];
					$photoY = $photoInfo[1];

					echo "<div class='button' style='overflow:hidden;width:184px;height:184px;'><a href='" . G_PAGE . "photo/".$photo['code']."'>";
					if ($photoX > $photoY)
					{
						echo "<img src='".$baseUrl."/".$photoDir."/".$photoImg."' height='184' />";
					}
					else
					{
						echo "<img src='".$baseUrl."/".$photoDir."/".$photoImg."' width='184' />";
					}
					echo "</a></div>";
				
				echo "</div>";
				
			}
			
			echo "<div style='clear:both;height:34px;'></div>";			
			
		?>
		
	</div>

	
</div> <? //id=content?>