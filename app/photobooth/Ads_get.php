<?php
require("common.php");

if(!$APP_dongleOK) return;

//SELECT `idCamp`, `type`, `startDate`, `endDate`, `startHour`, `startMinute`, `endHour`, `endMinute`, `duration`, `times`, `status` FROM `Ads_Campaigns`

$sql = "SELECT Ads_CampaignBooth.`idCamp`, `type`, `startDate`, `endDate`, `startHour`, `startMinute`, `endHour`, `endMinute`, `duration`, `times` FROM ";
$sql.= " Ads_CampaignBooth INNER JOIN Ads_Campaigns ON Ads_CampaignBooth.idCamp = Ads_Campaigns.idCamp WHERE status=1 AND idBooth=$APP_idBooth ORDER BY Ads_CampaignBooth.`idCamp`;";
//NOTA: en el futur s'enviarà un email d'informació del login
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
    echo "Error2 - code 03 $sql";
    return;
}
$nAdvs = 0;
$stream = ""; $ret = "";
while($APP_BdD->FetchRs()){
    $ret = "";
    $ret.= "#".$APP_BdD->GetField(1);
    $ret.= "#".$APP_BdD->GetField(2);
    $tmp = $APP_BdD->GetFieldDateTime(3);
    if($tmp){
     $ret.= "#".$tmp->format("Ymd");
    }
    else continue;
    $tmp = $APP_BdD->GetFieldDateTime(4);
    if($tmp){
     $ret.= "#".$tmp->format("Ymd");
    }
    else continue;
    
    $ret.= "#".$APP_BdD->GetField(5);
    $ret.= "#".$APP_BdD->GetField(6);
    $ret.= "#".$APP_BdD->GetField(7);
    $ret.= "#".$APP_BdD->GetField(8);
    $ret.= "#".$APP_BdD->GetField(9);
    $ret.= "#".$APP_BdD->GetField(10);
    $nAdvs++;
    $stream.=$ret;
}
$APP_BdD->CloseRs();

$dadesAcces = "ftp.myphotocode.com#u67519522-ads01#myads873";


echo "ok#$nAdvs$stream#$dadesAcces";
?>
