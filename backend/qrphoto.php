<?php
require_once "common/global.php";
//Aquí si arriba quan una persona llegeix el codi QR de la photo i el direcciona al index.php em el codi de la foto
$code = $_REQUEST['code'];
echo "<meta http-equiv=Refresh content='0; url=".G_PAGE."index.php?code=$code'>";
//echo "".G_PAGE."index.php?code=$code";
?>