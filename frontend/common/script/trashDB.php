<?php
/*
 * Script Vell per pasar events vells a la base de dades Trashed
 * Tot amb conexions manuals.
 * 11/11/16 - Nova versió amb BaseController a mitjes
 */
require_once dirname(__FILE__) . "/../global.php";
require_once G_PATH . "common/config/config.php";

utils::log("---- Clean " . date("Y-m-d H:i:s") . " ---", "logCronTrashDB");

function querySQL($connect, $SQL){
	if (!$resultado = $connect->query($SQL)) {
		print "Error: La ejecución de la consulta falló debido a: \n";
		print "Query: " . $SQL . "\n";
		print "Errno: " . $connect->errno . "\n";
		print "Error: " . $connect->error . "\n";
		exit;
	}
	return $resultado;
}

function backupSQL($connect, $table, $array){
	$columns = implode(", ",array_keys($array));
	$values = "";
	foreach($array as $field){
                $field = utf8_decode($field);
                $field = addslashes($field);
                //$field = "'" . $field . "'";
		if($field){
			$values  .= "'$field', ";
		}
		else {
			$values  .= "0, ";			
		}
	}
	$values = substr($values, 0, -2);

	$sql = "INSERT INTO $table ($columns) VALUES ($values)";

	if (!$resultado = $connect->query($sql)) {
		print "Error: La ejecución de la consulta falló debido a: \n";
		print "Query: " . $SQL . "\n";
		print "Errno: " . $connect->errno . "\n";
		print "Error: " . $connect->error . "\n";
		exit;
	}
	return $resultado;
}

$mysqliMYPC = new mysqli(
	$DB_myphotocode['host'],
	$DB_myphotocode['user'],
	$DB_myphotocode['pass'],
	$DB_myphotocode['database']
);
$mysqliBK = new mysqli(
	$DB_myphotocode_trashed['host'],
	$DB_myphotocode_trashed['user'],
	$DB_myphotocode_trashed['pass'],
	$DB_myphotocode_trashed['database']
);

$countEvents = 0;
$countCLD_questions_emails = 0;
$countPhotos = 0;
$countPhoto_Files = 0;
$countCLD_estadistiques_photo = 0;
$countregistre_emails = 0;
$countUsbs = 0;
$countCLD_emailsText = 0;

ob_start();

if ($mysqliMYPC->connect_errno) {
	print "Error: Fallo al conectarse a MySQL debido a: \n";
	print "Errno: " . $mysqliMYPC->connect_errno . "\n";
	print "Error: " . $mysqliMYPC->connect_error . "\n";
	exit;
}
if($mysqliBK->connect_errno){
        print "Error: Fallo al conectarse a MySQL debido a: \n";
	print "Errno: " . $mysqliBK->connect_errno . "\n";
	print "Error: " . $mysqliBK->connect_error . "\n";
	exit;
}
else{
	//Events Table
	$events = querySQL($mysqliMYPC, "SELECT * FROM events WHERE trashed IS NOT NULL ORDER BY id LIMIT 250");
	while ($event = $events->fetch_assoc()){
		print "Event: {$event['id']} - {$event['start_date']} - {$event['title']} \n";
		
		//CLD_questions_email Table
		$CLD_questions_emails = querySQL($mysqliMYPC, "SELECT * FROM CLD_questions_emails WHERE event = ".$event['id']);
		while ($CLD_questions_email = $CLD_questions_emails->fetch_assoc()){
			if(backupSQL($mysqliBK, 'CLD_questions_emails', $CLD_questions_email)){
//				print "Backup CLD_questions_emails Complete \n";
				if(querySQL($mysqliMYPC, "DELETE FROM CLD_questions_emails WHERE id={$CLD_questions_email['id']}")){
//					print "Deleted row Complete \n";
				}
			}
			//End Rows CLD_questions_email
			$countCLD_questions_emails++;
		}
		
		//Photos Table
		$photos = querySQL($mysqliMYPC, "SELECT * FROM photos WHERE event_id = ".$event['id']);
		while ($photo = $photos->fetch_assoc()){
			//Photo_Files Table
			$photoFiles = querySQL($mysqliMYPC, "SELECT * FROM photo_Files WHERE name LIKE '".$photo['code']."%'");
			while ($photoFile = $photoFiles->fetch_assoc()){
				if(backupSQL($mysqliBK, 'photo_Files', $photoFile)){
//					print "Backup photo_Files Complete \n";
					if(querySQL($mysqliMYPC, "DELETE FROM photo_Files WHERE id={$photoFile['id']}")){
//						print "Deleted row Complete \n";
					}
				}
				//End Rows Photo_Files
				$countPhoto_Files++;
			}

			//CLD_estadistiques_photos Table
//			$CLD_estadistiques_photo = querySQL($mysqliMYPC, "SELECT * FROM CLD_estadistiques_photos WHERE photo LIKE '".$photo['code']."%'");
//			while ($staticPhoto = $CLD_estadistiques_photo->fetch_assoc()){
//				if(backupSQL($mysqliBK, 'CLD_estadistiques_photos', $staticPhoto)){
////					print "Backup CLD_estadistiques_photo Complete \n";
//					if(querySQL($mysqliMYPC, "DELETE FROM CLD_estadistiques_photos WHERE id={$staticPhoto['id']}")){
////						print "Deleted row Complete \n";
//					}
//				}
//				//End Rows CLD_estadistiques_photos
//				$countCLD_estadistiques_photo++;
//			}

			//registre_emails Table
//			$registre_emails = querySQL($mysqliMYPC, "SELECT * FROM registre_emails WHERE event_id LIKE '".$photo['code']."%'");
//			while ($registre_email = $registre_emails->fetch_assoc()){
//				if(backupSQL($mysqliBK, 'registre_emails', $registre_email)){
////					print "Backup registre_emails Complete \n";
//					if(querySQL($mysqliMYPC, "DELETE FROM registre_emails WHERE id={$registre_email['id']}")){
////						print "Deleted row Complete \n";
//					}
//				}
//				//End Rows registre_emails
//				$countregistre_emails++;
//			}

                        
                        if(backupSQL($mysqliBK, 'photos', $photo)){
//				print "Backup photos Complete \n";
				if(querySQL($mysqliMYPC, "DELETE FROM photos WHERE id={$photo['id']}")){
//					print "Deleted row Complete \n";
				}
			}

			//End Rows photos
			$countPhotos++;
		}
		
		// usbs Table
		$usbs = querySQL($mysqliMYPC, "SELECT * FROM usbs WHERE event_id = ".$event['id']);
		while ($usb1 = $usbs->fetch_assoc()){
			if(backupSQL($mysqliBK, 'usbs', $usb1)){
//				print "Backup usbs Complete \n";
				if(querySQL($mysqliMYPC, "DELETE FROM usbs WHERE id={$usb1['id']}")){
//					print "Deleted row Complete \n";
				}
			}
			//End Rows usbs
			$countUsbs++;
		}

		//CLD_emailsText Table
		$CLD_emailsTexts = querySQL($mysqliMYPC, "SELECT * FROM CLD_emailsText WHERE event = ".$event['id']);
		while ($CLD_emailText = $CLD_emailsTexts->fetch_assoc()){
			if(backupSQL($mysqliBK, 'CLD_emailsText', $CLD_emailText)){
//				print "Backup CLD_emailsText Complete \n";
				if(querySQL($mysqliMYPC, "DELETE FROM CLD_emailsText WHERE id={$CLD_emailText['id']}")){
//					print "Deleted row Complete \n";
				}
			}
			//End Rows CLD_emailsText
			$countCLD_emailsText++;
		}

                
                //TABLE EVENT
                if(backupSQL($mysqliBK, 'events', $event)){
//			print "Backup event Complete \n";
			if(querySQL($mysqliMYPC, "DELETE FROM events WHERE id={$event['id']}")){
//				print "Deleted row Complete \n";
			} 
		}
                
                print "Event Trashed Complete \n";
                
		//End row Events
		$countEvents++;
	}
}

$today = date('d-m-Y');

print " \n \n \n";
print "------------------------------------------------------------------ \n";
print "Script Complete ".$today." \n";
print "------------------------------------------------------------------ \n";
print "Num Sentences Table events:                    {$countEvents} \n";
print "Num Sentences Table CLD_questions_emails:      {$countCLD_questions_emails} \n";
print "Num Sentences Table photos:                    {$countPhotos} \n";
print "Num Sentences Table photo_Files:               {$countPhoto_Files} \n";
print "Num Sentences Table CLD_estadistiques_photo:   {$countCLD_estadistiques_photo} \n";
print "Num Sentences Table registre_emails:           {$countregistre_emails} \n";
print "Num Sentences Table usbs:                      {$countUsbs} \n";
print "Num Sentences Table CLD_emailsText:            {$countCLD_emailsText} \n";
print "------------------------------------------------------------------ \n";

$logs = ob_get_contents();
ob_end_clean();
utils::log($logs, "logTrashEventsBK") ;
echo $logs;

?>