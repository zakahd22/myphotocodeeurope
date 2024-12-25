<?php
require_once '../../../common/global.php';

$id = $_REQUEST['id'];
if(isset($_REQUEST['D3'])){
    $ruta= "id=".$id."&D3=1";
}
else{
    if(isset($_REQUEST['video'])){
        $ruta= "id=".$id."&video2=1";
    }
    else{
        if(isset($_REQUEST['gif'])){
            $ruta= "id=".$id."&gif=1";;
        }
        else{
            $ruta= "id=".$id;     
        }
    }
}

if(isset($_REQUEST['v3d'])){
   $ruta= "id=".$id."&video2=1&v3d=1"; 
}

//echo "<div>";

include G_PATH . "includes/classes/Mobile.php";

if (Mobile::is_mobile()) {
//    echo "<div id='popupClose' style='background-repeat: no-repeat;background-size: 100px;width: 101px;height: 101px;position:relative;' onclick='disablePopup();'></div>";
}
else{
//    echo "<div id='popupClose' style='position:relative;' onclick='disablePopup();'></div>";
}

//echo <<<HTML
//        <div style='width: 100%;text-align:center;' align="center">            
//            <iframe src='assets/php/templates/popup_email_form.php?{$ruta}' style='width:446px;height:330px;' frameborder="0"></iframe>
//        </div>
//    </div>
//HTML;

//include_once('assets/php/templates/popup_email_form.php?'.$ruta);
$content = "<iframe src='assets/php/templates/popup_email_form.php?{$ruta}' frameborder='0'></iframe>";

$title = 'Insert your email';

$buttons = "";

//$array_result = array('content' =>$content);
$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);

echo json_encode($array_result);
