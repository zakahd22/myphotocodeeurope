<?php
include "../../conf.php";
include "../../conexio.php";

$r = $_REQUEST['comanda'];
$s = $_REQUEST['s'];

$ss = $r*13/10;
$ss = round($ss -0.5);
echo " ".$s . " | ";
echo $ss;
if($ss = $s){
    $shop = $_REQUEST['shop'];
    if($CLD_CON->Execute("UPDATE SHP_Comandes SET printed = 1 WHERE id=$r and shop = $shop")){
        echo "OK";
    }else{

        echo "UPDATE SHP_Comandes SET printed = 1 WHERE id=$r and shop = $shop";
    }
    
}else{
    echo "ERROR - Not Match";
}
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
