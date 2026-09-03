<?php
/*
 * Versió 8.0 28/09/2015
Mètode: checko.php. per a demanar informació de login d'owner, paràmetres:
u	cadena alfanumèrica	username `rentals`.`username` varchar(50)
retornarà
JSON:
·	 'id' != 0 si existeix  BdD  `CLD_Loginrentals`.`id_user` bigint(4)
·	,'name' BdD  `rentals`.`name` varchar(100) o CLD_Distributors.Name  varchar(30)
·	,'email'  `rentals`.`App_email` varchar(50) (només si és owner)
·	'c' password (és perillós ja que passa sense codificar!!!!!!)  `CLD_Loginrentals`.`passwordcode` varchar(1020)
·	'userType'  (3 o 4)  BdD `CLD_Login`.`userType`
·	'owners' en cas de ser distribudor: string del ids d'owner associats 'rentals'.'id' separts per ','


*/

require("common.php"); 

//resposta
$resposta['status'] = 0;
$resposta['id'] = 0;
$resposta['userType'] = 0;
$resposta['name'] = ""; 
$resposta['email'] = ""; 
$resposta['c'] = ""; 
$resposta['owners'] = ""; 


//$MTR_ok = true;      //PROVES ***************************************************************************

if(!$MTR_ok){
//    echo "ko#$MTR_status";
//no cal    $resposta['status'] = 0;
    $resposta['statusStr'] = $MTR_status;
    echo json_encode($resposta);
    return;
}

//paràmetres específics
if(isset($_REQUEST['u'])){ $username = $_REQUEST['u'];} 
else { 
    fesLog("Error - checko, missing u - Error01");
   // echo "ko#Error01";
    $resposta['statusStr'] = "Error01";
    echo json_encode($resposta);
    return;
}
//el control de la signatura s'hauria de canviar per una IP?????
//control de signatura
$signature = strtoupper(sha1($username.$MTR_tact.$MTR_MtrControl));
if($signature != $MTR_sg){
    fesLog("Error - checko, sg error local: $signature url:$MTR_sg  - Error02");
    $resposta['statusStr'] = "Error02";
    echo json_encode($resposta);
    return;
}

//20150122 INICI

//old code INICI
//cal llegir les dades de l'owner
//$sql = "SELECT `id`,`name`,`App_email`, `password` FROM rentals WHERE `username`='$username'; ";
//$esOK = $APP_BdD->OpenRs($sql);
//if(!$esOK){
//    fesLog("Error - checko, Error03_sql: $sql.");
//    $resposta['statusStr'] = "Error03_sql";
//    echo json_encode($resposta);
//    return;
//}
//    fesLog("TRACE - checko, sql: $sql.");
//
//if($APP_BdD->FetchRs()){
//    $resposta['id'] = $APP_BdD->GetField(1);
//    $resposta['name'] = utf8_encode($APP_BdD->GetField(2)); 
//    $resposta['email'] = utf8_encode($APP_BdD->GetField(3)); 
//    $resposta['c'] = utf8_encode($APP_BdD->GetField(4)); 
//}
//else{
//    $resposta['id'] = 0;
//    $resposta['name'] = ""; 
//    $resposta['email'] = ""; 
//    $resposta['c'] = ""; 
//}
//$APP_BdD->CloseRs();
//    
//old code FINAL
    
//Dades de SELECT `username`, `password`, `id_user`, `userType` FROM `CLD_Login`
// SELECT `id`, `code`, `Name`, `LOCATION`, `LATITUDE`, `LONGITUDE` FROM `CLD_Distributors` WHERE 1
// SELECT `id`, `code`, `name`, `username`, `password`, `App_email`, `CLD_DistributorId` FROM `rentals` WHERE 1
    
$okUser = false;
$sql = "SELECT `id_user`,`userType`,`password` FROM `CLD_Login` WHERE `username`='$username';";
$esOK = $APP_BdD->OpenRs($sql);
if(!$esOK){
    fesLog("Error - checko, Error03_sql: $sql.");
    $resposta['statusStr'] = "Error03_sql";
    echo json_encode($resposta);
    return;
}
if($APP_BdD->FetchRs()){
    $camp = 1;
    $resposta['id'] = $APP_BdD->GetField($camp); $camp++;
    $resposta['userType'] = $APP_BdD->GetField($camp); $camp++;
    $resposta['c'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
//    
//    $resposta['name'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
//    $resposta['email'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
    
    $okUser = true;
}
$APP_BdD->CloseRs();

if(!$okUser){
    $resposta['status'] = 1;
    echo json_encode($resposta);
    return;
}

switch($resposta['userType']){
    case 3://distributor
        //SELECT `id`, `code`, `Name`, `LOCATION`, `LATITUDE`, `LONGITUDE` FROM `CLD_Distributors` WHERE 1
        //SELECT `id`, `code`, `name`, `username`, `password`, `App_email`, `CLD_DistributorId` FROM `rentals` WHERE 1

        $sql = "SELECT `Name` FROM CLD_Distributors WHERE `id`={$resposta['id']}; ";
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
            fesLog("Error - checko, Error04_sql: $sql.");
            $resposta['statusStr'] = "Error04_sql";
            echo json_encode($resposta);
            return;
        }
        fesLog("TRACE - checko is distributor, sql: $sql.");

        if($APP_BdD->FetchRs()){
            $camp = 1;
            $resposta['name'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
        }
        else{
            $okUser = false;
        }
        $APP_BdD->CloseRs();
        
        
        $llistaOwners = ""; $nOwners = 0;
        $sql = "SELECT id FROM rentals WHERE `CLD_DistributorId`={$resposta['id']}; ";
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
            fesLog("Error - checko, Error05_sql: $sql.");
            $resposta['statusStr'] = "Error05_sql";
            echo json_encode($resposta);
            return;
        }
        fesLog("TRACE - checko is distributor, sql: $sql.");
        
        while($APP_BdD->FetchRs()){
            $camp = 1;
            $idOwner = $APP_BdD->GetField($camp); $camp++;
            if($nOwners){
                $resposta['owners'].= ",$idOwner"; 
            }
            else{
                $resposta['owners'] = "$idOwner"; 
            }
            $nOwners++;
        }
        $APP_BdD->CloseRs();
        
        
        
        break;
    case 4://owner
        
        $sql = "SELECT `name`,`App_email`, `ValidatedAlertEmail` FROM rentals WHERE `id`={$resposta['id']}; ";
        $esOK = $APP_BdD->OpenRs($sql);
        if(!$esOK){
            fesLog("Error - checko, Error06_sql: $sql.");
            $resposta['statusStr'] = "Error06_sql";
            echo json_encode($resposta);
            return;
        }
        fesLog("TRACE - checko is owner, sql: $sql.");

        if($APP_BdD->FetchRs()){
            $camp = 1;
            $resposta['name'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
            $resposta['email'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
            $resposta['email_val'] = utf8_encode($APP_BdD->GetField($camp)); $camp++;
        }
        else{
            $okUser = false;
        }
        $APP_BdD->CloseRs();
        if(!$okUser){
            $resposta['status'] = 1;
            echo json_encode($resposta);
            return;
        }
        
        break;
    default:
        $resposta['status'] = 1;
        echo json_encode($resposta);
        return;
}

//20150122 FINAL


//resposta
$resposta['status'] = 1;
echo json_encode($resposta);

?>
