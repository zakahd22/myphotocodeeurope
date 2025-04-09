<?php
 echo "<h1>Customs Shots <input type='button' class='miniAdd' onclick='edit(27 , $idUSB);'></h1>";
 echo "<p style='width:55%;'>Custom Shots are the images that you want display on the screen during an event or, while the PhotoBooth is on and not in use. </p>";

 echo "<img src='images/web/custom/$booth/shots.png'  style='width:30%;float:right;'>";
 echo "<div style='width:55%;border:1px solid gray; height:60%;margin-left:5%;overflow:auto;'>";
 listarArchivos2( "usbs/".$folder . "/PhotoIdEvents/CustomShots/", $screens   , $URL_LOGIN , 3 , $idUSB , $event_id , $folder , $booth);
 echo "</div>";