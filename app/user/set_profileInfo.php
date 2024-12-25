<?php
require("common.php");


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//"- username
//- password
//- userimage
//- e-mail
//- publicación automática en facebook (si/no)
//- publicación automática en el muro (si/no)
//- envío automático a e-mail del perfil (si/no)"

//es tarà  a $_FILES    if(isset($_REQUEST['userimage'])){ $userimage = ,


if(isset($_REQUEST['e-mail'])) $email = " email='". str_replace("'","",$_REQUEST['e-mail']). "'"; else $email = " email=NULL";
if(isset($_REQUEST['autofcbk'])){
    if($_REQUEST['autofcbk'] == "si") $autofcbk  = " autofcbk=1"; 
    else $autofcbk  = " autofcbk=0";
}
else $autofcbk  = " autofcbk=0";

if(isset($_REQUEST['autowall'])){
    if($_REQUEST['autowall'] == "si") $autowall  = " autowall=1";  
    else $autowall  = " autowall=0";
}
else $autowall  = " autowall=0";

if(isset($_REQUEST['autoemail'])){
    if($_REQUEST['autoemail'] == "si") $autoemail = " autoemail=1";
    else $autoemail = " autoemail=0";
}
else $autoemail = " autoemail=0";


//$sql = "SELECT `idPhoto`,`start_date`,code FROM (`Appusr_userPhoto` INNER JOIN `photos` ON `Appusr_userPhoto`.idPhoto = photos.id) ";
//$sql.= " INNER JOIN events ON photos.event_id = events.id WHERE idUser='$APP_userId' AND downloaded = 0;";
$sql = "UPDATE `Appusr_user` SET $email, $autofcbk, $autowall, $autoemail ";
$sql.= " WHERE id=$APP_userId;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}

//foto
$hihafitxer = false;
$quinError = $_FILES["userimage"]["error"];
if ($quinError > 0)
{
  switch($quinError){
      case 4:
      //o millor ho acceptem  echo "$APP_xml<comm_status>Error userimage: No file has been sent</comm_status></return>";
       break;
      default:
        echo "$APP_xml<comm_status>Error userimage: error: $quinError</comm_status></return>";
        return;
       break;
   }
}
else{
    $ContentType = $_FILES['userimage']['type'];
    switch($ContentType){
    //       case "image/gif":
    //                    $extensio=".gif";
    //                    break;
    case "image/jpeg":
    case "image/pjpeg":
        break;
    default:
        echo "$APP_xml<comm_status>Error userimage: Invalid file type: $ContentType</comm_status></return>";
        return;
    }

//20131028    $nomFitxer = "userimage/img$APP_userId.jpg";
    $nomFitxer = "userimage/img$APP_userQR.jpg";//20131028
    
    if(!move_uploaded_file($_FILES["userimage"]["tmp_name"], $nomFitxer)){
        echo "$APP_xml<comm_status>Error userimage: can't move file: " . $_FILES["fileUpload"]["tmp_name"] . "to $nomFitxer</comm_status></return>";
        return;
    }
}


echo "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més



?>
