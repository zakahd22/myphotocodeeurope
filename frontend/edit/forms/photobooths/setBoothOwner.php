<?php

$baseController->createModel('App_booths');

$pbs = $baseController->App_boothsModel->getBoothWhereid($ID);

$title = "Set Owner of PhotoBooth";
$content = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Select create new Owner or select a Old Owner";
$content .= "</div>";

$content .= "<div class='popup-row popup-center' style='width:392px;'>";
    $content .= "<div class='popup-col popup-margin-top' style='margin-right:10px;'>";
        $content .= "<img src='images/web/newOwner.jpg' style='width:150px; height:150px; border:10px outset gray; cursor:pointer;' title ='New Owner' onclick='edit(41 , $ID);'>";
    $content .= "</div>";
    $content .= "<div class='popup-col popup-margin-top' style='margin-right:10px;'>";
        $content .= "<img src='images/web/oldOwner.jpg' style='width:150px; height:150px; border:10px outset gray; cursor:pointer;' title ='Old Owners' onclick='edit(42 , $ID);'>";
    $content .= "</div>";
$content .= "</div>";

$buttons = "";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);

