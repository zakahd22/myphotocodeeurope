<?php
//header('Content-Type: application/json');
require_once dirname(__FILE__) . '/../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

if(isset($_SESSION['USERID'])){
    $USERID = $_SESSION['USERID'];
}

$title = "New Financing Dongle";
$content = "";
$buttons = "";


$content .= "<script src='sections/financingCode/resources/js/fCodeAddNew.js'></script>";
$content .= "<link rel='stylesheet' href='sections/financingCode/resources/css/fCodeAddNew.css'>";

$content .= <<<HTML
        <div id='popup_conten'>
             <form id='addFinancing' method='post'>
                <div class"posDiv">
                    Dongle String
                </div>
                <input class='tableInput' name='dongelString' value='' >
            </form>
        </div>
        
HTML;

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveFinancingCode(); hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);