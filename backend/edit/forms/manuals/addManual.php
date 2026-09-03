<?php

$content .= '<link rel="stylesheet" type="text/css" href="sections/manuals/resources/manuals.css">';
$content .= '<script type="text/javascript" src="sections/manuals/functions/functions.js"></script>';


$title .= "New Manual";

$content .= "";
$content .= "";

$content .= "<div class='todo' style='margin-top:10px;'><form id='formulari' method='post' enctype='multipart/form-data'>";

$content .= <<<HTML

        <div class="formArea">
            <div class="titol" id="titolName">Name</div>
            <div class="option" id="optionName">
                <input type="text" id="name" name="name"></input>
            </div>
        </div>
        <div class="formArea">
            <div class="titol" id="titolVersion">Version</div>
            <div class="option" id="optionVersion">
                <div class="bcheck"><input type="checkbox" class="hidden" onchange="selecciona(this)" id="Britta" name="version[]" value="Britta">Britta</input></div>
                <div class="bcheck"><input type="checkbox" class="hidden" onchange="selecciona(this)" id="Expression" name="version[]" value="Expression">Expression</input></div>
                <div class="bcheck"><input type="checkbox" class="hidden" onchange="selecciona(this)" id="Evo" name="version[]" value="Evo">Evo</input></div>
                <div class="bcheck"><input type="checkbox" class="hidden" onchange="selecciona(this)" id="B5" name="version[]" value="Evo">B5</input></div>
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
                <div class="bcheck" name="checkall"><input type="checkbox" class="hidden" onchange="seleccionatot(this), selecciona(this)" id="booth_all" name="booths[]" value="0">All</input></div>
HTML;

$CLD_CON->OpenRs("SELECT id , name FROM CLD_boothTypes");
while ($CLD_CON->FetchArray()) {
    $booth_id = $CLD_CON->GetArrayField("id");
    $booth_name = $CLD_CON->GetArrayField("name");

    $content .= '<div class="bcheck" name="check"><input type="checkbox" class="hidden" onchange="selecciona(this)" id="booth_'.$booth_id.'" name="booths[]" value="'.$booth_id.'">'.$booth_name.'</input></div>';  
}   

$content .= <<<HTML
            </div>
        </div>
        <div class="buttons">
        <input id='save' type='submit' class='popup-confirm' value='Save'>
        <input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'>
        </div>
        </form>
HTML;


//$buttons .= "<input id='save' type='submit' class='popup-confirm' value='Save'>";
//$buttons .= "<input id='cancel' type='button' class='popup-cancel' value='Cancel' onclick='hidePopupv2();'> </form>";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);