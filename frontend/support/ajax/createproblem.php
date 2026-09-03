<?php
include '../sessio.php';
include '../conexio.php';

if($userType == 5){
    $ownerID= 0;
}else{
$ownerID = $_POST['owner'];
}

list($boothID , $boothType) = explode("-" , $_POST['booth']);
//$info = addslashes($_POST['info']);
$dataTiempo = date("Y-m-d G:i:s");


//$_SESSION['enquesta'] = $CLD_CON->ExecuteInsert("INSERT INTO SAT_problems (propietari , solved , booth_id , boothType , dataTiempo , info) VALUES($ownerID , 0 , $boothID,'$boothType' , '$dataTiempo' , '$info')");
$_SESSION['enquesta'] = $CLD_CON->ExecuteInsert("INSERT INTO SAT_problems (propietari , solved , booth_id , boothType , dataTiempo) VALUES($ownerID , 0 , $boothID,'$boothType' , '$dataTiempo')");
$_SESSION['boothType'] = $boothType;
$_SESSION['boothID'] = $boothID;
?>
