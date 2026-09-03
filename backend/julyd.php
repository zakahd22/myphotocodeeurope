<?php
if(file_exists('log4july/FramesSummerDownload.txt')){
$file = fopen('log4july/FramesSummerDownload.txt', "r");
$d = nl2br(fgets($file));
$d= intval($d);
fclose($file);
$d = $d+1;
}else{
    $d = 1;
}

$file = fopen('log4july/FramesSummerDownload.txt', "w");
    fwrite($file, $d ."");
fclose($file);

echo $d;

?>
