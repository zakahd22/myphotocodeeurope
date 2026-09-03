<?php
require("common.php");

if(!$APP_user) return;


//prova

$codi = APP_QRdo($APP_userId);


//$codi = rndm32(12);


//echo $codi;
//return;

$sql = "UPDATE `Appusr_user` SET qrcode='$codi' WHERE id=$APP_userId;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0001 $sql</comm_status></return>";
    return;
}


////$nomFitxer = "userqr/qr$APP_userId.png";
////
////require("../common/phpqrcode.php");
//////	define('QR_ECLEVEL_L', 0);
//////	define('QR_ECLEVEL_M', 1);
//////	define('QR_ECLEVEL_Q', 2);
//////	define('QR_ECLEVEL_H', 3);
////
////$level = QR_ECLEVEL_H;
//// $margin = 2;
//////QRcode::png("QRphoto-planet:$codi", $nomFitxer, $level, 7, $margin);
////QRcode::png("QR-id:$codi", $nomFitxer, $level, 7, $margin);





//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename1.png', $level, 1, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename2.png', $level, 2, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename3.png', $level, 3, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename4.png', $level, 4, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename5.png', $level, 5, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename6.png', $level, 6, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename7.png', $level, 7, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename8.png', $level, 8, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename9.png', $level, 9, $margin);
//QRcode::png('https://www.myphotocode.com/photo/AE5FNGELHN', 'filename10.png', $level, 10, $margin);





//echo "$APP_xml$APP_xmlOKcomm<code>$codi</code></return>";
echo "$APP_xml$APP_xmlOKcomm</return>";
?>
