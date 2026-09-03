<?php
include '../../sessio.php';
require_once G_PATH . 'common/conexio.php';

//TYPE 
// 1 = MENU DE LA LLISTA
// 2 = MENU DE LA INFORMACIÓ
// 3 = MENU DE EDITAR
$type = $_POST['menu'];
if(isset($_POST['id'])){
    $ID = $_POST['id'];
}
$html = "";

if($type == 1){
    if($_SESSION['USERTYPE'] != 5 && $_SESSION['USERTYPE']!=6){				
        $html .= "<img src='images/icons/submenu/addNew.png' class='dMenu2' onclick='edit(62,0);' style='float: right;z-index: 10;position: absolute;right: 0px;'>";
    }
}

if($type == 2){
    if($_POST['trashed']){
        $html .= "<img src='images/icons/submenu/infoVerd.png' class='dMenuSelected2' onclick='profile(\"events\" , \"info\" , $ID, 1)' id='info'>";
    }
    
    else{
        if($_SESSION['USERTYPE'] <= 6){
            $html .= "<img id='no-menu' class='no-menu' src='images/web/customization.PNG' style='position: absolute;z-index: 0;height: 175%;top: -80%;left: 8%;'>";
            $html .= "<img src='images/icons/submenu/infoVerd.png' class='dMenuSelected2' onclick='profile(\"events\" , \"info\" , $ID)' id='info'>";
        }
        else{
            $html .= "<img id='no-menu' class='no-menu'  src='images/web/customization.PNG' style='position: absolute;z-index: 0;height: 179%;top: -80%;left:5%;'>";
        }
        if($_SESSION['USERTYPE'] != 6){
        $html .= "<img src='images/icons/submenu/cloud.png' class='dMenu2' onclick='profile(\"events\" , \"cloud\" , $ID)' id='cloud'>";
        $html .= "<img src='images/icons/submenu/eBooth.png' class='dMenu2' onclick='profile(\"events\" , \"photobooths\" , $ID)' id='Photobooths_2'>";
        $html .= "<img src='images/icons/submenu/printPhoto.png' class='dMenu2' onclick='profile(\"events\" , \"printPhoto\" , $ID)' id='printPhoto'>";
        $html .= "<img src='images/icons/submenu/PyV.png' class='dMenu2' onclick='profile(\"events\" , \"Photos\" , $ID)' id='Photos_2'>";
        //$html .= "<img src='images/icons/submenu/eventManager.png' class='dMenu2' onclick='profile(\"events\" , \"eventManager\" , $ID)'>";
        $html .= "<img src='images/icons/submenu/emailsEvent.png' class='dMenu2' onclick='profile(\"events\" , \"emails\" , $ID)' id='emails'>";
        $html .= "<img src='images/icons/submenu/sharedemail.png' class='dMenu2' onclick='profile(\"events\" , \"templateEmail\" , $ID)' id='templateEmail'>";
        }
    }
}

if($type == 3){
    $html .= <<<HTML
    <!--
    <div class='dMenuSelected2' onclick='edit(\"events\" , \"cloud\" , $ID)'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/cloud.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>Cloud</p>
    </div>
    <div class='dMenu2' onclick='edit(\"events\" , \"photobooths\" , $ID)'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/eBooth.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>PhotoBooth</p>
    </div>
    <div class='dMenu2' onclick='edit(\"events\" , \"printPhoto\" , $ID)'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/photoPrint.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>Print Photo</p>
    </div>
    <div class='dMenu2' onclick='edit(\"events\" , \"eventManager\" , $ID)'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/eventManager.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>EventManager</p>
    </div>
    <div class='dMenu2' onclick='addNew("events");' style='float:right;'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/addNew.png' class='imgMenu2' >
            </p>
            <p class='tMenu2'>Add New</p>
    </div>
    <div class='dMenu2' onclick='setSection(\"events\" , 1)'style='float:right;'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/list.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>Events List</p>
    </div>

    <div class='dMenu2'  onClick='setSection("events" , 2 , $ID)' style='float:right;'>
            <p class='iMenu2'>
                    <img src='images/icons/submenu/ull.png' class='imgMenu2'>
            </p>
            <p class='tMenu2'>Profile</p>
    </div>
    -->
HTML;
}

echo $html;
?>