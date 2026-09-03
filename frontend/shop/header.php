<a href='http://digital-centre.com' target='_blank'><img src='images/logo.png' id='logoIMG'></a>
<?php
    include $_SERVER['DOCUMENT_ROOT'] . "/includes/classes/Mobile.php";
    if (Mobile::is_mobile()) {
        
   
?>
<div class='blok' style='padding-top:30px'>
    <img src="images/destacat1.jpg" style="width:100%;"/>
</div>

<?php 
 } else {
?>     
  <div class='blok' style='padding-top:30px'>
<ul id="sb-slider" class="sb-slider">
    <li><img src="images/destacat1.jpg" alt="image2"/></li>
</ul>
    
    <div id="shadow" class="shadow"></div>

				<div id="nav-arrows" class="nav-arrows">
					<a href="#" style='right:11%;top:25%;'>>Next</a>
					<a href="#" style='left:11%;top:25%;'>Previous</a>
				</div>
</div>   
        
<?php

 }
?>