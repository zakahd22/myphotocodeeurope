<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
include G_PATH . "includes/classes/phpqrcode.php";
require_once(G_PATH . "includes/classes/html2pdf/html2pdf.class.php");


$photoBooth = $_POST['id'];
$sn = $_POST['sn'];
$owner = $_POST['owner'];
$code = md5($photoBooth . "#" . $sn . "#" . $owner);

$qr1 = array(image => "question1", questionID => 15, texto => "Qr question 1", titol => "Printer Errors");
$qr2 = array(image => "question2", questionID => 18, texto => "Qr question 2", titol => "Screen Errors");
$qr3 = array(image => "question3", questionID => 2, texto => "Qr question 3", titol => "Logo Errors");
$qr4 = array(image => "question4", questionID => 37, texto => "Qr question 4", titol => "Control Board Error");
$qr = array($qr1, $qr2, $qr3, $qr4);

$PNG_TEMP_DIR = G_PATH . 'temp/';

if (!file_exists($PNG_TEMP_DIR))
    mkdir($PNG_TEMP_DIR);

$html = "<html><head></head><body>";

$html .= "<div style='width:100%;height:100%;background-color:#610B0B;'>";
$url_img = G_PAGE . "/images/web/panic.png";
$bg = G_PAGE . "/images/web/panicBackground.jpg');";


$html .= "<div style='width:100%;height:12%;border-radius:10px;position:absolute;top:0px;left:0px;z-index:1;'>";
$html .= "<img src='$bg' style='height:100%;width:100%;'/>";
$html .="</div>";

$html .= "<div style='width:100%;height:10%;border-radius:10px;z-index:2;position:absolute;top:0;left:0px;'>";
$html .= "<img src='$url_img' style='font-size:30pt;display:inline;float:left;height:100%;'/><h1 style='font-size:30pt;display:inline;padding:50px;color:white;'>PANIC INSTRUCTIONS</h1><p style='color:white;font-size:20pt;'>$sn</p>";
$html .="</div>";
$html .="<div style='height:12%;'></div>";
$x = 1;
foreach ($qr as $q) {
    $matrixPointSize = 10;
    $errorCorrectionLevel = 'L';
    $filename = $PNG_TEMP_DIR . $q['image'] . "$photoBooth.png";
    $info = G_PATH . "support/php/preguntes.php?code=$code&p=$photoBooth&q=" . $q['questionID'];
    QRcode::png($info, $filename, $errorCorrectionLevel, $matrixPointSize, 2);
    $url_img = G_PATH . "temp/" . $q['image'] . "$photoBooth.png";

    if ($x % 2 == 0) {
        $img = "<img src='$url_img' style='height:150px;display:inline;float:right;margin-left:20px;'/>";
        $html .= "<div style='height:160px;'>";
        $html .= "$img";
        $html .= "<div style='margin-left:20px;display:inline; float:left;background-color:white;width:550px;height:150px;text-align:center;'><h1 style='font-size:40pt;'>" . $q['titol'] . "</h1></div>";
        $html .= "</div>";
    } else {
        $img = "<img src='$url_img' style='height:150px;display:inline;float:left;margin-right:20px;margin-left:20px;'/>";
        $html .= "<div style='height:160px;'>";
        $html .= "$img";
        $html .= "<div style='display:inline; float:left;background-color:white;width:550px;height:150px;text-align:center;'><h1 style='font-size:40pt;'>" . $q['titol'] . "</h1></div>";
        $html .= "</div>";
    }
    $x++;
}

$html .= "</div>";
$html .="</body></html>";

$html2pdf = new HTML2PDF('P', 'A4', 'es');
$html2pdf->writeHTML($html);
$output_file = "./panicInstructions/panik$code.pdf";
$html2pdf->Output($output_file, 'F');
echo G_PAGE . "sections/photobooths/functions/panicInstructions/panik$code.pdf";

foreach ($qr as $q) {
    $filename = $PNG_TEMP_DIR . $q['image'] . "$photoBooth.png";
    unlink($filename);
}
?> 
