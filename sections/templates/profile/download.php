<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";

$html = "";
$html .= '<div class="download">';

$html .= file_get_contents('download.html');

$html .= '</div>';

echo $html;

