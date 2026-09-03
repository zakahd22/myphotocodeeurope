<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";


echo '<div class="collage">';
    echo '<div class="button">';
        echo "<img class='button' src='images/icons/submenu/selectCollages.png' style='width:55%;cursor:pointer;' onclick='edit(73 , $USERID);'>";
    echo '</div>';    
        echo "<div class='img_collage'>";
        echo "<img src='images/web/custom/collages.png' style='width:100%;'>";
    echo '</div>';
echo '</div>';
echo "<div id='selectedCollage' class='selectedCollage hidden'>
        
    </div>";


        
