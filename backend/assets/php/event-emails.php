<?
	require_once dirname (__FILE__) . "/common/global.php";
        include G_PATH.'common/general.php';
	
	$event_folder = $_REQUEST['event'];
	$event_id = substr($event_folder,8);
	
	$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$event_id"));

	if ($event['owner_id'] != $_SESSION['owner_id']) header("Location:../../index.php");
	
	echo "<html><body>";
	echo "<h1>".$event['title']."</h1>";
	
	$list = array();
	$q = mysql_query("SELECT * FROM photos WHERE event_id=$event_id");
	while ($photo = mysql_fetch_array($q))
	{
		
		$q2 = mysql_query("SELECT * FROM registre_emails WHERE code='$photo[code]'");
		while ($email = mysql_fetch_array($q2))
		{
			array_push($list,$email['email']);
		}
		
	}
	
	
	if (count($list) == 0)
	{
		echo "No emails were entered in this event.";
	}
	else
	{
		
		sort($list);
		
		$last = "";
		foreach ($list as $key => $val) {
			
			if ($val != $last)
			{
		    	echo $val."<br />";
				$last = $val;
			}			
			
		}
		
	}
	
	echo "</body></html>";

?>