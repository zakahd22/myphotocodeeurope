<?php
echo "<h1>Background Music <input type='button' class='miniAdd' onclick='edit(28 , $idUSB);'></h1>";
echo "<p style='width:55%;'>The Background Music is the music that will be played on the PhotoBooth.</p>";
echo "<img src='images/web/custom/$booth/music.png'  style='width:30%;float:right;'>";
echo "<div style='width:60%;border:1px solid gray; height:60%;margin-left:5%;overflow:auto;text-align:center;'>";
if(file_exists(G_PATH."usbs/$folder/PhotoIdUpload/BGmusic.mp3")){
    echo "<p>Your Selected Background Music</p>";
    echo "<audio src='usbs/$folder/PhotoIdUpload/BGmusic.mp3' controls style='margin-top:5%;'></audio>";
    echo "<p style='color:red;text-align:center;cursor:pointer;border-bottom:1px solid gray;padding-bottom:10px;'>";
    echo "<input type='button' class='miniTrash' style='right: -47%;' onclick='deleteIMGusb(4 , $event_id , $idUSB , 0 , \"Background Music\" , \"$folder\" , \"$booth\" , 0);'>";
    echo "</p>";
}else{
    echo "<p> No uploaded music yet </p>";
}
echo "</div>"
?>
