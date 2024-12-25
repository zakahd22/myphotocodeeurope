<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php'; 
require_once G_PATH . "common/Classes/baseController.php";
include '../functions/functions.php';


echo '<link rel="stylesheet" type="text/css" href="sections/manuals/resources/manuals.css">';
echo '<script type="text/javascript" src="sections/manuals/functions/functions.js"></script>';

echo showManuals($USERID, "Expression");
