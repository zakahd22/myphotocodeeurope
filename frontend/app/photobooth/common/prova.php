<?php
error_reporting(E_ALL);
$dir  = dirname(__FILE__);
$d = $dir . "/../../easyapns/src/php/CLD_alertsEmails.php";
include "$d";

CLD_sendEmail( 73, 5,"Run Out of Film at Howarts");
echo "Errors";
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
