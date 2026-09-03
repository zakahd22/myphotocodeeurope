<?php
include '../sessio.php';
include '../conexio.php';

$problemaID = $_POST['problemID'];
$solution = addslashes($_POST['solution']);
$CLD_CON->Execute("UPDATE SAT_problems SET solved = 1 , solution_text2='$solution' WHERE id=$problemaID");
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */
?>
