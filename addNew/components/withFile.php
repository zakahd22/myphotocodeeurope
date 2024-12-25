<?php
include '../../common/global.php';
require_once G_PATH . 'common/conexio.php';

$CLD_CON2 = clone($CLD_CON);
$typeId= $_POST['type'];
$FILETXT = $_FILES['textFile']['tmp_name'];
$data = date("Y-m-d");
$x=0;
$addeds ="";
$errors ="";
$fh = fopen($FILETXT,'r');
while ($line = fgets($fh)) {

    $line = trim(preg_replace('/\s\s+/', '', $line));
 $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE serialnumber='$line'");
    if($CLD_CON->GetRsRows()<1){
        /*$CLD_CON2->Execute("INSERT INTO CLD_components (serialnumber , type, data_entrada) VALUES('$line',$typeId,'$data')");*/
        $addeds .= "<span style='margin-left:10px;'>  - $line</span><br>";
    }else{
       $errors .=  "<span style='margin-left:10px;'> - $line</span><br>";
    }
}
fclose($fh);
move_uploaded_file($FILETXT , "../../tmp/componentsFile.txt");
echo "<div style='width:49%;display:inline;float:left;border-right:1px solid gray;'>";
echo "<h2>OK serialnumbers</h2><hr>";

echo $addeds;
echo "</div>";
echo "<div style='width:49%;display:inline;float:left;'>";
echo "<h2>Existing serialnumbers :</h2><hr>";
echo $errors;
echo "</div>";

?>
