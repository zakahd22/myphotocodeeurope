<?php

include '../../sessio.php';

$USERTYPE = $_SESSION['USERTYPE'];
if ($USERTYPE == 1 || $USERTYPE == 6) {
    echo <<<HTML
    <img src='images/icons/submenu/manuals.png' class='dMenuSelected2' onclick='profile("manuals" , "super")' id='britta'>
    <img src='images/icons/submenu/items.png' class='dMenu2' onclick='profile("manuals" , "manageItems")' id='expression'>
    
    
    
HTML;
    if ($USERTYPE == 1){
        echo <<<HTML
    
    <img src='images/icons/submenu/addNewManual.png' class='dMenu2' onclick='edit(77,0);' style='float: right;z-index: 10;position: absolute;right: 0px;'>
HTML;
    }
     
    
} else {
    echo <<<HTML
        <img src='images/icons/submenu/britta-soft.png' class='dMenuSelected2' onclick='profile("manuals" , "britta")' id='britta'>
        <img src='images/icons/submenu/expression-soft.png' class='dMenu2' onclick='profile("manuals" , "expression")' id='expression'>

HTML;
}
