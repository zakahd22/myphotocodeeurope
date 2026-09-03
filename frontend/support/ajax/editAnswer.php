<?php
include "../sessio.php";
include '../conexio.php';

$ID = $_POST['ID'];
$text= addslashes($_POST['txt']);
$CLD_CON->Execute("UPDATE SAT_answers SET answer='$text' WHERE id=$ID");
?>
