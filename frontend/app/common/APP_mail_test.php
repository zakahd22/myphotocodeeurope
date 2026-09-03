<?php
/* 
Aqui concentrarem l'enviament de correu, variables:
 * $mail_quinMail: NO
 * $mail_email: mail destinatari
 * $mail_nom: nom destinatari
 * $mail_cont: contingut a afegir al missatge
 * $mail_ret: codi de retorn 1: ok   0:ko
 * 
 */
 
 

if(!isset ($mail_email)) return;
if(!isset ($mail_nom)) $mail_nom = "";
if(!isset ($mail_cont)) $mail_cont = "";
if(!isset ($mail_copianom)) $mail_copianom = "";
if(!isset ($mail_subject)) $mail_subject = "Message from myphotocode";


 

	$subject = $mail_subject;
	$str_missatge = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
	'https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
	<html xmlns='https://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>Consulta general</title>
	</head>
	<body>
	$mail_cont
	</body>
	</html>";

    //dades d'enviament, remitents, etc.
//*********************************************************************************************************
    $to  = $mail_email;
	$to_str = $mail_nom;//if(strlen($mail_nom)) 

//	$from = "noreply@myphotocode.com";
//	$from_str = "noreply";
//	$host = "smtp.1and1.com";
////	$username = "noreplay@myphotocode.com";
//	$username = "noreply@myphotocode.com";
//	$password = "DC12345";
    
	$from = "victor@dc-image.com";
	$from_str = "noreply";
	$host = "smtp.altecom.com";
//	$host = "212.121.224.17";
//	$username = "noreplay@myphotocode.com";
	$username = "victor@dc-image.com";
	$password = "mega6x16";
        
        
//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
        $mail_retMsg = "";
        $mail_ret = 0;
 //test!       ob_start();
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        
        
	require_once('class.phpmailer.php');

	$mail= new PHPMailer();
	
	$mail->CharSet="utf-8";

	$body = $str_missatge;
//	$mail->PluginDir = "common/";
	$mail->PluginDir = "";
//?	$mail->IsSMTP(); // telling the class to use SMTP
	$mail->Host = $host;
	$mail->SMTPAuth = true;   // enable SMTP authentication
	
	//$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
	//$mail->Host  = $host; // sets the SMTP server
	
	$mail->Port = 26; // set the SMTP port for the GMAIL server
	$mail->Username = $username; // SMTP account username
	$mail->Password  = $password; // SMTP account password
	$mail->SetFrom($from, $from_str);
	$mail->Timeout=30;
	$mail->ClearReplyTos();

//	$mail->AddReplyTo($replayto, "");

	$mail->Subject = $subject;


	$mail->AddAddress($to, $to_str);
        
	if(isset ($mail_copia)) if(strlen($mail_copia)>0)  $mail->AddBCC($mail_copia,$mail_copianom);

        $mail->AddBCC("victor@dc-image.com");

	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";

	$mail->MsgHTML($str_missatge);

	if(!$mail->Send()) {
 		 $mail_ret = 0;
	}
	else {
  		$mail_ret = 1;
	}

        
//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml    
 //test!        $mail_retMsg = ob_get_contents();
 //test!        ob_end_clean();
        if($mail_retMsg) $mail_ret = 0; //  $mail->Send() no és fiable
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        
        

?>
