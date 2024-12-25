<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;

//resposta:
//"lista de imágenes con la siguiente información:
//- ID de foto
//- URL a imagen en formato thumbnail (300x120)
//- username
//- título
//- localización
//- fecha"


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//SELECT `id`, `rental_id`, `start_date`, `title`, `background_id`, `private`, `autocreated`, `ftp_folder_id`, `available` FROM `events` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idBooth`, `location`, `latitude`, `longitude` FROM `App_booths` WHERE 1

$sql = "SELECT Appusr_userPhoto.idUser,`Appusr_userPhoto`.`idPhoto`, photos.code, `username`, `title`,`location`, `latitude`, `longitude`, `Appusr_datetime`, `votes` ";
$sql.= " FROM ((`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
$sql.= " INNER JOIN App_booths ON Appusr_userPhoto.idBooth = App_booths.idBooth )";
$sql.= " INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id";
$sql.= " WHERE wall=1 ORDER BY `Appusr_datetime` DESC;";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
return;
}
//'REMOTE_HOST'
$url =  APP_curPageURL();
$xml = $APP_xmlOKcomm;
$xml.= "<photos>";
while($APP_BdD->FetchRs()){
    $xml.= "<photo>";
    $tmp =  $APP_BdD->GetField(1);
    $xml.= "<user_id>$tmp</user_id>";
    $tmp =  $APP_BdD->GetField(2);
    $xml.= "<photo_id>$tmp</photo_id>";
    $code =  $APP_BdD->GetField(3);
    $xml.= "<photo_url>{$url}photowall/$code.jpg</photo_url>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(4));
    $xml.= "<username>$tmp</username>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(5));
    $xml.= "<title>$tmp</title>";
    $tmp =  APP_preparaXML($APP_BdD->GetField(6));
    $xml.= "<location>$tmp</location>";
    $tmp =  $APP_BdD->GetField(7); $tmp/=1000000;
    $xml.= "<latitude>$tmp</latitude>";
    $tmp =  $APP_BdD->GetField(8); $tmp/=1000000;
    $xml.= "<longitude>$tmp</longitude>";
    $datetime = $APP_BdD->GetFieldDateTime(9);
    $xml.= "<date>";
    if($datetime){
     $xml.= $datetime->format("m-d-Y H:i");
    }
    $xml.= "</date>";
    $tmp =  $APP_BdD->GetField(10);
    $xml.= "<nvotes>$tmp</nvotes>";
       if(file_exists("./photo3D/".$code."-T1.jpg") && file_exists("./photo3D/".$code."-T13.jpg")){
        $xml .= "<dim>yes</dim>";
    }else{
        $xml .= "<dim>no</dim>";
    }
    $xml.= "</photo>";
}
$APP_BdD->CloseRs();
$xml.= "</photos>";


echo "$APP_xml$xml</return>"; // no cal res més



?>
