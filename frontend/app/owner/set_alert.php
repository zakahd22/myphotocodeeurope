<?php
require("common.php");
if(!$APP_user) return;

//definicions d'alertes per a un PhotoBooth a editar
//info de les condicions d'alerta no editables des de App

if(isset($_REQUEST['id'])){ $idBooth = $_REQUEST['id'];}
else{
echo "$APP_xml<status>$APPERROR_noid</status></return>";
return;
}
$xml.= "<status>OK</status>";
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
                echo "$APP_xml<status>Error - Database error code: 0002 $sql</status></return>";
                return;
            }
        }
        
        
    }
}

echo "$APP_xml$xml</return>"; // no cal res més


//////Merda, no van els subqueries en  els JOIN FINAL


?>
