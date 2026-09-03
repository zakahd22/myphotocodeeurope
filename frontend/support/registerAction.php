<?php

$companyName = $_POST['name'];
$address = $_POST['address'];
$num = $_POST['num'];
$zip = $_POST['zip'];
$city = $_POST['city'];
$state = $_POST['state'];
$country = $_POST['country'];
$oName = $_POST['oName'];
$lastName = $_POST['lastName'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$model = $_POST['model'];
$sn = $_POST['sn'];

$SUBJECT = "REGISTRE AL SAT";
//$MESSAGE = "<h2>Company</h2>";
//$MESSAGE .= "<p>Company Name : $companyName </p>";
//$MESSAGE .= "<p>Carrer : $address</p>";
//$MESSAGE .= "<p>Numero : $num </p>";
//$MESSAGE .= "<p>ZIP : $zip</p>";
//$MESSAGE .= "<p>City : $city</p>";
//$MESSAGE .= "<p>State : $state</p>";
//$MESSAGE .= "<p>Country : $country</p>";
//$MESSAGE .= "<br>";
//$MESSAGE .= "<h2>Persona de  Contacte</h2>";
//$MESSAGE .= "<p>$oName $lastName</p>";
//$MESSAGE .= "<p>E-mail : $email</p>";
//$MESSAGE .= "<p>Phone : $phone </p>";
//$MESSAGE .= "<br>";
//$MESSAGE .= "<h2>PhotoBooth</h2>";
//$MESSAGE .= "<p>Model : $model</p>";
//$MESSAGE .= "<p>Serialnumber : $sn</p>";

$to = "joan@dc-image.com";
$to_str = "joan@dc-image.com";

// ob_start();

require_once(G_PATH . 'common/mail.php');

$mail = new mail();
$mail->addAdress($to, $to_str);
$mail->setSubject($SUBJECT);

//$mail->setBody($MESSAGE);

$mail->setTemplate(G_PATH . "common/resources/templates/html/en/support_registerAction.html");
$mail->addTemplateField("#companyName#",$companyName);
$mail->addTemplateField("#address#",$address);
$mail->addTemplateField("#num#",$num);
$mail->addTemplateField("#zip#", $zip);
$mail->addTemplateField("#city#",$city);
$mail->addTemplateField("#state#",$state);
$mail->addTemplateField("#country#",$country);
$mail->addTemplateField("#oName#",$oName);
$mail->addTemplateField("#lastName#",$lastName);
$mail->addTemplateField("#email#",$email);
$mail->addTemplateField("#phone#",$phone);
$mail->addTemplateField("#model#",$model);
$mail->addTemplateField("#sn#",$sn);
$mail->applyTempplateFields();

$mail->addAdressBCC("mon@dc-image.com", "Montserrat Canales");

if (!$mail->Send()) {
    echo "ERROR : please try again";
    utils::log($mail->retMsg, "logMail", "registerAction");
} else {
     echo "OK";
 
}
$resultado = file_get_contents("http://www.digital-centre.com/emailsMasius/addEmail.php?mail=$email");

?>
