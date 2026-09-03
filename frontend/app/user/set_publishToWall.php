<?php
require("common.php");

/*
//Mofifs
 20130609, quan wall == 0 -> possar votes = 0 i esborrar registres de Appusr_userVotes de l'idUserVoted i la foto
*/


if(!$APP_user) return;


//SELECT `id`, `username`, `password`, `email`, `autofcbk`, `autowall`, `autoemail`, `qrcode` FROM `Appusr_user` WHERE 1
//SELECT `idUser`, `idPhoto`, downloaded, `title`, `wall`, `votes`, `idBooth` FROM `Appusr_userPhoto` WHERE 1
//SELECT `idUser`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
//SELECT `id`, `code`, `event_id`, `booth_id`, `flag`, `Appusr_datetime` FROM `photos` WHERE 1
//params:
//"- username
//- password
//- ID de la foto
//- Title
//- Estado de publicación en el muro (si/no)"

if(isset($_REQUEST['id'])){ $idPhoto = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>Error - No ID photo param</comm_status></return>";
return;
}

if(isset($_REQUEST['title'])) $title = " title='". str_replace("'","",$_REQUEST['title']). "'"; else $title = " title=NULL";

if(isset($_REQUEST['wall'])){
    if($_REQUEST['wall'] == "si") $wall  = " wall=1";
    else
    {
//20130609 INICI
//20130609        $wall  = " wall=0";
        $wall  = " wall=0, `votes`=0 ";
        //SELECT `idUserVoting`, `idUserVoted`, `idPhoto`, `datetime` FROM `Appusr_userVotes` WHERE 1
        $sql = "DELETE FROM `Appusr_userVotes` WHERE idUserVoted=$APP_userId AND idPhoto=$idPhoto;";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            echo "$APP_xml<comm_status>Error Database error code: 0000 </comm_status></return>";
            return;
        }
//20130609 INICI
    }
}
else{
echo "$APP_xml<comm_status>Error - No wall param</comm_status></return>";
return;
}

$sql = "UPDATE `Appusr_userPhoto` SET $title, $wall ";
$sql.= " WHERE idUser=$APP_userId AND idPhoto=$idPhoto;";
$esOK = $APP_BdD->Execute($sql);
if(!$esOK){
    echo "$APP_xml<comm_status>Error Database error code: 0001 </comm_status></return>";
    return;
}

echo "$APP_xml$APP_xmlOKcomm</return>"; // no cal res més


?>
