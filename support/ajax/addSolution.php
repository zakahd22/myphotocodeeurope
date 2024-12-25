<?php
include "../sessio.php";
include '../conexio.php';
$solution = addslashes($_POST['solution']);
$CLD_CON->ExecuteInsert("INSERT INTO SAT_solutions (solution , nextQuestion) VALUES('$solution',999999)");
?>
