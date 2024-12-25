<?php
$idPhoto = "PO3ICK48NF";
$photoDir = "../../events/2014110610086";


if(file_exists("./photo3D/" . $idPhoto . "-T1.jpg")){
    $d3 = "yes";
}else{
echo  $photoDir . "/" . $idPhoto . "-3D/"  . $idPhoto . "-T13.jpg" ."<br>";
if(file_exists($photoDir . "/" . $idPhoto . "-3D/"  . $idPhoto . "-T13.jpg")){
    $cont = 1;
        while($cont<14){
     
            $imageFile= $photoDir . "/" . $idPhoto . "-3D/" . $idPhoto . "-T$cont.jpg";
            $imageObject = imagecreatefromjpeg($imageFile);
            $imageObject2 = imagescale($imageObject,300, 912, IMG_BICUBIC_FIXED);
            imagejpeg($imageObject2, "./photo3D/" .  $idPhoto . "-T$cont.jpg");
            $cont++;
        }
         $d3 = "yes";
}else{
     $d3 = "no";
}
}
echo $d3 . "<br>";
print_r(error_get_last());

?>
