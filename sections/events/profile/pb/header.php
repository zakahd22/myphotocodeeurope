<?php

echo "<h1>HEADER (Wedding version) <input type='button' class='miniAdd' onclick='edit(29 , $idUSB);'></h1>";
echo "<img src='images/web/custom/header.png'  style='width:30%;float:right;'>";

if (file_exists("../../../../usbs/$folder/PhotoIdEvents/Wedding/Header/1.jpg")) {
    echo "<p>Your Header Wedding Selected</p>";
    echo "<div style='margin-left:5%;width:60%;'>";
    echo "<img src='usbs/$folder/PhotoIdEvents/Wedding/Header/1.jpg'  style='width:60%;margin-left:20%;margin-top:10%;'>";
     echo "<p style='color:red;text-align:center;cursor:pointer;border-bottom:1px solid gray;padding-bottom:10px;'>";
                    echo "<input type='button' class='okB deleteB' onclick='deleteIMGusb(5 , $event_id , $idUSB , 0 , \"Header Banner\" , \"$folder\" , \"$booth\" , 0);'>";
    echo "</p>";
    echo "</div>";
    
} else {
    echo "<p> No uploaded Header yet</p>";
}
?>