<?php

	require_once '../common/global.php';
        include G_PATH."common/general.php";
	
	if (!isset($_REQUEST['event_id']))
	{
		echo "Usage: photos2event.php?event_id=1";
	}
	else
	{
		$event_id = $_REQUEST['event_id'];
		$dongle = $_REQUEST['dongle'];
		$event = mysql_fetch_array(mysql_query("SELECT * FROM events WHERE id=$event_id"));

		$directory = "events/".$event['start_date'].$event['id'];
		
		$results = array();
	    $handler = opendir($directory);
	    while ($file = readdir($handler)) {

	      if ($file != "." && $file != "..") {
	        $results[] = $file;
	      }

	    }

	    closedir($handler);
		sort($results);

		$lastResult = "";
		for ($x = 0; $x < count($results); $x++)
		{
			$result = substr($results[$x], 0, -4);
			if ($result != $lastResult)
			{
				
				//echo $result."<br />";
				
				//mysql_query("INSERT INTO photos SET code='$result', event_id=$event_id, booth_id='$dongle'") or die("ko#insert");
				$b = mysql_fetch_array(mysql_query("SELECT * FROM booths WHERE dongle='$dongle'"));
				$b_id = $b['id'];
				mysql_query("INSERT INTO photos SET code='$result', event_id=$event_id, booth_id=$b_id");
				$lastResult = $result;
				
			}
		}
	    
	
	}

?>