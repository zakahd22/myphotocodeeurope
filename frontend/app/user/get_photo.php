<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;


if(isset($_REQUEST['code'])){ $code = $_REQUEST['code'];}
else{
echo "$APP_xml<comm_status>Error - No code param</comm_status></return>";
return;
}

//	$photoDir = "events/".$event['start_date'].$event['id'];
//	$photoImg = $code.".jpg";
//
//	$photoInfo = GetImageSize($photoDir."/".$photoImg);


$xml = $APP_xmlOKcomm;
$sql = "SELECT photos.id, Appusr_datetime,booth_id,events.start_date,events.id FROM photos LEFT JOIN events ON photos.event_id = events.id WHERE code='$code';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}



if($APP_BdD->FetchRs()){
   $idPhoto =  $APP_BdD->GetField(1);
   $datetime = $APP_BdD->GetFieldDateTime(2);
//   if($datetime){
//     $xml.= $dateReport->format("m-d-Y H:i ");
//    }
   $idDongle =  $APP_BdD->GetField(3);
   $photoDir = "../../events/".$APP_BdD->GetField(4);
   $photoDir.=  $APP_BdD->GetField(5);
}
else{
    $APP_BdD->CloseRs();
    echo "$APP_xml<comm_status>Error - Code not found </comm_status></return>";
    return;
}

$APP_BdD->CloseRs();



//volem:
//"- ID de la foto
//- URL de la foto en formato mobile
//- fecha
//- título
//- localización
//- puntos
//- publicado en el muro (si/no)
//- e-mail del usuario (el definido al registrarse)"

//SELECT `idBooth`, `location`, `latitude`, `longitude` FROM `App_booths` WHERE 1
//comprovem si ja està a Appusr_userPhoto
$sql = "SELECT Appusr_userPhoto.idBooth, `idUser`, `title`, `wall`, `votes`,`location`, `latitude`, `longitude` FROM `Appusr_userPhoto` ";
$sql.= " INNER JOIN App_booths ON Appusr_userPhoto.idBooth = App_booths.idBooth";
$sql.= " WHERE idPhoto=$idPhoto;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
    return;
}
$title = ""; $wall = 0; $votes= 0;
$location= ""; $latitude= ""; $longitude= "";
$jahihaphoto = false;
$jahihauserphoto = false;

//        echo "$APP_xml<comm_status>TRACE05 $idBooth - $tmp - $APP_userId - </comm_status></return>";
//        return;


while($APP_BdD->FetchRs()){
    $jahihaphoto = true;
    $idBooth =  $APP_BdD->GetField(1);
    $tmp =  $APP_BdD->GetField(2);



    if($tmp == $APP_userId){
        $jahihauserphoto = true;
        $title =  APP_preparaXML($APP_BdD->GetField(3));
        $wall =  $APP_BdD->GetField(4);
        $votes =  $APP_BdD->GetField(5);
        $location =  APP_preparaXML($APP_BdD->GetField(6));
        $latitude =  $APP_BdD->GetField(7); $latitude/=1000000;
        $longitude =  $APP_BdD->GetField(8); $longitude/=1000000;
        break;
    }
}
$APP_BdD->CloseRs();


$url =  APP_curPageURL();//20121023


if(!$jahihaphoto){//hem de saber el idBooth
//SELECT `idBooth`, `idDongle`, `datetimeS`, `datetimeF` FROM `App_boothDongle` WHERE 1
    $sql = "SELECT App_boothDongle.idBooth,`location`, `latitude`, `longitude` FROM App_boothDongle ";
    $sql.= " INNER JOIN App_booths ON App_boothDongle.idBooth = App_booths.idBooth";
    $sql.= " WHERE idDongle=$idDongle ORDER BY datetimeS DESC LIMIT 0 , 1;";
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
        //caldria controlar l'error
        echo "$APP_xml<comm_status>Error Database error code: 0003 </comm_status></return>";
        return;
    }
    if($APP_BdD->FetchRs()){
       $idBooth =  $APP_BdD->GetField(1);
        $location =  APP_preparaXML($APP_BdD->GetField(2));
        $latitude =  $APP_BdD->GetField(3);
        $longitude =  $APP_BdD->GetField(4);
    }
    $APP_BdD->CloseRs();
//        echo "$APP_xml<comm_status>TRACE03 $idBooth - </comm_status></return>";
//        return;

//20121023 ha d'estar abans    $url =  APP_curPageURL();

    ini_set("memory_limit","96M");
    
    //a més hem de crear la foto mobile i la wall
    $nomFoto = "$photoDir/$code.jpg";


if(!$datetime){
//    $datetime = DateTime(filemtime($nomFoto));
    //$datetime = new DateTime();
//echo $date->format('U = Y-m-d H:i:s') . "\n";
    
    $datetime = new DateTime(date ("Y-m-d H:i", filemtime($nomFoto)));

//    $datetime->setTimestamp(filemtime($nomFoto));
}

    
//    $xml.= "<trace>nomFoto $nomFoto</trace>";
    
//    $existeix = file_exists ($nomFoto);

//        echo "$APP_xml<comm_status>TRACE02 $nomFoto - $existeix</comm_status></return>";
//        return;

    
    $img = imagecreatefromjpeg($nomFoto);
    
    if(!$img){
//        $xml.= "<trace>error opening $nomFoto</trace>";
        echo "$APP_xml<comm_status>Error - Can't open $nomFoto</comm_status></return>";
        return;
     }
//    else
//        $xml.= "<trace>open $nomFoto</trace>";
    
    $tipusPhoto = 0;
    //segons les mides tindrem el tipus de foto 1:tira vert   2: tira hor   2:4x6 hor  3:4x6 vert
    $wImg = imagesx( $img );
    $hImg = imagesy( $img ); 
    
    $isMobile = false;
    $factorMobile = 912 / 2152;
    
    ////deixem un marge en les comparacions
    if(abs($wImg - 708) < 50){//
        $tipusPhoto = 1;
    }
    else if(abs($hImg - 708) < 50){//
        $tipusPhoto = 2;
    }
    else if(abs($hImg - 1416) < 50){//
        $tipusPhoto = 3;
    }
    else if(abs($wImg - 1416) < 50){//
        $tipusPhoto = 4;
    }
        
//ens preparem per si l'upload ha estat de la foto ja reduida INICI        
    else if(abs($wImg - 300) < 50){//
        $tipusPhoto = 1;
        $isMobile = true;
    }
    else if(abs($hImg - 300) < 50){//
        $tipusPhoto = 2;
        $isMobile = true;
    }
    else if(abs($hImg - 912) < 50){//
        $tipusPhoto = 3;
        $isMobile = true;
    }
    else if(abs($wImg - 912) < 50){//
        $tipusPhoto = 4;
        $isMobile = true;
    }
        
//ens preparem per si l'upload ha estat de la foto ja reduida FINAL        
        

//bool imagecopyresized ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
//bool imagecopyresampled ( resource $dst_image , resource $src_image , int $dst_x , int $dst_y , int $src_x , int $src_y , int $dst_w , int $dst_h , int $src_w , int $src_h )
 
    //foto mobile (només escalat)
    switch($tipusPhoto){
        case 1:
            $w = 300; $h=912;
            break;
        case 2:
            $w = 912; $h=300;
            break;
        case 3:
            $w = 912; $h=600;
            break;
        case 4:
            $w = 600; $h=912;
            break;
        default://calculem per a que entri dins un quadrat de 912
            if ($wImg > $hImg) {
                $w = 912;
                $divisor = $wImg / 912;
                $h = floor( $hImg / $divisor);
            }
            else {
                $h =  912;
                $divisor = $hImg / 912;
                $w = floor( $wImg / $divisor );
            }
            break;
    }
    $tmpimg = imagecreatetruecolor( $w, $h );

    // Copy and resize old image into new image.
    imagecopyresampled( $tmpimg, $img, 0, 0, 0, 0, $w, $h, $wImg, $hImg );
    // Save thumbnail into a file.

//    $xml.= "<trace>thumbnail photomobile/$code.jpg</trace>";



    imagejpeg( $tmpimg, "photomobile/$code.jpg");//, $quality); default is 75
    // release the memory
    imagedestroy($tmpimg);

    //foto wall (pendent de coordenades
    $wWall = 300; $hWall=120;
    switch($tipusPhoto){
        default://com la 1
        case 1:
            $x = 0; $y=125; $w = 708; $h=283;
            if($isMobile){
                $y = floor( $y * $factorMobile );
                $w = floor( $w * $factorMobile );
                $h = floor( $h * $factorMobile );
            }
            break;
        case 2:
            $x = 468; $y=224; $w = 544; $h=218;
            if($isMobile){
                $x = floor( $x * $factorMobile );
                $y = floor( $y * $factorMobile );
                $w = floor( $w * $factorMobile );
                $h = floor( $h * $factorMobile );
            }
            break;
        case 3:
            $x = 315; $y=242; $w = 1676; $h=670;
            if($isMobile){
                $x = floor( $x * $factorMobile );
                $y = floor( $y * $factorMobile );
                $w = floor( $w * $factorMobile );
                $h = floor( $h * $factorMobile );
            }
            break;
        case 4:
            $x = 0; $y=396; $w = 1416; $h=566;
            if($isMobile){
                $x = floor( $x * $factorMobile );
                $y = floor( $y * $factorMobile );
                $w = floor( $w * $factorMobile );
                $h = floor( $h * $factorMobile );
            }
            break;
        
    }


    $tmpimg = imagecreatetruecolor( $wWall, $hWall );

    // Copy and resize old image into new image.
    imagecopyresampled( $tmpimg, $img, 0, 0, $x, $y, $wWall, $hWall, $w, $h );
    // Save thumbnail into a file.
//    $xml.= "<trace>thumbnail photowall/$code.jpg</trace>";
    imagejpeg( $tmpimg, "photowall/$code.jpg");//, $quality); default is 75
    // release the memory
    imagedestroy($tmpimg);

    imagedestroy($img);


}

if(!$jahihauserphoto){//cal insertar
//$sql = "SELECT `idBooth`, `idUser`, `title`, `wall`, `votes` FROM `Appusr_userPhoto` WHERE idPhoto=$idPhoto;";

    $sql = "INSERT INTO Appusr_userPhoto SET idPhoto=$idPhoto, idUser=$APP_userId, idBooth=$idBooth; ";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK){
        echo "$APP_xml<comm_status>Error - Database error code: 0004 </comm_status></return>";
        return;
    }
    
}



if(file_exists("./photo3D/" . $code . "-T1.jpg")){
    $d3 = "yes";
}else{
if(file_exists($photoDir . "/" . $code . "-3D/"  . $code . "-T13.jpg")){
    $cont = 1;
        while($cont<14){
     
            $imageFile= $photoDir . "/" . $code . "-3D/" . $code . "-T$cont.jpg";
            $imageObject = imagecreatefromjpeg($imageFile);
            $imageObject2 = imagescale($imageObject,300, 912, IMG_BICUBIC_FIXED);
            imagejpeg($imageObject2, "./photo3D/" .  $code . "-T$cont.jpg");
            $cont++;
        }
         $d3 = "yes";
}else{
     $d3 = "no";
}
}

//ara xml
//"- ID de la foto
//- URL de la foto en formato mobile
//- fecha
//- título
//- localización
//- puntos
//- publicado en el muro (si/no)
//- e-mail del usuario (el definido al registrarse)"

$xml.= "<photo>";
$xml.= "<id>$idPhoto</id>";

$xml.= "<photomobile>{$url}photomobile/$code.jpg</photomobile>";
$xml.= "<photo_url>{$url}photowall/$code.jpg</photo_url>";
if($datetime){
    $xml.= "<date>".$datetime->format("m-d-Y H:i ")."</date>";
}
else{
    $xml.= "<date></date>";
    
}
$xml.= "<title>$title</title>";
$xml.= "<location>$location</location>";
$xml.= "<latitude>$latitude</latitude>";
$xml.= "<longitude>$longitude</longitude>";
$xml.= "<nvotes>$votes</nvotes>";
//$xml.= "<trace>$tipusPhoto ($wImg, $hImg) $isMobile $factorMobile ($x, $y, $w, $h)</trace>";
$xml.= "<wall>$wall</wall>";
$xml.= "<email>$APP_userEmail</email>";
$xml.= "<dim>$d3</dim>";
$xml.= "</photo>";

echo "$APP_xml$xml</return>"; // no cal res més



?>
