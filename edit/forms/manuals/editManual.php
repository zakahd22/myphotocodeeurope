<?php

$manualID = $_POST['id'];


$content .= '<link rel="stylesheet" type="text/css" href="sections/manuals/resources/manuals.css">';
$content .= '<script type="text/javascript" src="sections/manuals/functions/functions.js"></script>';


$title .= "Edit Manual";

$content .= "";
$content .= "";

    $baseController = new baseController();
    $baseController->createModel('manuals');
    
    $manualList = $baseController->manualsModel->getOne($manualID);
    $manuals = $manualList['manuals'];    
    $manualsBooths = $manualList['manualsBooths'];
    $manualsItems = $manualList['manualsItems'];
    
    
    $version = explode(", ", $manuals[0]['version']);
    

// Start painting the form
$content .= <<<HTML
        <form id='formulariedit' method='post' enctype='multipart/form-data'>
        <input type="hidden" name="manualID" value="$manualID">
        <div class="formArea">
            <div class="titol" id="titolName">Name</div>
            <div class="option" id="titolName">
                <input type="text" id="name" name="name" value="{$manuals[0]['name']}" size=40></input>
            </div>
        </div>
                
        <div class="formArea">
            <div class="titol" id="titolVersion">Version</div>
            <div class="option" id="optionVersion">
HTML;

$ver = ["Britta", "Expression", "Evo"];
foreach ($ver as $v){
    if (in_array($v, $version)) {
        $content .= '<div class="bchecked"><input type="checkbox" onchange="selecciona(this)" id="'.$v.'" name="version[]" value="'.$v.'" checked>'.$v.'</input></div>';
    }else{
        $content .= '<div class="bcheck"><input type="checkbox" onchange="selecciona(this)" id="'.$v.'" name="version[]" value="'.$v.'">'.$v.'</input></div>';
    }
   
}           

$content .= <<<HTML

            </div>
        </div>
       
        
        <div class="formArea">
            <div class="titol" id="titolActive">Active Items</div>
            <div class="option" id="optionActive">
        
HTML;

foreach ($manualsItems as $item) {

    $type = $item['type'];
    $id = $item['id'];
    $data = $item['data'];
    $desc = $item['desc'];
    
    $content .= <<<HTML
            
                <div class="itemLine" id="newline$id" name="activeitems">
                    <div class="itemType" id="active$id">&nbsp;
                    </div>
                    <div class="itemData" id="activedata$id">
                            $data
HTML;
    if ($desc) {
        $content .= " - ";
    }
    $content .= <<<HTML
                    $desc
                    </div>
                    <div class="bcheck" id="td_active$id">
                       <input type="checkbox" name="todelete[]" id="deletecheck$id" value=$id>Delete</input>
                    </div>
                </div>
            
HTML;
}



$content .= <<<HTML
            </div>
        </div>
         <div class="formArea">
            <div class="titol" id="titolReuse">Reuse Items</div>
            <div class="option" id="optionReuse">
            <script>itemLine ('old', '00')</script>
        
        
            </div>
        </div>
        <div class="formArea">
            <div class="titol" id="titolNew">New Items</div>
            <div class="option" id="optionNew">

            <script>itemLine ('new', '00')</script>
        
            </div> 
        </div>        
        <div class="formArea">
            <div class="titol" id="titolBooths">Compatible Booths</div>
            <div class="option" id="optionBooths">

HTML;

//"<div class="bcheck" name="checkall"><input type="checkbox" onchange="seleccionatot(this), selecciona(this)" id="booth_all" name="booths[]" value="0">All</input></div>"

//======== NOW PRINT THE MANUALS ===============================================

$booths = explode(",", $manualsBooths[0]['booth_id']);

$CLD_CON->OpenRs("SELECT id , name FROM CLD_boothTypes");


if ($booths[0] == 0) { //check if booth 0 (all) is selected, because it's not the list really
    $content .= '<div class="bchecked" name="checkall"><input type="checkbox" onchange="seleccionatot(this), selecciona(this)" id="booth_all" name="booths[]" value="0" checked>All</input></div>';
    while ($CLD_CON->FetchArray()) {
        $booth_id = $CLD_CON->GetArrayField("id");
        $booth_name = $CLD_CON->GetArrayField("name");

        $content .= '<div class="bchecked" name="check"><input type="checkbox" onchange="selecciona(this)" id="booth_' . $booth_id . '" name="booths[]" value="' . $booth_id . '" checked>' . $booth_name . '</input></div>';
    }
} else {
    $content .= '<div class="bcheck" name="checkall"><input type="checkbox" onchange="seleccionatot(this), selecciona(this)" id="booth_all" name="booths[]" value="0">All</input></div>';
    while ($CLD_CON->FetchArray()) {
        $booth_id = $CLD_CON->GetArrayField("id");
        $booth_name = $CLD_CON->GetArrayField("name");
        if (in_array($booth_id, $booths)) {
            $content .= '<div class="bchecked" name="check"><input type="checkbox" onchange="selecciona(this)" id="booth_' . $booth_id . '" name="booths[]" value="' . $booth_id . '" checked>' . $booth_name . '</input></div>';
        } else {
            $content .= '<div class="bcheck" name="check"><input type="checkbox" onchange="selecciona(this)" id="booth_' . $booth_id . '" name="booths[]" value="' . $booth_id . '">' . $booth_name . '</input></div>';
        }
    }
}

$content .= "</div></div>
        <div class='buttons'>
        <input id='save' type='submit' class='popup-confirm' value='Save'>
        <input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>
        </div></form>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);