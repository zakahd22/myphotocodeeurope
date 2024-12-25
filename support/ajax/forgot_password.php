<?php
require_once "../../common/global.php";
require_once G_PATH . 'common/conexio.php';

$username = $_REQUEST['username'];
$CLD_CON2 = clone($CLD_CON);
$CLD_CON->OpenRs("SELECT  username , userType , id_user FROM CLD_Login WHERE username='$username'");
//mirem si existeix el usuari 
if ($CLD_CON->FetchArray()) {
    $types = array("", "SUPERUSUARI", "MANUFACTURER", "DISTRIBUTOR", "OWNER", "EVENT MANAGER", "USER");
//el usuari SI existeix 
    //Agafem Dades
    $x= false;
    $iType = $CLD_CON->GetArrayField("userType");
    $userType = $types[$iType];
    $userID = $CLD_CON->GetArrayField("id_user");
    
    
    $CLD_CON2->OpenRs("SELECT name, App_email FROM rentals WHERE id = $userID");
    $App_email = "";
    if($CLD_CON2->FetchArray()){
    $App_email = $CLD_CON2->GetArrayField("App_email");
    $name = $CLD_CON2->GetArrayField("name");
    }
    //Aqui Si la variable APP_EMAIL te un emais s'enviarà aqui si no senviara al user name sempre i quan contingui un emai també.
    if (filter_var($username, FILTER_VALIDATE_EMAIL)) {
        $x= true;
        $email = $username;
    }
    if(filter_var($App_email, FILTER_VALIDATE_EMAIL)){
        $x = true;
        $email = $App_email;
    }
    
    //Comprobem que el nom de usari sigui o no un email
    if ($x) {//es un email
        //Creem un codi de seguretat que enviarem com a parametre dins de un link.
        $code = generateRandomString() . date("Y-m-d H:i:s");
        $codeX = sha1($code);
        //Agafem la data em dos dies mes. Que seran els dies que el codi anterior tindrà validesa.
        $d = date("m-d-Y");
        $finish_date = date("Y-m-d H:i:s", strtotime("$DATE +2 days"));
        $finish_date2 = date("m-d-Y", strtotime("$DATE +2 days"));
        //Creem la URL
        $ok = $CLD_CON->Execute("INSERT INTO CLD_forgot_pws (user ,userType , code , datef) VALUES($userID , $iType , '$codeX' , '$finish_date')");
        if ($ok) {
            $urlLink = "https://www.myphotocode.com/restart_password.php?c=$codeX";
            //SEND EMAIL
            
            $to = $email;
            $to_str = "$username";
            
            $mail = new mail();

            $mail->addAdress($to, $to_str);
            //$mail->addAdressBCC("alex@dc-image.com", "Alex");

            $mail->setSubject("MyPhotoCode password reset");

            $mail->setTemplate(G_PATH . "common/resources/templates/html/en/forgotPassword.html");
            
            $mail->addTemplateField("##USERNAME##", $name);
            $mail->addTemplateField("##DATE##", $finish_date2);
            $mail->addTemplateField("##URL##", $urlLink);
//            $mail->addTemplateField("##ticket##", G_PATH . "common/resources/templates/html/en/support.html");
            
            $mail->applyTempplateFields();            
            
            if (!$mail->send()) {
                echo "Error sending mail, please try again";
                utils::log($mail->retMsg, "logMailer", "forgot_password");
            } else {
                
                $subcadena = "@"; 
                $posicionsubcadena = strpos($email, $subcadena); 
                $l = strlen($email);
                $l = $l -$posicionsubcadena+1;
                $x=0;
                $end = "";
                while($x<$l){
                    $end .= "*";
                    $x++;
                }
                $e = substr($email , 0 , $posicionsubcadena+1) . $end; 
                
                echo "An email have been sent to $e with instructions to reset your password.</br>"
                        . "If you do not receive the password reset email within a few moments, please check your spam folder or other filtering tools.";
            }
          //  $mail_retMsg = ob_get_contents();
           // ob_end_clean();

            //SEND EMAIL
        } else {
            echo "An error has occurred, please try again.";
        }
    } else {//No ho es
        echo "#Error:" . "This username has no email associated with it. Please send an email with your username and your email address to myphotocode@dc-image.com.";
    }
} else {
//El usuari no existeix
    echo "#Error:" . "The username you entered does not exist, please check your username and re-enter";
}

//funcions
function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, strlen($characters) - 1)];
    }
    return $randomString;
}
?>
