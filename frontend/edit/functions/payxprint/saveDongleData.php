<?php
include '../../../common/global.php';
require_once G_PATH . 'common/conexio.php';

//America/New_York
//date_default_timezone_set("Europe/Madrid");
date_default_timezone_set("America/New_York");

$error = false;
$new_dongle = 0;

if(isset($_POST['id'])){
    $id = $_POST['id'];
}
if(isset($_POST['ms'])){
    $minStock = $_POST['ms'];
}
if(isset($_POST['qty'])){
    $quantity = $_POST['qty'];
}
if(isset($_POST['pr'])){
    $preu = $_POST['pr'];
}
if(isset($_POST['new'])){
    $new_dongle = $_POST['new'];
}

if($new_dongle == 1){
    $data = utils::get_datetime();
    $sql= "INSERT INTO Pay_print_dongle (idDongle , startDate , minStock , quantitat , preu , saldo) VALUES({$id} , '{$data}', {$minStock}, {$quantity}, {$preu}, {$quantity})";
    if($CLD_CON->Execute($sql) != 0){
        $sql= "INSERT INTO Pay_print_order (idDongle, idOwner, quantitat, preu, proposedDate, validatedDate, reportedDate, commissionDate)
               VALUES ({$id}, (SELECT booths.rental_id FROM booths WHERE booths.id = {$id}), {$quantity}, {$preu}, '{$data}', '{$data}', null, null)";
        if($CLD_CON->Execute($sql) == 0){
            utils::log("TRACE Dongle - ERROR {$CLD_CON->errno}: {$CLD_CON->error}", G_PATH . "logs/log");
            utils::log("TRACE Dongle - sql = {$sql}", G_PATH . "logs/log");
            $error = 1;
        }
    }
    else{
        utils::log("TRACE Dongle - ERROR {$CLD_CON->errno}: {$CLD_CON->error}", G_PATH . "logs/log");
        utils::log("TRACE Dongle - sql = {$sql}", G_PATH . "logs/log");
        $error = 2;
    }
}
else{
    $sql = "UPDATE Pay_print_dongle SET minStock = {$minStock} WHERE idDongle = $id";
    if($CLD_CON->Execute($sql) == 0){
        utils::log("TRACE Dongle - ERROR {$CLD_CON->errno}: {$CLD_CON->error}", G_PATH . "logs/log");
        utils::log("TRACE Dongle - sql = {$sql}", G_PATH . "logs/log");
        $error = 3;        
    }

    $sql = "UPDATE Pay_print_dongle SET quantitat = {$quantity} WHERE idDongle = $id";
    if($CLD_CON->Execute($sql) == 0){
        utils::log("TRACE Dongle - ERROR {$CLD_CON->errno}: {$CLD_CON->error}", G_PATH . "logs/log");
        utils::log("TRACE Dongle - sql = {$sql}", G_PATH . "logs/log");
        $error = 4;        
    }

    $sql = "UPDATE Pay_print_dongle SET preu = {$preu} WHERE idDongle = $id";
    if($CLD_CON->Execute($sql) == 0){
        utils::log("TRACE Dongle - ERROR {$CLD_CON->errno}: {$CLD_CON->error}", G_PATH . "logs/log");
        utils::log("TRACE Dongle - sql = {$sql}", G_PATH . "logs/log");
        $error = 5;        
    }
}

if(!$error){
    echo "OK";
}
else{
    echo "ERROR $error";
}