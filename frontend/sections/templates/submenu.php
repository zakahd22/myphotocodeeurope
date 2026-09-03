<?php
include '../../sessio.php';

$USERTYPE = $_SESSION['USERTYPE'];
$html = "<img src='images/icons/submenu/collages.png' class='dMenuSelected2' onclick='profile('templates' , 'collages')' id='collages'>";

if($USERTYPE == 1){
    $html .= "<img src='images/icons/submenu/addNewT.png' class='dMenu2' onclick='edit(76,0);' style='float: right;z-index: 10;position: absolute;right: 0px;'>";
    $html .= "<img src='images/icons/submenu/DeleteT.png' class='dMenu2' onclick='edit(80,0);' style='float: right;z-index: 11;position: absolute;right: 121px;'>";
}

echo $html;