<?php
require("common.php");


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUserVoting`, `idUserVoted`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//params:
//"- username
//- password
//- ID de foto  (també cal user_id)
//- estado del voto
//user_id>6</user_id>
//<photo_id



if(isset($_REQUEST['photo_id'])){ $idPhoto = $_REQUEST['photo_id'];}
else{
echo "$APP_xml<comm_status>Error - No ID photo param</comm_status></return>";
return;
}
if(isset($_REQUEST['user_id'])){ $idUser = $_REQUEST['user_id'];}
else{
echo "$APP_xml<comm_status>Error - No ID user param</comm_status></return>";
return;
}

if($idUser == $APP_userId){
echo "$APP_xml<comm_status>Error - ID user param and the username are equal</comm_status></return>";
return;    
}




if(isset($_REQUEST['vote'])){ $vote = $_REQUEST['vote'];}
else{
echo "$APP_xml<comm_status>Error - No vote param</comm_status></return>";
return;
}

//llegim quants vots te la foto
$nVotes = 0;
$sql = "SELECT votes FROM Appusr_userPhoto WHERE idUser=$idUser AND idPhoto=$idPhoto; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}
if($APP_BdD->FetchRs()){
   $nVotes =  $APP_BdD->GetField(1);
}
else{//no existeix
    $APP_BdD->CloseRs();
    echo "$APP_xml<comm_status>Error - Photo from user not found</comm_status></return>";
    return;
    
}
$APP_BdD->CloseRs();

//mirem si ja ha votat (ha d'existir el registre a Appusr_userVotes)
$hasVoted = 0;
$sql = "SELECT idPhoto FROM Appusr_userVotes WHERE idUserVoting=$APP_userId AND idUserVoted=$idUser AND idPhoto=$idPhoto; ";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    //caldria controlar l'error
    echo "$APP_xml<comm_status>Error Database error code: 0001bis </comm_status></return>";
    return;
}
if($APP_BdD->FetchRs()){
   $hasVoted = 1;
}

$APP_BdD->CloseRs();



if($vote == "si"){//insert  //$photodate = date('Y-m-d H:i');
    if($hasVoted == 1){
        echo "$APP_xml$APP_xmlOKcomm</return>";//no ho tractem com a error
        return;
    }
    $nVotes+=1;
    $datetime = date('Y-m-d H:i'); //`idUserVoting`, `idUserVoted`
    $sql = "INSERT INTO Appusr_userVotes SET idUserVoting=$APP_userId, idUserVoted=$idUser, idPhoto=$idPhoto, datetime='$datetime' ; ";
    
}
else{
    if($hasVoted == 0){
        echo "$APP_xml$APP_xmlOKcomm</return>";//no ho tractem com a error
        return;
    }
    $sql = "DELETE FROM Appusr_userVotes WHERE idUserVoting=$APP_userId AND idUserVoted=$idUser AND idPhoto=$idPhoto ; ";
    $nVotes-=1; if($nVotes<=0) $nVotes = 0;
}
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
    return;
}

//actualitzem
$sql = "UPDATE Appusr_userPhoto SET votes=$nVotes WHERE idUser=$idUser AND idPhoto=$idPhoto;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0003 </comm_status></return>";
    return;
}


echo "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més


?>
