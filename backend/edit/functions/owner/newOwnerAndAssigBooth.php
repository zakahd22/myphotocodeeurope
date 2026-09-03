<?php

include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$t = false;
$CLD_CON2 = clone($CLD_CON);
$idBooth = $_POST['idBooth'];
$companyName = $_POST['companyName'];
$contactEmail = $_POST['email'];
$subDisID = $_POST['subDis'];
$Distributorid = $_POST['distributor'];
$createUserSendEmail = true;
$p_array = ['a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J', 'k', 'K', 'm', 'M'
    , 'n', 'N', 'p', 'P', 'q', 'Q', 'r', 'R', 't', 'T', 'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z'
    , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
$date = date("Y-m-d H:i:s");


$CLD_CON->OpenRs("SELECT serialnumber , owner , type FROM App_booths WHERE idBooth=$idBooth");
if ($CLD_CON->FetchArray()) {
    $oldOwner = $CLD_CON->GetArrayField("owner");
    $boothTYPE = $CLD_CON->GetArrayField("type");
    $sn = $CLD_CON->GetArrayField("serialnumber");
}

if (empty($contactEmail)) {
    $createUserSendEmail = false;
    $password = "-";
    $username = "-";
} else {
    $username = $contactEmail;
    $c = 0;
    $r = 0;
    while ($c < 10) {
        $r = rand(0, sizeof($p_array) - 1);
        $password .= $p_array[$r];
        $c++;
    }
}
if($_SESSION['USERTYPE'] == 3){
    $Distributorid = $_SESSION['USERID'];
}
        

if ($ownerNewID = $CLD_CON->ExecuteInsert("INSERT INTO rentals (code , name , username , password ,App_email , CLD_DistributorId) VALUES('-' , '$companyName' , '$username' , '$password' , '$contactEmail' , $Distributorid)")){
    if ($CLD_CON->Execute("UPDATE App_booths SET owner=$ownerNewID , CLD_date_tOwner='$date' , CLD_subDistributor=$subDisID , CLD_Status=3 WHERE idBooth=$idBooth")) {
        $CLD_CON->Execute("UPDATE CLD_components SET owner=$ownerNewID , data_owner='$date' , Status=3 WHERE booth=$idBooth");
        $CLD_CON->OpenRs("SELECT idDongle FROM App_boothDongle WHERE idBooth=$idBooth AND datetimeF IS NULL ORDER BY datetimeS DESC LIMIT 1");
        if ($CLD_CON->FetchArray()) {
            $dongleID = $CLD_CON->GetArrayField("idDongle");
            if ($CLD_CON2->Execute("UPDATE booths SET rental_id=$ownerNewID WHERE id=$dongleID AND rental_id=$oldOwner")) {
                $t = TRUE;
            }
        } else {
            $t = TRUE;
        }
        $coment2 = addslashes("Sold to $companyName");
        $CLD_CON2->ExecuteInsert("INSERT INTO CLD_historyBooth (comment , data , idBooth , sn) VALUES('$coment2' , '$date' , $idBooth ,'$sn');");
    } else {
        echo "ERROR , Cambiando el propietario de la màquina.";
    }
} else {
    echo "ERROR , Creando el propietario.";
//    echo "INSERT INTO rentals (code , name , username , password ,App_email , CLD_DistributorId) VALUES('-' , '$companyName' , '$username' , '$password' , '$contactEmail' , $Distributorid)";
}


if ($t) {
    $CLD_CON->OpenRs("SELECT serialnumber FROM CLD_components WHERE  booth=$idBooth");
    while ($CLD_CON->FetchArray()) {
        $serialnumber = $CLD_CON->GetArrayField("serialnumber");
        $coment2 = addslashes("Sold to $companyName into photobooth $sn");
        $CLD_CON2->Execute("INSERT INTO CLD_historyComponents (comment , data , component_sn) VALUES('$coment2' , '$date' , '$serialnumber');");
    }

    $fitxer = G_PATH . "common/resources/templates/html/en/welcome.html";
    
    if ($createUserSendEmail) {
        if ($CLD_CON->Execute("INSERT INTO CLD_Login (username , password , id_user , userType) VALUES('$username' , '$password' , $ownerNewID , 4 )")) {
            echo $ownerNewID;
            ob_start();
            if($boothTYPE=="A"){
                $fitxer = G_PATH . "common/resources/templates/html/en/welcome-strip.html";
            }else{
                $fitxer = G_PATH . "common/resources/templates/html/en/welcome.html";
            }
            
            $to = $contactEmail;
            $to_str = $companyName; //if(strlen($mail_nom)) 

            require_once(G_PATH . 'common/mail.php');
            try {
                $mail= new mail();
                $mail->addAdress($to, $to_str);
                $mail->setSubject("WELCOME TO DIGITAL CENTRE");
                $mail->setTemplate($fitxer);
                $us = $username;
                $ps = $password;
                $mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
                $mail->addTemplateField("#USERNAME#", $us);
                $mail->addTemplateField("#PASSWORD#", $ps);
                $mail->addTemplateField("#NAMECOMPANY#", $companyName);
                $mail->applyTempplateFields();

                if (!$mail->Send()) {
                    $mail_ret = 0;
                    echo "ERROR";
                    utils::log($mail->retMsg, "logMail", "newOwnerAndAssigBooth");
                } else {
                    $mail_ret = 1;
                }
            } catch (Exception $ex) {
                utils::log("Can not send message!", "logMail", "newOwnerAndAssigBooth");
            }
            

            $CLD_CON2->OpenRs("SELECT * FROM App_booths WHERE idBooth=$idBooth");
            if ($CLD_CON2->FetchArray()) {
                $type = $CLD_CON2->GetArrayField("CLD_idType");
                $nameP = $CLD_CON2->GetArrayField("name");
                $version = $CLD_CON2->GetArrayField("version");
                $serialnumber = $CLD_CON2->GetArrayField("serialnumber");
            }
            $typeName = "Comprobar per serial number";
            if (!empty($type)) {
                $CLD_CON2->OpenRs("SELECT name FROM CLD_boothTypes WHERE id = $type");
                if ($CLD_CON2->FetchArray()) {
                    $typeName = $CLD_CON2->GetArrayField("name");
                }
            }
            $CLD_CON2->OpenRs("SELECT x.rand_string FROM App_boothDongle y LEFT JOIN booths x ON x.id=y.idDongle WHERE y.idBooth=$idBooth AND datetimeF IS NULL");
            if ($CLD_CON2->FetchArray()) {
                $dongle = $CLD_CON2->GetArrayField("rand_string");
            }
            
        }


        $mail_retMsg = "";
        $mail_ret = 0;
        require_once(G_PATH . 'common/mail.php');
        try {
            $mail2= new mail();
            //$mail2->addAdress("marina@dc-image.com", "Marina");
            $mail2->addAdress("accounts@dc-image.com", "DC. Digital Centre. Francesc");
            $mail2->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
            //$mail2->addAdressBCC("accounts@dc-image.com", "DC. Digital Centre. Francesc");
            $mail2->setSubject("Informació nova venta Myphotocode");

            $mail2->setTemplate(G_PATH . "common/resources/templates/html/en/pb_assignPB.html");
            $mail2->addTemplateField("#user#",$us);
            $mail2->addTemplateField("#password#",$ps);
            $mail2->addTemplateField("#companyia#",$companyName);

            $mail2->addTemplateField("#text#", "");

            $mail2->addTemplateField("#serialnumber#",$serialnumber);
            $mail2->addTemplateField("#dongle#",$dongle);
            $mail2->addTemplateField("#typename#",$typeName);
            $mail2->addTemplateField("#PBname#",$nameP);
            $mail2->addTemplateField("#version#",$version);
            $mail2->applyTempplateFields();

            if (!$mail2->Send()) {
                $mail_ret = 0;
                utils::log($mail2->retMsg, "logMailer", "newOwnerAndAssigBooth");
                echo "ERROR";
            } else {
                $mail_ret = 1;
            }
        } catch (Exception $ex) {
            utils::log("Can not send message!", "logMail", "newOwnerAndAssigBooth");
        }
        
        ob_clean();
    } else {
        $ownerNewID;
    }
}
?>