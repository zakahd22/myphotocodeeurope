<?php
$ID = $_POST['id'];
$submenu = $_POST['s2'];
if($submenu == 'cloud'){
?>
<div class='dMenuSelected3' <?php echo "onclick='profile(\"events\" , \"infoCloud\" , $ID)'";?> style='margin-left:7%;'>
Info
</div>
<div class='dMenu3' <?php echo "onclick='profile(\"events\" , \"backgroundCloud\" , $ID)'";?>>
Background
</div>
<div class='dMenu3' <?php echo "onclick='profile(\"events\" , \"bannerCloud\" , $ID)'";?>>
Banner
</div>
<div class='dMenu3' <?php echo "onclick='profile(\"events\" , \"questionsCloud\" , $ID)'";?>>
Questions
</div>
<?php
}
if($submenu == 'printPhoto'){
?>
<div class='dMenuSelected3' <?php echo "onclick='profile(\"events\" , \"ppFrames\" , $ID)'";?> style='margin-left:7%;'>
FRAMES
</div>
<div class='dMenu3' <?php echo "onclick='profile(\"events\" , \"ppLogo\" , $ID)'";?>>
LOGO
</div>
<div class='dMenu3' <?php echo "onclick='profile(\"events\" , \"ppText\" , $ID)'";?>>
TEXT
</div>
<?php
}
?>