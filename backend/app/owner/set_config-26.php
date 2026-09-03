<?php
require("common.php");
if(!$APP_user) return;

//alertes editades per a un PhotoBooth

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<comm_status>$APPERROR_noid</comm_status></return>";
return;
}


$xml = $APP_xmlOKcomm;
for($i=11;$i<21;$i++){ //de moment, lloc per a 10
    $elParam = "alert$i";
    if(isset($_REQUEST[$elParam])){ 
        $value = $_REQUEST[$elParam];
        //provem INSERT
        $sql = "INSERT INTO `App_boothAlertDef` (idBooth,`typeAlert`,`value`) VALUES ($idBooth,$i,$value); ";
//            $xml.= "<trace>$sql</trace>";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            //caldria controlar l'error
//            $xml.= "<trace>$APP_BdD->errno</trace>";
//            $xml.= "<trace>$APP_BdD->error</trace>";
            if($APP_BdD->errno == 1062){
                $sql = "UPDATE `App_boothAlertDef` SET  `value`=$value WHERE idBooth=$idBooth AND `typeAlert`=$i; ";
                $esOK = $APP_BdD->Execute($sql);
            }
            if(!$esOK){
                echo "$APP_xml<comm_status>Error Database error code: 0002 </comm_status></return>";
                return;
            }
        }
        
        
    }
}

//20120926 INICI afegim altres configs

$xml = $APP_xmlOKcomm;
for($i=1;$i<11;$i++){ //de moment, lloc per a 10
    $elParam = "config$i";
    if(isset($_REQUEST[$elParam])){
        $value = $_REQUEST[$elParam];
        //provem INSERT
        $sql = "INSERT INTO `App_boothConfigDef` (idBooth,`typeConfig`,`value`) VALUES ($idBooth,$i,$value); ";
//            $xml.= "<trace>$sql</trace>";
        $esOK = $APP_BdD->Execute($sql);
        if(!$esOK){
            //caldria controlar l'error
//            $xml.= "<trace>$APP_BdD->errno</trace>";
//            $xml.= "<trace>$APP_BdD->error</trace>";
            if($APP_BdD->errno == 1062){
                $sql = "UPDATE `App_boothConfigDef` SET  `value`=$value WHERE idBooth=$idBooth AND `typeConfig`=$i; ";
                $esOK = $APP_BdD->Execute($sql);
            }
            if(!$esOK){
                echo "$APP_xml<comm_status>Error Database error code: 0002bis </comm_status></return>";
                return;
            }
        }


    }
}

//20120926 FINAL afegim altres configs



echo "$APP_xml$xml</return>"; // no cal res més


//////Merda, no van els subqueries en  els JOIN FINAL


?>
