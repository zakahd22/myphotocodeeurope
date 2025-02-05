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
 

error_log( "common/APP_mail.php" );
 

if(!isset ($mail_email)) return;
if(!isset ($mail_nom)) $mail_nom = "";
if(!isset ($mail_cont)) $mail_cont = "";
if(!isset ($mail_copianom)) $mail_copianom = "";
if(!isset ($mail_copianom1)) $mail_copianom1 = "";//20140405
if(!isset ($mail_copianom2)) $mail_copianom2 = "";//20140329
if(!isset ($mail_copianom3)) $mail_copianom3 = "";//20140329
if(!isset ($mail_subject)) $mail_subject = "Message from myphotocode";


//20140519disclaimer
if(!isset ($mail_disclaimer)) $mail_disclaimer ="
  <p><U>DISCLAIMER:</U><br/>
  The information contained in this electronic message is privileged and/or confidential and is intended only for the use of the individual or entity named above. If you are not the intended recipient, or if you are responsible for delivering it to the intended recipient, you are hereby notified that any dissemination, distribution or copying of the communication is not authorized, allowed or intended by the sender. If you have received this communication in error, please immediately notify us by telephone at the above number and forward the original message to the sender above. Thank you.</p>
";

require_once("../../common/config/params.php");
//20140329...
	$from = $mail_noreply['smtp_user'];
	$from_str = $mail_noreply['smtp_user'];
	$host = $mail_noreply['host'];
        $hostPort = $mail_noreply['port'];
	$username = $mail_noreply['smtp_user'];
	$password = $mail_noreply['smtp_pass'];
//        $replayto = "";

if(isset ($mail_remitent)) $from = $mail_remitent;
if(isset ($mail_nomremitent)) $from_str = $mail_nomremitent;
if(isset ($mail_hostremitent)) $host = $mail_hostremitent;
if(isset ($mail_usrremitent)) $username = $mail_usrremitent;
if(isset ($mail_pswremitent)) $password = $mail_pswremitent;
//if(isset ($mail_replayto)) $replayto = $mail_replayto;
 
        
	$subject = $mail_subject;
	$str_missatge = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN'
	'https://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
	<html xmlns='https://www.w3.org/1999/xhtml'>
	<head>
	<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
	<title>-</title>
	</head>
	<body>
	$mail_cont
        <br/>   $mail_disclaimer
	</body>
	</html>";

    //dades d'enviament, remitents, etc.
//*********************************************************************************************************
    $to  = $mail_email;
	$to_str = $mail_nom;//if(strlen($mail_nom)) 

//	$from = "noreply@myphotocode.com";
//	$from_str = "noreply";
//	$host = "smtp.1and1.com";
//            //	$username = "noreplay@myphotocode.com";
//	$username = "noreply@myphotocode.com";
//	$password = "DC12345";
        
//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml        
        $mail_retMsg = "";
        $mail_ret = 0;
        ob_start();
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        
        
	require_once('class.phpmailer.php');

	$mail= new PHPMailer();
	
	$mail->CharSet = "UTF-8";

	$body = $str_missatge;
//	$mail->PluginDir = "common/";
	$mail->PluginDir = "";
//	$mail->IsMail(); // telling the class to use SMTP
	$mail->IsSMTP();
//	$mail->Host = $host;
//20250203mail	$mail->Host = 'smtp.ionos.com';
	$mail->Host = 'smtp.ionos.es';//20250203mail
	$mail->SMTPAuth = true;   // enable SMTP authentication
	$mail->SMTPSecure = 'ssl';
	
	//$mail->SMTPKeepAlive = true; // SMTP connection will not close after each email sent
	//$mail->Host  = $host; // sets the SMTP server
	
	//$mail->Port = 25; // Port de server 1and1
//	$mail->Port = $hostPort; // set the SMTP port for the server
	$mail->Port = '465';	
//20140329	$mail->Port = 26; // set the SMTP port for the  server
	$mail->Username = $username; // SMTP account username
	$mail->Password  = $password; // SMTP account password
//	$mail->SetFrom($from, $from_str);
	$mail->SetFrom('noreply@myphotocode.com', 'DC Report Platform');
	$mail->Timeout=30;
	$mail->ClearReplyTos();

//	$mail->AddReplyTo($replayto, "");
	if(isset ($mail_replayto)) if(strlen($mail_replayto)>0)  $mail->AddReplyTo($mail_replayto,"");//20140329

	$mail->Subject = $subject;


	$mail->AddAddress($to, $to_str);
        
	if(isset ($mail_copia1)) if(strlen($mail_copia1)>0)  $mail->AddCC($mail_copia1,$mail_copianom1);//20140329
	if(isset ($mail_copia2)) if(strlen($mail_copia2)>0)  $mail->AddCC($mail_copia2,$mail_copianom2);//20140329
	if(isset ($mail_copia3)) if(strlen($mail_copia3)>0)  $mail->AddCC($mail_copia3,$mail_copianom3);//20140329
        
	if(isset ($mail_copia)) if(strlen($mail_copia)>0)  $mail->AddBCC($mail_copia,$mail_copianom);

//20150713        $mail->AddBCC("victor@dc-image.com");//a eliminar més endavant!!!!!

	$mail->AltBody = "To view the message, please use an HTML compatible email viewer!";

	$mail->MsgHTML($str_missatge);

	if(!$mail->Send()) {
 		 $mail_ret = 0;
	}
	else {
  		$mail_ret = 1;
	}

        
//20130621 INICI intentem evitar missatges d'error, ja que volem tornar un xml    
        $mail_retMsg = ob_get_contents();
        ob_end_clean();
        if($mail_retMsg) $mail_ret = 0; //  $mail->Send() no és fiable
//20130621 FINAL intentem evitar missatges d'error, ja que volem tornar un xml        
        

?>
