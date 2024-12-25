<?php
include '../sessio.php';
include '../conexio.php';
$e = $_SESSION['enquesta'];
$CLD_CON->Execute("DELETE FROM SAT_problems WHERE id=$e");
$CLD_CON->Execute("DELETE FROM SAT_problemsquestions WHERE problem_id=$e");
?>
