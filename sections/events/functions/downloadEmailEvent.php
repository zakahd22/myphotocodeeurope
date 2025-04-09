<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';

$ID = $_POST['id'];
if (!file_exists("../../../temp/emails/e$ID")){
    mkdir("../../../temp/emails/e$ID", 0777);
}

$f = "../../../temp/emails/e$ID/emails.xls";
$link = G_PAGE . "temp/emails/e$ID/emails.xls";
 
$query= "SELECT e.email FROM registre_emails e WHERE e.event_id=$ID GROUP BY e.email";
$CLD_CON->OpenRs($query);
$fp = fopen($f, "w");
$text="";
while($CLD_CON->FetchArray()){
    $email = $CLD_CON->GetArrayField('email');
    $text .= $email . "\n";
}

fwrite($fp, $text . PHP_EOL);
fclose($fp);

echo $link;

?>
