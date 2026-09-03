<?php

/*
 * El PB envia informació d'una sessió AAAAMMDDHHMM i nombre de prints consumits
 * Crearem el registre i descontarem saldos de les comandes
 */
$APP_common_no_idb = true;


require("common.php");
if(!$APP_dongleOK) return;


if(!$APP_sg){echo "Error sg"; APP_fesLog("Error - PBprint_send, sg is empty"); return;}
if(!$APP_tact){echo "Error tact"; APP_fesLog("Error - PBprint_send, tact is empty");  return;}

// paràmetres
if(isset($_POST['sess'])){ $sess = $_POST['sess'];} else {echo "Error sess"; APP_fesLog("Error - PBprint_send, sess is empty");  return;}
if(isset($_POST['prints'])){ $PB_common_prints = $_POST['prints'];} else {echo "Error prints"; APP_fesLog("Error - PBprint_send, prints is empty");  return;}

//signatura:
$signature = strtoupper(sha1($sess.$PB_common_prints.$APP_dongle.$APP_tact.$APP_seccode));
if($signature != $APP_sg){
    APP_fesLog("Error - PBprint_send, sg error local: $signature url:$APP_sg");
    echo "Error - sg";
    return;
}

//
//SELECT `idDongle`, `startData`, `minStock`, `quantitat`, `preu` FROM `Pay_print_dongle`
//SELECT `idOrder`, `idDongle`, `idOwner`, `quantitat`, `preu`, `saldo`, `proposedDate`, `validadtedDate`, `paidDate`, `commissionDate` 
//FROM `Pay_print_order` WHERE 1
//SELECT `idDongle`, `startDate`, `prints` FROM `Pay_print_sessions` WHERE 1


//primer guardem la informació

$sql = "INSERT INTO `Pay_print_sessions` SET idDongle=$APP_idDongle, `startDate`=".$APP_BdD->myDateTimeSerialFull($sess."00");
$sql.= ", `prints`=$PB_common_prints;";


APP_fesLog("PBprint_send: idDongle=$APP_idDongle, `prints`=$PB_common_prints");

$esOK = $APP_BdD->Execute($sql);
if(!$esOK) {
    APP_fesLog("PBprint_send, Error00: $sql");
    echo "Error00";
    return;

}

require("common/PBprint_common_checkNewOrder.php");
if($PB_common_error) {
    echo $PB_common_errorStr;
    return;
}


echo "ok#";//res més

//20160119 FINAL

//anterior a 20160119 INICI

//ara miraré de restar $PB_common_prints al saldo de la comanda més antiga amb saldo > 0
//si encara queden prints, seguiré amb la següent comanda
//en cas de tenir prints sense comanda, es crearan les necessàries
//per si en necessitem crear registres 
//$hemCreatComandes = false;//el primer cop agafarem els valors per defecte 
//$whereValidated = "AND `validadtedDate` IS NOT NULL";//primer les validades
//while($PB_common_prints){
//
//    $sql = "SELECT idOrder,`saldo` FROM Pay_print_order WHERE idDongle=$APP_idDongle $whereValidated AND saldo > 0 ORDER BY `proposedDate`;";
//    $esOK = $APP_BdD->OpenRs($sql);
//    if(!$esOK){
//        APP_fesLog("Error - PBprint_send, Error01: $sql.");
//        echo "Error01";
//        return;
//    }
//    $nR = 0;
//    while($APP_BdD->FetchRs()){
//        $idOrders[$nR] = $APP_BdD->GetField(1);
//        $saldos[$nR] = $APP_BdD->GetField(2);
//
//        $PB_common_prints-= $PB_common_saldo;
//        if($PB_common_prints>=0){
//            $nousSaldos[$nR] = 0;
//        }
//        else{
//            $nousSaldos[$nR] = -$PB_common_prints;
//            $PB_common_prints = 0;
//        }
//        $nR++;
//        if( $PB_common_prints == 0) break;
//    }
//    $APP_BdD->CloseRs();
//    for($i=0;$i<$nR;$i++){
//        $sql="UPDATE Pay_print_order SET saldo=0 WHERE idDongle=$APP_idDongle AND idOrder={$idOrders[$i]} ;";
//        $esOK = $APP_BdD->Execute($sql);
//        if(!$esOK) {
//            APP_fesLog("PBprint_send, error sql: $sql");
//            echo "Error - Database update: $sql.";
//            return;
//        }
//    }
//
//    if($PB_common_prints>0){//encara en queden. No hauria de passsar mai
//
//        if($nR){ //miraré si hi ha comandes amb saldo però encara no validades
//            $whereValidated = "";
//            continue;
//        }
//        else{
//        //si estic aqui és que encara en queden i no hi ha cap comanda amb saldo, encara que no estigui validada. No hauria de passsar mai
//        //faré un insert
//            
//            if(!$hemCreatComandes){
//                $sql = "SELECT `minStock`, `quantitat`, `preu` FROM `Pay_print_dongle` WHERE idDongle=$APP_idDongle;";
//                $esOK = $APP_BdD->OpenRs($sql);
//                if(!$esOK){
//                    APP_fesLog("Error - PBprint_send, Error: $sql.");
//                    echo "Error - Database select Pay_print_dongle";
//                    return;
//                }
//                if($APP_BdD->FetchRs()){
//                    $camp = 1;
//                    $PB_common_minStock = $APP_BdD->GetField($camp);$camp++;
//                    $quantitat = $APP_BdD->GetField($camp);$camp++;
//                    $preu = $APP_BdD->GetField($camp);$camp++;
//                }
//                $APP_BdD->CloseRs();                
//                $hemCreatComandes = true;
//            }
//            $sql = "INSERT INTO `Pay_print_order` SET idDongle=$APP_idDongle, idOwner=$APP_idRental, 
//                ,`quantitat`=$quantitat,`preu`=$preu,`saldo`=$quantitat,`proposedDate`=$APP_araTimeSerial";
//
//            APP_fesLog("PBprint_send: $sql");
//
//            $esOK = $APP_BdD->Execute($sql);
//            if(!$esOK) {
//                APP_fesLog("PBprint_send, error sql: $sql");
//                echo "Error - Database insert: $sql.";
//                return;
//
//            }
//            //no sortim del bucle i es trobarà aquesta comanda
//
//        }
//
//    }
//}
//
//echo "ok#";//res més
//
//anterior a 20160119 FINAL

?>
