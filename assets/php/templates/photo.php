<?
	
	$displayPhoto = false;
	
	//////////////////////////////////////////
	// NEVER EXPIRE
	//////////////////////////////////////////
	
	include("never-expire.php");
	
	for($x = 0; $x < count($neverExpire); $x++)
	{
		if ($neverExpire[$x] == $code) $displayPhoto = true;
	}
	
	//////////////////////////////////////////
	
	
	$photo = mysql_fetch_array(mysql_query("SELECT * FROM photos WHERE code='$code'"));
	if ($photo['flag'] && !$displayPhoto)
	{
		echo "<div id='title' style='margin:98px 0px;line-height:1.2em;'>This photo has been marked<br />as inappropiate, sorry!</div>";
	}
	else
	{
	
		if (!$displayPhoto)
		{
	
			if ($event['start_date'] > date('Ymd'))
			{
				echo "<div id='title' style='margin:98px 0px;'>Your photo will be available from: ".date8($event['start_date'],"/")."</div>";
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
					//echo $event['start_date'] ."---". $check_date;
					echo "<div id='title' style='margin:98px 0px;'>Expired photo, sorry!</div>";
					//$displayPhoto = true;
				}
				else
				{
					$displayPhoto = true;
				}
			}
		
		}//displayPhoto
		
	}
	
	if (!$event['available'])
	{
		echo "<div id='title' style='margin:98px 0px;'>This event photos are not available.</div>";
		$displayPhoto = false;
	}
	
	if ($displayPhoto)
	{
		
	$photoDir = "events/".$event['start_date'].$event['id'];
	$photoImg = $code.".jpg";

	$photoInfo = GetImageSize($photoDir."/".$photoImg);
	$photoX = $photoInfo[0];
	$photoY = $photoInfo[1];
	
	if ($photoX > $photoY)
	{
		
		$displayX = 936;
		$displayY = $displayX * $photoY / $photoX; //308;
		
		echo "<div id='photoLandscape'>";
			
			echo "<div id='title'>".$event['title']."</div>";
			echo "<div id='date'>".date ("F d, Y", filemtime($photoDir."/".$photoImg))."</div>";
			
			echo "<div id='photo'><img src='".$baseUrl."/".$photoDir."/".$photoImg."' width='".$displayX."' height='".$displayY."' /></div>";
			clearBoth(0);
			
			echo "<div id='actions'>";
			
				$file1 = $photoDir."/".$code.".wmv";
				$file2 = "../".$photoDir."/".$code.".wmv";
				$file3 = "../../".$photoDir."/".$code.".wmv";
				$file4 = "../../../".$photoDir."/".$code.".wmv";

			    if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4))
				{
					echo "<a style='margin-right:12px;' href='".$baseUrl."/".$photoDir."/".$code.".wmv' target='_blank'><img class='photoButton' id='video' src='".$baseUrl."/assets/images/button-video-off.png' width='120' height='120' border='0' /></a>";
				}
//201306mp4 INICI VIC				
				$file1 = $photoDir."/".$code.".mp4";
				$file2 = "../".$photoDir."/".$code.".mp4";
				$file3 = "../../".$photoDir."/".$code.".mp4";
				$file4 = "../../../".$photoDir."/".$code.".mp4";

			    if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4))
				{
					echo "<a style='margin-right:12px;' href='".$baseUrl."/".$photoDir."/".$code.".mp4' target='_blank'><img class='photoButton' id='video' src='".$baseUrl."/assets/images/button-video-off.png' width='120' height='120' border='0' /></a>";
				}
                                
//201306mp4 FINAL VIC				
                                
				//<script>function fbs_click() {u=location.href;t=document.title;window.open('https://www.facebook.com/sharer.php?u='+encodeURIComponent(u)+'&t='+encodeURIComponent(t),'sharer','toolbar=0,status=0,width=626,height=436');return false;}</script><a rel="nofollow" href="https://www.facebook.com/share.php?u=<;url>" onclick="return fbs_click()" target="_blank"><img src='<? echo $baseUrl; /assets/images/button-facebook.png' width='119' height='119' border='0' /></a>
				echo "<a style='margin-left:12px;' href='https://www.myphotocode.com/assets/php/templates/facebook.php' target='_blank'><img class='photoButton' id='facebook' src='".$baseUrl."/assets/images/button-facebook-off.png' width='120' height='120' border='0' /></a>";
				echo "<a style='margin-left:12px;' href=javascript:startPopup('email','".$code."');><img class='photoButton' id='email' src='".$baseUrl."/assets/images/button-email-off.png' width='120' height='120' border='0' /></a>";
				echo "<a style='margin-left:12px;' href='https://twitter.com/share?url=".$baseUrl."/".$photoDir."/".$photoImg."' target='_blank'><img class='photoButton' id='twitter' src='".$baseUrl."/assets/images/button-twitter-off.png' width='120' height='120' border='0' /></a>";
				
				if ($event['private'] == 0)
				{	
					echo "<div style='padding-top:32px;'><a href='https://www.myphotocode.com/event/".$event['start_date'].$event['id']."'>View more photos!</a></div>";
				}
				if($event['id'] == 5886){
				echo "<a href='http://www.digital-centre.com/advertising-banner.html'>";
				   echo "<img src='https://www.myphotocode.com/assets/images/banner_Iaapa.gif' style='width:600;height:200;margin-top:40px;'>";
				   echo "</a>";
				}
			echo "</div>";
			
		echo "</div>";
		
	}
	else
	{
		
		$displayY = 576;		
		$displayX = $displayY * $photoX / $photoY; //190;
		
		echo "<div id='photoPortrait'>";
		
			echo "<div id='photo'><img src='".$baseUrl."/".$photoDir."/".$photoImg."' width='".$displayX."' height='".$displayY."' /></div>";
			
			echo "<div id='actions'>";
			
				echo "<div id='title'>".$event['title']."</div>";
				echo "<div id='date'>".date ("F d, Y", filemtime($photoDir."/".$photoImg))."</div>";
				
				echo "<div id='actions2'>";

					$file1 = $photoDir."/".$code.".wmv";
					$file2 = "../".$photoDir."/".$code.".wmv";
					$file3 = "../../".$photoDir."/".$code.".wmv";
					$file4 = "../../../".$photoDir."/".$code.".wmv";

				    if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4))
					{
						echo "<a style='margin-right:12px;' href='".$baseUrl."/".$photoDir."/".$code.".wmv' target='_blank'><img class='photoButton' id='video' src='".$baseUrl."/assets/images/button-video-off.png' width='120' height='120' border='0' /></a>";
					}
//201306mp4 INICI VIC				
				$file1 = $photoDir."/".$code.".mp4";
				$file2 = "../".$photoDir."/".$code.".mp4";
				$file3 = "../../".$photoDir."/".$code.".mp4";
				$file4 = "../../../".$photoDir."/".$code.".mp4";

			    if (file_exists($file1) || file_exists($file2) || file_exists($file3) || file_exists($file4))
				{
					echo "<a style='margin-right:12px;' href='".$baseUrl."/".$photoDir."/".$code.".mp4' target='_blank'><img class='photoButton' id='video' src='".$baseUrl."/assets/images/button-video-off.png' width='120' height='120' border='0' /></a>";
				}
                                
//201306mp4 FINAL VIC				

					echo "<a style='margin-left:12px;' href='https://www.myphotocode.com/assets/php/templates/facebook.php' target='_blank'><img class='photoButton' id='facebook' src='".$baseUrl."/assets/images/button-facebook-off.png' width='120' height='120' border='0' /></a>";
					echo "<a style='margin-left:12px;' href=javascript:startPopup('email','".$code."');><img class='photoButton' id='email' src='".$baseUrl."/assets/images/button-email-off.png' width='120' height='120' border='0' /></a>";
					echo "<a style='margin-left:12px;' href='https://twitter.com/share?url=".$baseUrl."/".$photoDir."/".$photoImg."' target='_blank'><img class='photoButton' id='twitter' src='".$baseUrl."/assets/images/button-twitter-off.png' width='120' height='120' border='0' /></a>";
					
					if ($event['private'] == 0)
					{	
						echo "<div style='padding:32px 0px 0px 10px;text-align:left;'><a href='https://www.myphotocode.com/event/".$event['start_date'].$event['id']."'>View more photos!</a></div>";
					}
								if($event['id'] == 5886){
				echo "<a href='http://www.digital-centre.com/advertising-banner.html'>";
				   echo "<img src='https://www.myphotocode.com/assets/images/banner_Iaapa.gif' style='width:600;height:200;margin-top:40px;margin-left:12px;'>";
				   echo "</a>";
				}	
				echo "</div>";
				
			echo "</div>";
			
			clearBoth(0);			
			
		echo "</div>";
		
	}
	
	}

?>