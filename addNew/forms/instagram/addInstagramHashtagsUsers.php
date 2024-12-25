<?php
//header('Content-Type: application/json');
require_once dirname(__FILE__) . '/../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

if(isset($_SESSION['USERID'])){
    $USERID = $_SESSION['USERID'];
}

$title = "New Hashtags and User Suggestions";
$content = "";
$buttons = "";


$content .= "<script src='sections/instagram/resources/js/instagramAddNew.js'></script>";
$content .= "<link rel='stylesheet' href='sections/instagram/resources/css/instagramAddNew.css'>";

$content .= <<<HTML
        <div id='popup_conten'>
             <form id='addSuggestions' method='post'>
                <div class"posDiv" style='width:300px;'>
                    <b>Type</b>
        <br>
                </div>  
                <select id='type' class='popupInputLarge' name='type' value="" >
                    <option id='hashtag' class='' value='hashtag'>Hashtag</option>
                    <option id='username' class='' value='username'>Username</option>
                </select>
                <br>
                <div class"posDiv" >
        <br>
                    <b>Instagram Suggestions</b>
        <br>
                </div>               
               <textarea style='width:100%;height:200px' name='words' class='popupInputLarge' value='' placeholder='Insert space-separated words. The @ and # will be removed, you can put the words starting with these 2 characters and the users or hashtags will be entered correctly.' ></textarea>
            </form>
        </div>
        
HTML;

$buttons .= "<input type='button' class='popup-confirm' value='Save' onclick='saveInstagramHashtagUsers(); hidePopupv2();'>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);