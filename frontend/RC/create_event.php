<?php
include '../common/global.php';
require_once G_PATH . 'common/conexio.php';

//Sembla que no es crida

$d = $_GET['d'];
$title = date("l, F d, Y", strtotime($d));

if($idevent = $CLD_CON->ExecuteInsert("INSERT INTO events (rental_id , start_date , title , private , autocreated , available) VALUES(0 , '$data' , '$title' , 1 , 0 , 1) ")){
    if(mkdir($_SERVER['DOCUMENT_ROOT'] . "/events/$data$idevent")){
        echo $idevent;
    }
}
?>