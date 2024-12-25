<?php
$_lletres = ["A", "C", "D", "B"];
$_x = 1;

/*
while ($_x < 14) {
    $_baseimagen = imagecreatetruecolor(356, 1076);
    $_bgcolor = imagecolorallocate($_baseimagen, 255, 255, 255);
    imagefill($_baseimagen, 0, 0, $_bgcolor);
    $_top_p = 25;
    foreach ($_lletres as $_lletra) {
        $_img = imagecreatefromjpeg($folder3D . $code . "-" . $_lletra . $_x . ".jpg");
        $_img_s = imagescale($_img, 306, 237, IMG_BICUBIC_FIXED);
        imagecopy($_baseimagen, $_img_s, 25, $_top_p, 0, 0, 306, 237);
        ImageDestroy($_img);
        ImageDestroy($_img_s);
        $_top_p = $_top_p + 262;
    }
    //imagealphablending($_baseimagen,true); 
    imagegif($_baseimagen, $folder3D . $code . "-$_x.gif");
    ImageDestroy($_baseimagen);
    $_x++;
}
*/
$_x = 1;
while ($_x < 14) {
    $imageFile= $folder3D . $code . "-T$_x.jpg";
    $imageObject = imagecreatefromjpeg($imageFile);
    $imageObject2 = imagescale($imageObject,356,1076, IMG_BICUBIC_FIXED);
    imagegif($imageObject2, $folder3D . $code . "-T$_x.gif");
    $_x++;
}

$_x = 1;
while ($_x < 14) {
    $frames[] = $folder3D . $code . "-T$_x.gif";
    $time[] = 10;
    $_x++;
}
$_x = 13;
while ($_x > 0) {
    $frames[] = $folder3D . $code . "-T$_x.gif";
    $time[] = 10;
    $_x--;
}

$gif = new GIFEncoder($frames, $time, 0, 2, 0, 0, 0, "url");
FWrite(FOpen($folder2D . $code . "-T3D.gif", "wb"), $gif->GetAnimation());


$_x = 1;
while ($_x < 14) {
    unlink($folder3D . $code . "-T$_x.gif");
    $_x++;
}

?>
