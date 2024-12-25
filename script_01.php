<?php
/*include './sessio.php';
include './conexio.php';
$CLD_CON2 = clone($CLD_CON);
$CLD_CON3 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT id FROM events");
while($CLD_CON->FetchArray()){
   $eventID =  $CLD_CON->GetArrayField("id");
   $CLD_CON2->OpenRs("SELECT Appusr_datetime FROM photos WHERE event_id=$eventID ORDER BY Appusr_datetime DESC LIMIT 1");
   if($CLD_CON2->FetchArray()){
   $d = $CLD_CON2->GetArrayField("Appusr_datetime");
   $CLD_CON3->Execute("UPDATE events SET CLD_date_lastPhoto='$d' WHERE id=$eventID");
   echo "<p>OK</P>";
   }else{
     echo "<p>O Photos</P>";  
       
   }
}*/
?>
