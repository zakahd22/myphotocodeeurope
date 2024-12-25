<div id="content">

	<div id="rental-navigation"><span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental">Home</a> <span class="raquo">&raquo;</span> <a href="<? echo $baseUrl; ?>/rental/events">Online Events</a> <span class="raquo">&raquo;</span> Photos</div>
	<div id="title" style="line-height:1.2em;">These are the photos of the Online Event.</div>
	
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
					echo "<div style='float:left;margin-right:10px;width:184px;height:210px;;'>";
					$col++;
				}
				
					$photoDir = "events/".$event['start_date'].$event['id'];
					$photoImg = $photo['code'].".jpg";

					$photoInfo = GetImageSize($photoDir."/".$photoImg);
					$photoX = $photoInfo[0];
					$photoY = $photoInfo[1];

					echo "<div class='button' style='overflow:hidden;width:184px;height:184px;'><a href='https://www.myphotocode.com/photo/".$photo['code']."' target='_blank'>";
					if ($photoX > $photoY)
					{
						echo "<img src='".$baseUrl."/".$photoDir."/".$photoImg."' height='184' />";
					}
					else
					{
						echo "<img src='".$baseUrl."/".$photoDir."/".$photoImg."' width='184' />";
					}
					echo "</a></div>";
					echo "<div class='button' style='margin-top:6px;width:18px;'><a href='".$baseUrl."/assets/php/photo-flag.php?code=".$photo['code']."&row=".$row."'><img src='".$baseUrl."/assets/images/flag-".$photo['flag'].".png' width='18' height='17' /></a></div>";							
				
				echo "</div>";
				
			}
			
			echo "<div style='clear:both;height:34px;'></div>";			
			
		?>
		
	</div>
	
	<?
	
	if ($row != 1 || $col != 1)
	{
		
		echo "<div class='succeedMsg' style='margin-bottom:42px;'>";
	
		echo "<a href='https://www.myphotocode.com/assets/php/templates/facebook-album-uploader.php?id=".$event['start_date'].$event['id']."'>&raquo; Post all photos to Facebook.</a>";
	
		echo "</div>";
	
	}
	else
	{
		echo "<div style='margin-bottom:24px;'>The Online Event has 0 photos.</div>";
	}
	
	?>
	
	<div class="button" style="width:220px;margin:0px 0px 34px 0px;padding:0px;background:transparent;"><a href="<? $baseUrl; ?>/rental/events"><img alt="Cancel" src="<? echo $baseUrl; ?>/assets/images/button-goback.png" width="220" height="63" /></a></div>
	
	
</div> <? //id=content?>