<?php
 echo "<h1>Goodbye screen <input type='button' class='miniAdd' onclick='edit(26 , $idUSB);'></h1>";
 echo "<p style='width:55%;'>The bye screen is the image that will be displayed on the PhotoBooth&#39;s screen when a customer finishes taking a photo.</p>";

 echo "<img src='images/web/custom/$booth/bye.png'  style='width:30%;float:right;'>";
 echo "<div style='width:55%;border:1px solid gray; height:60%;margin-left:5%;overflow:auto;'>";
 listarArchivos( "usbs/".$folder . "/PhotoIdUpload/Bye/Random/", $screens   , $URL_LOGIN , 2 , $idUSB , $event_id , $folder , $booth);
 echo "</div>"
?>
