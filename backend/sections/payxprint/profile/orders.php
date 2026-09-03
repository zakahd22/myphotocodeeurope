<?php
include "../../../sessio.php";
require_once G_PATH . 'sections/payxprint/functions/orderManager.php';

$orderManager = new orderManager($USERID);
$orderManager->indexAction();