<?php
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}
$title = " Select what do with PhotoBooth $sn";
$content = "";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Select Sold (Assign existing owner or create a new owner.), Damaged (The Photobooth is Damaged) or Incomplete (The Photobooth not have all components)." ;
$content .= "</div>";

$content .= "<div style='width:450px;' class='popup-row popup-margin-top popup-center'>";
$content .= "<img src='images/web/toDamage.jpg' style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;margin-right:10px;' onclick='edit(48 , $ID);'>";
$content .= "<img src='images/web/toDistributorStock.jpg'style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;margin-right:10px;' onclick='edit(58 , $ID);'>";
$content .= "</div>";
$buttons = "";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);
?>