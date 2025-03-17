<?php
require_once G_PATH . 'common/global.php';
$html = getAll();
function showManuals($USERID, $pbmodel) {
    $baseController = new baseController();
    $baseController->createModel('manuals');

    //This extracts the list of Booths related to the specified model
    $manualList = $baseController->manualsModel->getManuals($USERID, $pbmodel);


    $manualNames = $manualList['manuals'];

    usort($manualNames, function ($a, $b) {
        return $a['id'] > $b['id'];
    });

    //print the title section
    $html .= "";
    $html .= "<div class='gran'>";
    $html .= "<h2>Photobooth Manuals - $pbmodel</h2>";

    foreach ($manualNames as $manual) {
        $html .= "<div class='element'>
                <div class='nom'>" . $manual['name'] . "</div><div class='contenidor'>";
        $itemList = $baseController->manualsModel->getItems($manual['id']);
        $man = $itemList['manuals'];
        $item = $itemList['manualsItems'];

        foreach ($item as $it) {
            $que = $it['type'];
            $data = $it['data'];
            $html .= printMe($que, $data, $pbmodel, $manual['name']);
        }

        $html .= "</div></div>";
    }
    $html .= "</div>";
    return $html;
}

function printMe($que, $data, $pbmodel, $alt) {
    switch ($que) {
        case "video":
            // search if a video tutorial about this exists, and prints it
            //$archiu = strtolower(str_replace(' ', '_', $data));

            $ruta = dirname(__FILE__) . "/../../../manuals/videos/" . $data;
            if (file_exists($ruta)) {
                $codi = "<div class='link'><img src='images/icons/submenu/watch-video.png' height='32' alt='$alt' onclick='showVideo(" . '"' . $data . '"' . ")'></div>";
                return $codi;
            } else {
                $codi = "<div class='linkoff'><img src='images/icons/submenu/watch-video.png' alt='$alt' height='32'></div>";
                return $codi;
            }
            break;
        case "pdf":
            // search if a pdf about this exists, and prints it
            $pbmodel = strtolower($pbmodel);
            //$archiu = strtolower(str_replace(' ', '_', $data));

            $ruta = dirname(__FILE__) . "/../../../manuals/" . $data;
            if (file_exists($ruta)) {
                $codi = "<div class='link'><a href='manuals/$data' download><img src='images/icons/submenu/download-button.png' alt='$alt' height='32'></a></div>";
                return $codi;
            } else {
                $codi = "<div class='linkoff'><img src='images/icons/submenu/download-button.png' alt='$alt' height='32'></div>";
                return $codi;
            }
            break;
        case "youtube":
            $codi = <<<HTML
                <div class='link'><img src='images/icons/submenu/watch-video.png' onclick='mostraTube("$data")' alt='$alt'></div>
HTML;
            return $codi;
    }
}

//function buscaItems($codi) {
//    $retorn = ['id'=>'','type'=>'','data'=>''];
//    $CLD_CON->OpenRs("SELECT manuals.id, manualsItems.type, manualsItems.data FROM manuals, manualsItems WHERE manualsItems.manual_id = manuals.id AND manuals.id = $codi");
//    if ($CLD_CON->FetchArray()) {
//        $id = $CLD_CON->GetArrayField("manuals.id");
//        $type = $CLD_CON->GetArrayField("manualsItems.type");
//        $data = $CLD_CON->GetArrayField("manualsItems.data");
//        
//    $retorn[] = ['id'=>$id, 'type'=>$type, 'data'=>$data];
//    return $retorn;
//    }
//}
function getAll(){
    $baseController = new baseController();
    $baseController->createModel('manuals');
    
    $manualList = $baseController->manualsModel->getAll();
    $manuals = $manualList['manuals'];
    
    $manualsBooths = $manualList['manualsBooths'];
    $manualsItems = $manualList['manualsItems'];
    
    $html .= <<<HTML
            <div class='gran'>
                <div class='elementheader'>
                    <div class='check'>&nbsp;</div>
                    <div class='nom2'><strong>Name</strong></div>
                    <div class='cell'><strong>Version</strong></div>
                    <div class='cell2'><strong>Items</strong></div>
                    <div class='cell3'><strong>Compatible Booths</strong></div>
                    <div class='cellItems' style='width:64px;'>&nbsp;</div>
                </div>  
HTML;
            
        for ($x=0; $x < count($manuals); $x++){
            
                $manualsBoothsA = explode(",", $manualsBooths[$x]['booth_id']);
                $manualsItemsA = explode(",", $manualsItems[$x]['id']);
     
                $boothsCount = count($manualsBoothsA);
                $itemsCount = count($manualsItemsA);
            
                $hiddenLeaf = hiddenLeaf($manualsItemsA, $manualsBoothsA);
                str_replace('_', ' ', $manuals[$x]['name']);
                
            $html .= <<<HTML
                    <div class='element' id={$manuals[$x]['id']} >
                    <div class='check'>                        
HTML;
             
                        
                        
            if ($_SESSION['USERTYPE'] ==1) {
                $html .= <<<HTML
                    
                        <input type="checkbox" name="seleccionat" id="{$x}" value="{$manuals[$x]['id']}">
                        
HTML;

            }
                        $html .= <<<HTML
                        </div>
                        <div class='nom2' onclick="expandir('sub{$manuals[$x]['id']}')" style="cursor:pointer">{$manuals[$x]['name']}</div>
                        <div class='cell'onclick="expandir('sub{$manuals[$x]['id']}')" style="cursor:pointer">{$manuals[$x]['version']}</div>
                        <div class='cell2' onclick="expandir('sub{$manuals[$x]['id']}')" style="cursor:pointer">{$itemsCount}</div>
HTML;
                        
                        if(in_array("0", $manualsBoothsA)){
                        $html .= <<<HTML
                                <div class='cell3' onclick="expandir('sub{$manuals[$x]['id']}')" style="cursor:pointer">All</div>
HTML;
                        }
                        else{
                        $html .= <<<HTML
                                <div class='cell3' onclick="expandir('sub{$manuals[$x]['id']}')" style="cursor:pointer">{$boothsCount}</div>
HTML;
                        }
                        
                            $html .= <<<HTML
                                <div class='cellItems'>
HTML;
                             if ($_SESSION['USERTYPE'] ==1) {
                                 $html .= <<<HTML
                                <img src='images/web/edit.png' onclick="edit(78, {$manuals[$x]['id']})" style="cursor:pointer">
                            <img src='images/web/papelera.png' onclick="deleteManual({$manuals[$x]['id']})" style="cursor:pointer">
HTML;
                            
                        

                          } 
                            $html .= <<<HTML
                             </div>       
                        <div class='expandit hidden' id='sub{$manuals[$x]['id']}'>{$hiddenLeaf}</div>
                    </div> 
HTML;
                        
                        
        }
        $html .= "<div class='element'>"
                    ."<div>Selected</div>";
        if ($_SESSION['USERTYPE'] ==1) {
                    $html .= "<img src='images/web/papelera.png' onclick='deleteSelectManual()' style='cursor:pointer'>";      
        }            
                $html .= "</div>"
            . "</div>";
            
            
     return $html;   
}



function hiddenLeaf($items, $booths) {
    // List of items
    $baseController = new baseController();
    $baseController->createModel('manuals');
    $codi = "<div class='supergran'><h2>Items</h2>";
    foreach ($items as $item) {
        $itemA = $baseController->manualsModel->getItemWhereid($item);
        $codi .= "<div class ='superitem'><div class ='{$itemA[0]['type']}'></div>";
//        . "<div class='superdata'>{$itemA[0]['data']}</div>";
        switch ($itemA[0]['type']) {
            case "pdf":                
                if(strlen($itemA[0]['data']) > 40){
                    $text = substr($itemA[0]['data'],0,35)."...";
                    $text2 = substr($itemA[0]['data'],-4);
                }else{
                    $text = $itemA[0]['data'];
                }                
                $codi .= "<div class='superdata'><a href='manuals/{$itemA[0]['data']}' download>$text$text2</a></div>";
                array_push($pdf, $itemA[0]['data']);
                
                break;
            case "youtube":
                if(strlen($itemA[0]['desc']) > 46){
                    $text = substr($itemA[0]['desc'],0,45)."...";
                }else{
                    $text = $itemA[0]['desc'];
                }
                $codi .= "<div class='superdata'><a onclick='mostraTube(" . '"' . $itemA[0]['data'] . '"' . ")'> $text</a></div>";
                break;
            case "video":
                $codi .= "<div class='superdata'><a onclick='showVideo(" . '"' . $itemA[0]['data'] . '"' . ")'>{$itemA[0]['data']}</a></div>";
                break;
        }
        $codi .= "</div>";
    }
    $codi .= "</div><div class='supergran'><h2>Booths</h2>";

    // List of booths
    $baseController = new baseController();
    $baseController->createModel('App_booths');
    foreach ($booths as $booth) {
        if ($booth == 0) {
            $codi .= 'All';
        } else {
            $boothN = $baseController->App_boothsModel->getBoothNameWhereid($booth);

            $codi .= "<div class='boothname'>{$boothN[0]['name']}</div>";
        }
    }
    $codi .= "</div>";
    return $codi;
}