<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController;
$baseController->createModel('photos');

$photo = $_POST['photo'];
$nflag = $_POST['nflag'];

$array = array('flag' => $nflag);
$baseController->photosModel->updatePhoto($photo, $array);

