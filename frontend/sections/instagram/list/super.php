<?php

$CLD_CON2 = clone($CLD_CON);

$baseController = new baseController();
$baseController->createModel('InstagramSuggestions');

$CLD_CON->OpenRs("SELECT word, type, numPrint, numFollowers, pais, isVerified, fbid
                  FROM InstagramSuggestions ORDER by type");


$html = "<ul class='regDongleUL regDongleUL_Title'>";
$html .= "<li  style='width:14%;'><b>Word</b></li>";
$html .= "<li  style='width:14%;'><b>Type</b></li>";
$html .= "<li  style='width:14%;'><b>numPrint</b></li>";
$html .= "<li  style='width:14%;'><b>numFollowers</b></li>";
$html .= "<li  style='width:14%;'><b>pais</b></li>";
$html .= "<li  style='width:14%;'><b>isVerified</b></li>";
$html .= "<li  style='width:14%;'><b>fbid</b></li>";
$html .= "</ul>";
    
while ($CLD_CON->FetchArray()) {
   
    $word = $CLD_CON->GetArrayField("word");
    $type = $CLD_CON->GetArrayField("type");
    $numPrint = $CLD_CON->GetArrayField("numPrint");
    $numFollowers = $CLD_CON->GetArrayField("numFollowers");
    $pais = $CLD_CON->GetArrayField("pais");
    $isVerified = $CLD_CON->GetArrayField("isVerified");
    $fbid = $CLD_CON->GetArrayField("fbid");
    
//    
//    
//    $dong = $baseController->Fcode_regModel->getDateEndFcode($idDongle, $today);

   
    
//    $html .= "<ul class='regDongleUL' onclick='edit(71 , $idDongle)'>";
    $html .= "<ul class='regDongleUL'>";
    $html .= "<li  style='width:14%;' title='Word'> &nbsp $word</li>";
    $html .= "<li  style='width:14%;' title='Type'> &nbsp $type</li>";
    $html .= "<li  style='width:14%;' title='numPrint'> &nbsp $numPrint</li>";
    $html .= "<li  style='width:14%;' title='numFollowers'> &nbsp $numFollowers</li>";
    $html .= "<li  style='width:14%;' title='numFollowers'> &nbsp $pais</li>";
    $html .= "<li  style='width:14%;' title='numFollowers'> &nbsp $isVerified</li>";
    $html .= "<li  style='width:14%;' title='numFollowers'> &nbsp $fbid</li>";
    $html .= "</ul>";
}
$html .= "</div>";

echo $html;

//$s = "dongles";
//$color = "orange";
//include '../../pagescount.php';
