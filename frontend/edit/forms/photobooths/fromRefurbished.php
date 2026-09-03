<?php

$baseController->createModel('App_booths');

$pbs = $baseController->App_boothsModel->getBoothWhereid($ID);

if($pbs){
    $sn = $pbs[0]["serialnumber"];
}

$title = " Select what do with PhotoBooth $sn";
$content = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Sold (Assign existing owner or create a new owner). <br>Damaged (The Photobooth is Damaged). <br> Incomplete (The Photobooth not have all components).";
$content .= "</div>";

$content .= "<div class='popup-row popup-center' style='width:500px'>";
    $content .= "<div class='popup-col'>";
        $content .= "<img src='images/web/sold.jpg' style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;' onclick='edit(40 , $ID);'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<img src='images/web/toDamage.jpg' style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;' onclick='edit(48 , $ID);'>";
    $content .= "</div>";
    $content .= "<div class='popup-col'>";
        $content .= "<img src='images/web/toIncomplete.jpg'style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;' onclick='edit(51 , $ID);'>";
    $content .= "</div>";
$content .= "</div>";

$buttons = "";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);
