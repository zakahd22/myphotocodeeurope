<?php
include '../../../sessio.php';
require_once G_PATH . 'common/conexio.php';
$sendWelcomeEmail = true;
$ID = $_POST['own'];
$p_array = ['a', 'A', 'b', 'B', 'c', 'C', 'd', 'D', 'e', 'E', 'f', 'F', 'g', 'G', 'h', 'H', 'i', 'I', 'j', 'J', 'k', 'K', 'm', 'M'
    , 'n', 'N', 'p', 'P', 'q', 'Q', 'r', 'R', 't', 'T', 'u', 'U', 'v', 'V', 'w', 'W', 'x', 'X', 'y', 'Y', 'z', 'Z'
    , '1', '2', '3', '4', '5', '6', '7', '8', '9', '0'];
$replace = [" " , "\'" , "\"" , "/" , "\\" , "\$" , "#" , "|" , "!" , "?" , "[" , "]" , "`", "´" , "(" , ")" , "¿" , "¡" , "º" ,"ª" ,"|", "~" , "<" ,">" , "ñ" , "Ñ" , "ç" , "Ç"];
$replaceto = ["" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "" , "", "" , "" , "" , "" , "" , "" ,"" ,"", "" , "" ,"" , "" , "" , "" , ""];
$CLD_CON->OpenRs("SELECT name, App_email FROM rentals WHERE id=$ID");
$contact_email = "";
if($CLD_CON->FetchArray()){
    $onwer_email = $CLD_CON->GetArrayField("App_email");
    $owner_name = $CLD_CON->GetArrayField("name");
    if(!empty($contact_email)){
        $username = $owner_email;
    }else{
        $sendWelcomeEmail=0;
        $owner_name= trim($owner_name);
        $owner_name = str_replace($replace,$replaceto, $owner_name);
        $owner_name = strtolower($owner_name);
        if(strlen($owner_name) > 5){
               $username = substr($owner_name, 0 , 6);
        }else{
             $username = $owner_name;
        }
    }
}
$c = 0;
    $r = 0;
    while ($c < 10) {
        $r = rand(0, sizeof($p_array) - 1);
        $password .= $p_array[$r];
        $c++;
    }

$CLD_CON->Execute("UPDATE rentals SET username='$username' , password='$password' WHERE id=$ID");
$CLD_CON->ExecuteInsert("INSERT INTO CLD_Login (username , password , id_user , userType) VALUES('$username','$password', $ID, 4)");

  if($sendWelcomeEmail){
        ob_start();
        require_once(G_PATH . 'common/mail.php');

        $mail= new mail();
        $mail->addAdress($onwer_email, $username);
        $mail->setSubject("WELCOME TO DIGITAL CENTRE");
        $mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");
        $mail->setTemplate(G_PATH . "common/resources/templates/html/en/welcome.html");
        $mail->addTemplateField("#USERNAME#", $username);
        $mail->addTemplateField("#PASSWORD#", $password);
        $mail->addTemplateField("#NAMECOMPANY#", $ownerCompanyName);
        $mail->applyTempplateFields();

        if(!$mail->send()){
            $mail_ret = 0;
            $result = false;
            utils::log($mail->retMsg, "logMailWelcomes", "WelcomesEmail");
        }
        else {
            $mail_ret = 1;
        }
        
        ob_end_flush();
        echo "OK";
  }else{
      echo "OK";
  }

?>