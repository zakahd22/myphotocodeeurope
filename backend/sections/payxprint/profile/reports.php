<?php
include "../../../sessio.php";
require_once G_PATH . 'sections/payxprint/functions/reportManager.php';

$reportManager = new reportManager($USERID);
$reportManager->indexAction();
