<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_dongleOK) return;



if(isset($_REQUEST['f'])){ $code = $_REQUEST['f'];}
else{
echo "Error - No f param";
return;
}

if(!isset($_REQUEST['code'])){
    echo "Error2 - code 01";
    return;
}

$codi = $_REQUEST['code'];
$l = strlen($codi);
if($l <= 12){
    echo "Error2 - code 02";
    return;
}
$l-=12;
$APP_userId = substr($codi, 0,$l);



//echo "TRACE APP_userId: $APP_userId#";



$sql = "SELECT photos.id, Appusr_datetime,booth_id,events.start_date,events.id FROM photos LEFT JOIN events ON photos.event_id = events.id WHERE code='$code';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "Error Database error code: 0001 $sql";
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
    echo "Error - Code not found $sql</comm_status></return>";
    return;
}

$APP_BdD->CloseRs();


//no comprovem si existeix ja que be d'un PB amb login
//$APP_idBooth

    ini_set("memory_limit","96M");

    //a més hem de crear la foto mobile i la wall
    $nomFoto = "$photoDir/$code.jpg";


//echo "TRACE nomFoto: $nomFoto#";

    if(!$datetime){
        $datetime = new DateTime(date ("Y-m-d H:i", filemtime($nomFoto)));
    }


    
    $img = imagecreatefromjpeg($nomFoto);
    
    if(!$img){
//        $xml.= "<trace>error opening $nomFoto</trace>";
        echo "Error - Can't open $nomFoto</comm_status></return>";
        return;
     }
//    else
//        $xml.= "<trace>open $nomFoto</trace>";
    
    $tipusPhoto = 0;
    //segons les mides tindrem el tipus de foto 1:tira vert   2: tira hor   2:4x6 hor  3:4x6 vert
    $wImg = imagesx( $img );
    $hImg = imagesy( $img ); 
    
    
    
//echo "TRACE wImg: $wImg; hImg: $hImg#";
    
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

    
    
//echo "TRACE w: $w; h: $h#";
    
    
    
    // Copy and resize old image into new image.
    imagecopyresampled( $tmpimg, $img, 0, 0, 0, 0, $w, $h, $wImg, $hImg );
    // Save thumbnail into a file.

//    $xml.= "<trace>thumbnail photomobile/$code.jpg</trace>";



    imagejpeg( $tmpimg, "../user/photomobile/$code.jpg");//, $quality); default is 75
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
    imagejpeg( $tmpimg, "../user/photowall/$code.jpg");//, $quality); default is 75
    // release the memory
    imagedestroy($tmpimg);

    imagedestroy($img);

//20130531shared    $sql = "INSERT INTO Appusr_userPhoto SET idPhoto=$idPhoto, idUser=$APP_userId, idBooth=$APP_idBooth ";
    $sql = "INSERT INTO Appusr_userPhoto SET idPhoto=$idPhoto, idUser=$APP_userId, idBooth=$APP_idBooth, automaticallyShared = 1 ";//20130531shared
    $sql.= " ON DUPLICATE KEY UPDATE idBooth=$APP_idBooth;";
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK){
        echo "Error - Database error code: 0004 $sql</comm_status></return>";
        return;
    }
//20130531shared INICI
//20130531shared echo "OK";
    //Cal enviar una notificació
    
    
echo $APP_okResp;//20170220apns
//20170220apns
//    include("../easyapns/src/php/APP_apns.php");
//    APNS_MyAutomaticSharesUser($APP_userId,$code);
//    
//    $APP_okResp = "OK";
//    ignore_user_abort(true);
//    header("Connection: close");
//    header("Content-Length: " . mb_strlen($APP_okResp));
//    echo $APP_okResp;
//    flush();    
//    APNS_sendMessages();

//20130531shared FINAL


?>
