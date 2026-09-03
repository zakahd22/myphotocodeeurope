<div id="content">
	
	<div style="position:absolute;top:14px;left:0px;"><a href="http://www.digital-centre.com" target="_blank"><img src="<? echo $baseUrl; ?>/assets/images/header-logo-digitalcentre.jpg" width="115" height="30" border="0" /></a></div>
	
	<?
	
	echo "<div style='position:absolute;top:0px;right:0px;line-height:54px;'>";
	
	switch ($content)
	{
		case "home" : echo "<a href='".$baseUrl."/login'>Login.</a>"; break;
		case "rental" : echo "<span class='header-text'>Hello ".$rental['name']."!</span> <a href='".$baseUrl."/rental/logout'>Logout.</a>"; break;
		default : echo "<a href='".$baseUrl."'>Home.</a>"; break;
	}
	
	echo "</div>";
	
	?>
	
</div>