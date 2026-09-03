<?php
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}
$title = "$sn - PhotoBooth to : ";

$content .= "<div class='popup-text popup-margin-bottom'>";
    $content .= "Select Finish Factory Product or to incomplet product";
$content .= "</div>";

$content .= "<div style='width:450px;' class='popup-margin-top popup-center'>";
$content .= "<img src='images/web/toStock.jpg' style='width:150px; height:150px;float:left;display:inline;border:10px outset gray;cursor:pointer;' title ='Finish Factory Product(STOCK)' onclick='edit(45 , $ID);'>";
$content .= "<img src='images/web/toIncomplete.jpg' style='width:150px; height:150px;float:right;display:inline;border:10px outset gray;cursor:pointer;' title ='To Incomplet Product' onclick='edit(54 , $ID);'>";
$content .= "</div>";

$buttons = "";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);
?>