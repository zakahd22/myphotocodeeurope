<?php
include '../../sessio.php';
//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$ID = $_POST['id'];
$type = $_POST['menu'];


switch($type){
    case 2:
        echo <<<HTML
        <img src='images/icons/submenu/dongles_payxprint.png' class='dMenuSelected2' onclick='setProfileAndSubmenu("payxprint", "dongles", {$USERID})' id='dongles'>
        <img src='images/icons/submenu/orders_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint" , "orders" , $USERID)' id='orders'>
        <img src='images/icons/submenu/reports_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint" , "reports" , $USERID)' id='reports'>
        <img src='images/icons/submenu/addNew_payxprint.png' class='dMenu2' onclick='addNew(2 , $USERID)' id='addnew' style='float: right;z-index: 10;position: absolute;right: 0px;'>
HTML;
        break;
    
    case 3:
         echo <<<HTML
            <img src='images/icons/submenu/dongles_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint", "dongles", {$USERID})' id='dongles'>
            <img src='images/icons/submenu/orders_payxprint.png' class='dMenuSelected2' onclick='setProfileAndSubmenu("payxprint" , "orders" , $USERID)' id='orders'>
            <img src='images/icons/submenu/reports_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint" , "reports" , $USERID)' id='reports'>
HTML;
        break;
    
    case 4:
         echo <<<HTML
            <img src='images/icons/submenu/dongles_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint", "dongles", {$USERID})' id='dongles'>
            <img src='images/icons/submenu/orders_payxprint.png' class='dMenu2' onclick='setProfileAndSubmenu("payxprint" , "orders" , $USERID)' id='orders'>
            <img src='images/icons/submenu/reports_payxprint.png' class='dMenuSelected2' onclick='setProfileAndSubmenu("payxprint" , "reports" , $USERID)' id='reports'>
HTML;
        break;
}   

?>