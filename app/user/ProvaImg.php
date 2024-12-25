<?php

$im = imagecreatefrompng('filename10.png');
header('Content-Type: image/png');

imagepng($im);
imagedestroy($im);

?>
