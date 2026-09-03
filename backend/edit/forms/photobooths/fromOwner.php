<?php
$CLD_CON->OpenRs("SELECT serialnumber FROM App_booths WHERE idBooth=$ID");
if($CLD_CON->FetchArray()){
    $sn = $CLD_CON->GetArrayField("serialnumber");
}
$title = "$sn - PhotoBooth to : ";
$content .= "<div class='popup-text popup-margin-bottom'>";
$content .= "Select Returned or Sold:";
$content .= "</div>";

$content .= "<div style='width:450px;' class='popup-margin-top popup-center'>";
$content .= "<img src='images/web/returned.jpg' style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;margin-right:10px;'  onclick='edit(56 , $ID);'>";
//$content .= "<img src='images/web/returneDamaged.jpg' style='width:28%;float:left;display:inline;border:10px outset gray;cursor:pointer;margin:1%' title ='To Incomplet Product' onclick='edit(57 , $ID);'>";
$content .= "<img src='images/web/sold.jpg' style='width:150px; height:150px;display:inline;border:10px outset gray;cursor:pointer;' onclick='edit(40 , $ID);'>";
$content .= "</div>";
$buttons = "";

$array_result = array('title' => $title, 'content' =>$content, 'buttons' =>$buttons);
echo json_encode($array_result);
?>
