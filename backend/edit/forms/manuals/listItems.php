<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$json = json_decode($_POST["data"], TRUE);
$x = $json[0];
$id = $json[1];
$content = "";
$content .= <<<HTML
        <select name='olddata[]' id='d_old{$id}' style='width:200px' onclick='cambiaTryMe("{$x}", "{$id}", "old", this)'>
        <option value=0>None</option>
HTML;
$CLD_CON->OpenRs("SELECT `id` , `data`, `desc` FROM manualsItems WHERE type='{$x}' GROUP BY `data`");

while ($CLD_CON->FetchArray()) {
    $itemID = $CLD_CON->GetArrayField("id");
    $itemData = $CLD_CON->GetArrayField("data");
    $itemDesc = $CLD_CON->GetArrayField("desc");
    $content .= "<option value='$itemID' data-cadena='$itemData' data-desc='$itemDesc'>$itemData";
    if ($itemDesc) {
        $content .= "- $itemDesc</option>";
    } else {
        $content .= "</option>";
    }
    
}
$content .= "</select>";
echo $content;