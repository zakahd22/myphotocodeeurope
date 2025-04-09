<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$baseController = new baseController;
$baseController->createModel('events');

$ID = $_POST['id'];
$title = $_POST['title'];

if(empty($title)){
    echo "- El titulo no puede estar vacio -";
}
else{
    $array = array('title' => $title, 'autocreated' => 0);
    $upd = $baseController->eventsModel->updateEvent($ID, $array); 
        
    if ($upd) {
        echo "OK";
    }
    else {
        echo "ERROR";
    }
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

