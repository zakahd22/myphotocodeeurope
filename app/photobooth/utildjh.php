<?php

require("../common/APP_BdD.php");

$sql = "SELECT `when`,`idBooth`,`idDongle`, COUNT(*) AS n FROM `App_info`
GROUP BY `when`,`idBooth`,`idDongle` HAVING n>1 LIMIT 0,50";

$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
//caldria controlar l'error
echo "Error01 Database error $sql";
return;
}

$iRec = 0;
while($APP_BdD->FetchRs()){
    $array_when[$iRec] = $APP_BdD->GetField(1);
    $array_idb[$iRec] = $APP_BdD->GetField(2);
    $array_idd[$iRec] = $APP_BdD->GetField(3);

    $iRec++;
}
$APP_BdD->CloseRs();

$nRecs = $iRec;

for($iRec=0;$iRec<$nRecs;$iRec++){
    $sql= "SELECT idInfo FROM `App_info` WHERE `when`='$array_when[$iRec]' AND `idBooth`=$array_idb[$iRec] AND `idDongle`=$array_idd[$iRec] LIMIT 0,1;";
    
    $esOK = $APP_BdD->OpenRs($sql);
    if(!$esOK){
    //caldria controlar l'error
    echo "Error02 Database error $sql";
    return;
    }

    $array_primerID[$iRec] = "";
    if($APP_BdD->FetchRs()){
        $array_primerID[$iRec] = $APP_BdD->GetField(1);
    }
    $APP_BdD->CloseRs();
    
}

for($iRec=0;$iRec<$nRecs;$iRec++){
    if(strlen($array_primerID[$iRec])==0){
        echo "no promerID a $array_when[$iRec] <br/>\r\n";
        continue;
    }
    $sql = "DELETE FROM  `App_info` WHERE idInfo <> $array_primerID[$iRec] AND `when`='$array_when[$iRec]' AND `idBooth`=$array_idb[$iRec] AND `idDongle`=$array_idd[$iRec];";
    
    echo "$sql <br/>\r\n";
    
    $esOK = $APP_BdD->Execute($sql);
    if(!$esOK) {
    echo "Error $sql. <br/>\r\n";
    return;

}
    
}
?>
