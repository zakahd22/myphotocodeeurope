<?php
 echo "<h1>Welcome Screen<input type='button' class='miniAdd' onclick='edit(25 , $idUSB);'></h1>";
 echo "<p style='width:55%;'>The welcome screen is the image that will be displayed on the PhotoBooth&#39;s screen when a customer start&#39;s to take a photo.</p>";

 echo "<img src='images/web/custom/$booth/welcome.png' style='width:30%;float:right;'>";
 echo "<div style='width:55%;border:1px solid gray; height:60%;margin-left:5%;overflow:auto;'>";
 listarArchivos( "usbs/".$folder . "/PhotoIdUpload/Welcome/Random/", $screens   , $URL_LOGIN , 1 , $idUSB , $event_id , $folder , $booth);
 echo "</div>"
?>
