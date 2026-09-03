<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
require_once G_PATH . 'common/global.php';


$videoformats = ['webm', 'mkv', 'ogg', 'ogv', 'gifv', 'avi', 'mov', 'qt', 'wmv', 'mp4', 'mpg', 'mpeg', 'm4v', '3gp', '3g2'];
$clearorphans=[];

$title .= "Manage Items";

$content .= '<link rel="stylesheet" type="text/css" href="sections/manuals/resources/manuals.css">';
$content .= '<script type="text/javascript" src="sections/manuals/functions/functions.js"></script>';

$content .= "";
$content .= "";


$totaldata = [];


$content .= "<div class='contingut'><div class='gran'>";
$content .= <<<HTML

                <div class='elementheader'>
                    <div class='headerType'><strong>Type</strong></div>
                    <div class='headerData'><strong>Data</strong></div>
                    <div class='headerDesc'><strong>Description</strong></div>
                    <div class='headerManuals'><strong>Manuals</strong></div>
                </div>  
        
HTML;

$CLD_CON->OpenRs('SELECT manualsItems.type, manualsItems.data, manualsItems.`desc` , GROUP_CONCAT(DISTINCT manuals.id) as manualID, GROUP_CONCAT(DISTINCT manuals.name) as manualName
                    FROM manualsItems, manuals
                    WHERE manuals.id = manualsItems.manual_id
                    GROUP BY manualsItems.data');
while ($CLD_CON->FetchArray()) {
    $id = $CLD_CON->GetArrayField("id");
    //$manual_id = $CLD_CON->GetArrayField("manual_id");
    $type = $CLD_CON->GetArrayField("type");
    $data = $CLD_CON->GetArrayField("data");
    $description = $CLD_CON->GetArrayField("desc");
    $manualName = $CLD_CON->GetArrayField("manualName");
    $manualName = explode(",", $manualName);
    $manualID = $CLD_CON->GetArrayField("manualID");
    $manualID = explode(",", $manualID);


    $content .= "<div class='element'>
            <div class='fType'>$type</div>";

    switch ($type) {
        case "pdf":
            $content .= "<div class='fData'><a href='manuals/$data' download>$data</a></div>";
            $content .= "<div class='fDesc'><a href='manuals/$data' download>$description</a></div>";
            break;
        case "youtube":
            $content .= "<div class='fData'><a onclick='mostraTube(" . '"' . $data . '"' . ")'>$data</a></div>";
            $content .= "<div class='fDesc'><a onclick='mostraTube(" . '"' . $data . '"' . ")'>$description</a></div>";
            break;
        case "video":
            $content .= "<div class='fData'><a onclick='showVideo(" . '"' . $data . '"' . ")'>$data</a></div>";
            $content .= "<div class='fDesc'><a onclick='showVideo(" . '"' . $data . '"' . ")'>$description</a></div>";
            break;
    }


    $content .= '<div class="fManuals">';


    $i = 0;
    foreach ($manualName as $mn) {
        $content .= "$mn <input type='hidden' value='{$manualID[$i]}'><br />";
        $i++;
    }

    $content .= "</div></div>";

    array_push($totaldata, $data);
}
$content .= "</div>"
        . "<div class='gran'><div class='elementheader'>";

if ($USERTYPE == 1){
    $content .= "<div class ='left'><strong>Orphan Files</strong></div>"
        . "<div onClick='deleteOrphans()'><img src='images/web/papelera.png'></div>";
    
}
     
 $content .=  "</div>"
        . "<div class='left'>";


$dirpdf = G_PATH . 'manuals/';
$dirvid = G_PATH . 'manuals/videos/';

$pdf = scandir($dirpdf);
$vid = scandir($dirvid);

//$pdf = glob($dirpdf.'*{pdf}', GLOB_BRACE);



$content .= "<strong>PDF</strong><br />";

foreach ($pdf as $file) {
    if (!in_array($file, $totaldata) && pathinfo($dirpdf.$file, PATHINFO_EXTENSION) == "pdf") {
        $content .= "<a href='manuals/$file' target='_blank'>{$file} <input type='hidden' name='orphans[]' value='". $dirpdf.$file . "'></a><br />";
        array_push($clearorphans, "$dirpdf.$file");
    }
}

$content .= "</div>
    <div class='right'>";

$content .= "<strong>Videos</strong><br />";

foreach ($vid as $file) {
    
    if (!in_array($file, $totaldata) && in_array(pathinfo($dirvid.$file, PATHINFO_EXTENSION), $videoformats)) {
        $content .= "<a onclick='showVideo(" . '"' . $file . '"' . ")'>$file <input type='hidden' name='orphans[]' value='". $dirvid.$file . "'></a><br />";
        array_push($clearorphans, "$dirvid.$file");
    }
}

$content .= "</div>";
$content .= "</div></div>";

$buttons = "";



echo $content;