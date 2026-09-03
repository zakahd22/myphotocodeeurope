<?php
$APP_open = true;//20121023
require("common.php");


if(!$APP_user) return;
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1


//201301 ho deixem en les 10 fotos més votades
$sql = "SELECT Appusr_userPhoto.idUser,`Appusr_userPhoto`.`idPhoto`, photos.code, `username`, `title`,`location`, `latitude`, `longitude`, `Appusr_datetime`, `votes` ";
$sql.= " FROM ((`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
$sql.= " INNER JOIN App_booths ON Appusr_userPhoto.idBooth = App_booths.idBooth )";
$sql.= " INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id";

//20130620 $sql.= " WHERE wall=1 ORDER BY votes,`Appusr_datetime` DESC LIMIT 0,10;";
$sql.= " WHERE wall=1 AND votes>0 ORDER BY votes DESC,`Appusr_datetime` DESC LIMIT 0,10;";//20130620 


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
while($APP_BdD->FetchArray()){
    $xml.= "<photo>";
    $tmp =  $APP_BdD->GetArrayField("idUser");
    $xml.= "<user_id>$tmp</user_id>";
    $tmp =  $APP_BdD->GetArrayField("idPhoto");
    $xml.= "<photo_id>$tmp</photo_id>";
    $code =  $APP_BdD->GetArrayField("code");
    $xml.= "<photo_url>{$url}photowall/$code.jpg</photo_url>";
    $tmp =  APP_preparaXML($APP_BdD->GetArrayField("username"));
    $xml.= "<username>$tmp</username>";
    $tmp =  APP_preparaXML($APP_BdD->GetArrayField("title"));
    $xml.= "<title>$tmp</title>";
    $tmp =  APP_preparaXML($APP_BdD->GetArrayField("location"));
    $xml.= "<location>$tmp</location>";
    $tmp =  $APP_BdD->GetArrayField("latitude"); $tmp/=1000000;
    $xml.= "<latitude>$tmp</latitude>";
    $tmp =  $APP_BdD->GetArrayField("longitude"); $tmp/=1000000;
    $xml.= "<longitude>$tmp</longitude>";
    $datetime = $APP_BdD->GetArrayFieldDateTime("Appusr_datetime");
    $xml.= "<date>";
    if($datetime){
     $xml.= $datetime->format("m-d-Y H:i");
    }
    $xml.= "</date>";
    $tmp =  $APP_BdD->GetArrayField("votes");
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


//201301 codi antic
////
////top usuaris (amb més vots)
//$sql = "SELECT idUser,Appusr_user.username,SUM(votes) FROM Appusr_userPhoto INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id GROUP BY idUser,Appusr_user.username HAVING wall=1  LIMIT 10; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    //caldria controlar l'error
//    echo "$APP_xml<comm_status>Error Database error code: 0002 $sql</comm_status></return>";
//    return;
//}
//$xml.= "<votes>";
//while($APP_BdD->FetchRs()){
//    $xml.= "<votes_user>";
//    $tmp =  $APP_BdD->GetField(1);
//    
//    
////20121027    $xml.= "<userimage>{$url}userimage/img$APP_userId.jpg</userimage>";
//   $fitxerImatge = "userimage/img$APP_userId.jpg";//20121027
//    if(!file_exists($fitxerImatge)) $fitxerImatge = "userimage/avatar.png";//20121027
//    $xml.= "<userimage>{$url}$fitxerImatge</userimage>";//20121027
//AQUI    
//    
//    
//    
//    
//    $xml.= "<votes_userimage>{$url}userimage/img$tmp.jpg</votes_userimage>";
//    $tmp =  APP_preparaXML($APP_BdD->GetField(2));
//    $xml.= "<votes_username>$tmp</votes_username>";
//    $tmp =  $APP_BdD->GetField(3);
//    $xml.= "<votes_n>$tmp</votes_n>";
//    $xml.= "</votes_user>";
//}
//$APP_BdD->CloseRs();
//$xml.= "</votes>";
//
//
//
////top usuaris (amb més booths diferents)
//
//
//AQUI AQUI AQUI AQUI
//
//
//$sql = "SELECT idUser,Appusr_user.username,idBooth,COUNT(*) FROM Appusr_userPhoto INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id GROUP BY idUser,Appusr_user.username,idBooth HAVING wall=1  LIMIT 10; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    //caldria controlar l'error
//    echo "$APP_xml<comm_status>Error Database error code: 0002 $sql</comm_status></return>";
//    return;
//}
//$xml.= "<booths>";
//while($APP_BdD->FetchRs()){
//    $xml.= "<booths_user>";
//    $tmp =  $APP_BdD->GetField(1);
//    $xml.= "<booths_userimage>{$url}userimage/img$tmp.jpg</booths_userimage>";
//    $tmp =  APP_preparaXML($APP_BdD->GetField(2));
//    $xml.= "<booths_username>$tmp</booths_username>";
//    $tmp =  $APP_BdD->GetField(3);
//    $xml.= "<booths_n>$tmp</booths_n>";
//    $xml.= "</booths_user>";
//}
//$APP_BdD->CloseRs();
//$xml.= "</booths>";
//
//
////usuaris amb més fotos (scanking)
////
//$sql = "SELECT idUser,Appusr_user.username,COUNT(idUser) FROM Appusr_userPhoto INNER JOIN Appusr_user ON Appusr_userPhoto.idUser = Appusr_user.id GROUP BY idUser,Appusr_user.username HAVING wall=1  LIMIT 10; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    //caldria controlar l'error
//    echo "$APP_xml<comm_status>Error Database error code: 0001 $sql</comm_status></return>";
//    return;
//}
//$xml.= "<photos>";
//while($APP_BdD->FetchRs()){
//    $xml.= "<photos_user>";
//    $tmp =  $APP_BdD->GetField(1);
//    $xml.= "<photos_userimage>{$url}userimage/img$tmp.jpg</photos_userimage>";
//    $tmp =  APP_preparaXML($APP_BdD->GetField(2));
//    $xml.= "<photos_username>$tmp</photos_username>";
//    $tmp =  $APP_BdD->GetField(3);
//    $xml.= "<photos_n>$tmp</photos_n>";
//    $xml.= "</photos_user>";
//}
//$APP_BdD->CloseRs();
//$xml.= "</photos>";
//
//
//
//
//
//echo "$APP_xml$xml</return>"; // no cal res més



?>
